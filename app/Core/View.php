<?php
/**
 * View — basit Blade-lite template engine.
 *
 * v2.0 KRİTİK BUG FIX (bug 1):
 *   Önceki versiyonda @include compile pattern'i `get_defined_vars()` ile
 *   internal state'i ($compiled, $content, $view) partial'a sızdırıyordu;
 *   `extract($data)` da bunları over-write edip layout'un compiled'ını
 *   tekrar eval'liyor, sonsuz döngü oluşuyordu.
 *
 *   Fix:
 *     1) Tüm internal değişkenler `__view_*` prefix'i ile yeniden adlandırıldı
 *        — kullanıcı $data'sıyla çakışmaz.
 *     2) `extract($__view_data, EXTR_SKIP)` kullanılıyor — varolan local'lere
 *        dokunmaz (defansif ikinci kat).
 *     3) `@include`/`@extends` artık scope-isolated bir render fonksiyonuna
 *        gider; partial'lar layout'un compiled state'ini göremez.
 *
 * Desteklenen direktifler:
 *   {{ $var }}            (escape'li echo)
 *   {!! $var !!}          (raw echo)
 *   @if / @elseif / @else / @endif
 *   @isset / @endisset / @unless / @endunless
 *   @foreach / @endforeach
 *   @for / @endfor
 *   @while / @endwhile
 *   @php / @endphp
 *   @extends('layouts.app')
 *   @section('x') ... @endsection
 *   @yield('x')
 *   @include('partials.header')
 *   @include('partials.header', ['key' => 'value'])
 *   @continue / @break
 *   @csrf  (CSRF input field shortcut)
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class View
{
    protected static string $viewPath    = VIEW_PATH;
    protected static string $cachePath   = STORAGE_PATH . '/cache/views';
    protected static array  $sections    = [];
    protected static ?string $layout     = null;
    protected static int    $renderDepth = 0;

    /**
     * View render et.
     */
    public static function render(string $__view_name, array $__view_data = []): string
    {
        self::$renderDepth++;

        $__view_file = self::$viewPath . '/' . str_replace('.', '/', $__view_name) . '.php';
        if (!is_file($__view_file)) {
            self::$renderDepth--;
            throw new \RuntimeException("View bulunamadı: [$__view_name] → $__view_file");
        }

        $__view_compiled = self::compile(file_get_contents($__view_file) ?: '');

        // Kullanıcı değişkenlerini local scope'a aktar — internal __view_*
        // değişkenlerine dokunmasın diye EXTR_SKIP (defansif ikinci kat).
        extract($__view_data, EXTR_SKIP);

        // Section'lara erişim için reference
        $__view_sections = &self::$sections;
        // @yield için fallback olarak child içerik
        $childContent    = '';

        ob_start();
        try {
            eval('?>' . $__view_compiled);
        } catch (\Throwable $e) {
            ob_end_clean();
            self::$renderDepth--;
            throw $e;
        }
        $childContent = ob_get_clean() ?: '';

        // @extends ile layout set edilmişse, layout'u render et
        if (self::$layout !== null) {
            $__layout = self::$layout;
            self::$layout = null;
            $output = self::render($__layout, array_merge($__view_data, ['childContent' => $childContent]));
            self::$renderDepth--;
            if (self::$renderDepth === 0) self::$sections = [];
            return $output;
        }

        self::$renderDepth--;
        if (self::$renderDepth === 0) self::$sections = [];
        return $childContent;
    }

    public static function setLayout(string $layout): void
    {
        self::$layout = $layout;
    }

    /**
     * Şablon → PHP compile.
     */
    protected static function compile(string $content): string
    {
        // {{-- comment --}} tamamen kaldırılır (compile'dan önce işle, sonra {{ }} regex'i çalışsın)
        $content = preg_replace('/\{\{--\s*(.*?)\s*--\}\}/s', '', $content) ?? $content;

        $rules = [
            // {{ $var }} (escape) ve {!! $var !!} (raw)
            '/(?<!@)\{\{\s*(.+?)\s*\}\}/s'  => '<?php echo htmlspecialchars((string)($1 ?? ""), ENT_QUOTES, "UTF-8"); ?>',
            '/(?<!@)\{!!\s*(.+?)\s*!!\}/s'  => '<?php echo (string)($1 ?? ""); ?>',

            // Kontrol akışı
            '/(?<!@)@if\s*\(((?:[^()]|\([^()]*\))*)\)/'      => '<?php if($1): ?>',
            '/(?<!@)@elseif\s*\(((?:[^()]|\([^()]*\))*)\)/'  => '<?php elseif($1): ?>',
            '/(?<!@)@else\b/s'                               => '<?php else: ?>',
            '/(?<!@)@endif\b/s'                              => '<?php endif; ?>',
            '/(?<!@)@unless\s*\(((?:[^()]|\([^()]*\))*)\)/'  => '<?php if(!($1)): ?>',
            '/(?<!@)@endunless\b/s'                          => '<?php endif; ?>',
            '/(?<!@)@isset\s*\(((?:[^()]|\([^()]*\))*)\)/'   => '<?php if(isset($1)): ?>',
            '/(?<!@)@endisset\b/s'                           => '<?php endif; ?>',
            '/(?<!@)@empty\s*\(((?:[^()]|\([^()]*\))*)\)/'   => '<?php if(empty($1)): ?>',
            '/(?<!@)@endempty\b/s'                           => '<?php endif; ?>',

            // Döngüler
            '/(?<!@)@foreach\s*\(((?:[^()]|\([^()]*\))*)\)/' => '<?php foreach($1): ?>',
            '/(?<!@)@endforeach\b/s'                         => '<?php endforeach; ?>',
            '/(?<!@)@for\s*\(((?:[^()]|\([^()]*\))*)\)/'     => '<?php for($1): ?>',
            '/(?<!@)@endfor\b/s'                             => '<?php endfor; ?>',
            '/(?<!@)@while\s*\(((?:[^()]|\([^()]*\))*)\)/'   => '<?php while($1): ?>',
            '/(?<!@)@endwhile\b/s'                           => '<?php endwhile; ?>',
            '/(?<!@)@continue\b/s'                           => '<?php continue; ?>',
            '/(?<!@)@break\b/s'                              => '<?php break; ?>',

            // Layout / Section
            '/(?<!@)@extends\s*\([\'"](.+?)[\'"]\)/s'        => '<?php \App\Core\View::setLayout(\'$1\'); ?>',
            // @yield('name', 'default') — default-value\'lı varyant ÖNCE
            '/(?<!@)@yield\s*\(\s*[\'"]([^\'\"]+?)[\'"]\s*,\s*[\'"]((?:[^\'\"\\\\]|\\\\.)*)[\'"]\s*\)/s'
                => '<?php echo $__view_sections[\'$1\'] ?? \'$2\'; ?>',
            '/(?<!@)@yield\s*\([\'"](.+?)[\'"]\)/s'          => '<?php echo $__view_sections[\'$1\'] ?? ($childContent ?? ""); ?>',
            // @hasSection — section var ve doluysa içeri gir
            '/(?<!@)@hasSection\s*\([\'"](.+?)[\'"]\)/s'
                => '<?php if(isset($__view_sections[\'$1\']) && $__view_sections[\'$1\'] !== ""): ?>',
            // İki argümanlı @section('name', 'inline value') — açık/kapatma yok, doğrudan atar
            '/(?<!@)@section\s*\(\s*[\'"]([^\'\"]+?)[\'"]\s*,\s*[\'"]((?:[^\'\"\\\\]|\\\\.)*)[\'"]\s*\)/s'
                => '<?php $__view_sections[\'$1\'] = \'$2\'; ?>',
            // Tek argümanlı @section('name') ... @endsection
            '/(?<!@)@section\s*\(\s*[\'"]([^\'\"]+?)[\'"]\s*\)/s'
                => '<?php ob_start(); $__view_currentSection = \'$1\'; ?>',
            '/(?<!@)@endsection\b/s'                         => '<?php $__view_sections[$__view_currentSection] = ob_get_clean(); ?>',

            // Include (extra data destekli) — internal state SIZDIRMAZ:
            // sadece kullanıcı $data'sını override-merge eder.
            '/(?<!@)@include\s*\([\'"](.+?)[\'"]\s*,\s*((?:[^()]|\([^()]*\))*)\)/s'
                => '<?php echo \App\Core\View::renderPartial(\'$1\', get_defined_vars(), $2); ?>',
            '/(?<!@)@include\s*\([\'"](.+?)[\'"]\s*\)/s'
                => '<?php echo \App\Core\View::renderPartial(\'$1\', get_defined_vars()); ?>',

            // Raw PHP blok
            '/(?<!@)@php\b/s'                                => '<?php',
            '/(?<!@)@endphp\b/s'                             => '?>',

            // CSRF kısayolu
            '/(?<!@)@csrf\b/s'                               => '<?php echo \'<input type="hidden" name="_csrf_token" value="\' . \App\Core\Session::csrfToken() . \'">\'; ?>',
        ];

        $compiled = preg_replace(array_keys($rules), array_values($rules), $content) ?? $content;

        // Escape edilmiş direktif kaçışları (@@if → @if)
        $escapes = ['@@if','@@elseif','@@else','@@endif','@@unless','@@endunless',
                    '@@isset','@@endisset','@@empty','@@endempty',
                    '@@foreach','@@endforeach','@@for','@@endfor','@@while','@@endwhile',
                    '@@continue','@@break','@@extends','@@yield','@@section','@@endsection',
                    '@@include','@@php','@@endphp','@@csrf','@@{{','@@{!!'];
        $real    = array_map(fn ($e) => substr($e, 1), $escapes);

        return str_replace($escapes, $real, $compiled);
    }

    /**
     * @include için scope-safe render entry.
     *
     * $callerVars: include'i çağıran scope'taki tüm değişkenler (kullanıcı + internal).
     * Burada internal __view_* değişkenleri FİLTRELENİR — partial'a sızmaz.
     */
    public static function renderPartial(string $name, array $callerVars = [], array $extra = []): string
    {
        $safe = [];
        foreach ($callerVars as $k => $v) {
            if (str_starts_with($k, '__view_')) continue;
            if ($k === 'childContent')          continue;
            $safe[$k] = $v;
        }
        return self::render($name, array_merge($safe, $extra));
    }
}
