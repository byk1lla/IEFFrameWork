<?php

namespace App\Core;

/**
 * Simple Template Engine (Blade-lite)
 */
class View
{
    protected static string $viewPath = VIEW_PATH;
    protected static string $cachePath = STORAGE_PATH . '/cache/views';
    protected static array $sections = [];
    protected static ?string $layout = null;
    protected static int $renderCount = 0;

    public static function render(string $view, array $data = []): string
    {
        self::$renderCount++;

        $viewFile = self::$viewPath . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            self::$renderCount--;
            throw new \Exception("View [{$view}] not found at {$viewFile}");
        }

        $content = file_get_contents($viewFile);
        $compiled = self::compile($content);

        extract($data);
        $sections = &self::$sections;

        ob_start();
        try {
            eval ('?>' . $compiled);
        } catch (\Throwable $e) {
            ob_end_clean();
            self::$renderCount--;
            throw $e;
        }
        $childContent = ob_get_clean();

        if (self::$layout) {
            $layout = self::$layout;
            self::$layout = null;
            $result = self::render($layout, array_merge($data, ['childContent' => $childContent]));
            self::$renderCount--;
            if (self::$renderCount === 0)
                self::$sections = [];
            return $result;
        }

        self::$renderCount--;
        if (self::$renderCount === 0)
            self::$sections = [];
        return $childContent;
    }

    protected static function compile(string $content): string
    {
        $directives = [
            '/(?<!@)\{\{\s*(.+?)\s*\}\}/' => '<?php echo htmlspecialchars($1 ?? ""); ?>',
            '/(?<!@)\{!!\s*(.+?)\s*!!\}/' => '<?php echo $1; ?>',
            '/(?<!@)@if\s*\(((?:[^()]*|\([^()]*\))*)\)/' => '<?php if($1): ?>',
            '/(?<!@)@elseif\s*\(((?:[^()]*|\([^()]*\))*)\)/' => '<?php elseif($1): ?>',
            '/(?<!@)@else/s' => '<?php else: ?>',
            '/(?<!@)@endif/s' => '<?php endif; ?>',
            '/(?<!@)@foreach\s*\(((?:[^()]*|\([^()]*\))*)\)/' => '<?php foreach($1): ?>',
            '/(?<!@)@endforeach/s' => '<?php endforeach; ?>',
            '/(?<!@)@extends\s*\([\'"](.+?)[\'"]\)/s' => '<?php \App\Core\View::setLayout(\'$1\'); ?>',
            '/(?<!@)@yield\s*\([\'"](.+?)[\'"]\)/s' => '<?php echo $sections[\'$1\'] ?? $childContent ?? \'\'; ?>',
            '/(?<!@)@section\s*\([\'"](.+?)[\'"]\)/s' => '<?php ob_start(); $currentSection = \'$1\'; ?>',
            '/(?<!@)@endsection/s' => '<?php $sections[$currentSection] = ob_get_clean(); ?>',
            '/(?<!@)@include\s*\([\'"](.+?)[\'"]\s*\)/s' => '<?php echo \App\Core\View::render(\'$1\', get_defined_vars()); ?>',
            '/(?<!@)@include\s*\([\'"](.+?)[\'"]\s*,\s*(.+?)\)/s' => '<?php echo \App\Core\View::render(\'$1\', array_merge(get_defined_vars(), $2)); ?>',
        ];

        foreach ($directives as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        // Clean up escaped directives
        $content = str_replace([
            '@@if',
            '@@elseif',
            '@@else',
            '@@endif',
            '@@foreach',
            '@@endforeach',
            '@@extends',
            '@@yield',
            '@@section',
            '@@endsection',
            '@@include',
            '@@{{',
            '@@{!!'
        ], [
            '@if',
            '@elseif',
            '@else',
            '@endif',
            '@foreach',
            '@endforeach',
            '@extends',
            '@yield',
            '@section',
            '@endsection',
            '@include',
            '{{',
            '{!!'
        ], $content);

        return $content;
    }

    public static function setLayout(string $layout): void
    {
        self::$layout = $layout;
    }
}
