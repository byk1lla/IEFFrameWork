<?php

namespace App\Core;

/**
 * Simple Template Engine (Blade-lite)
 */
class View
{
    protected static string $viewPath = VIEW_PATH;
    protected static string $cachePath = STORAGE_PATH . '/cache/views';

    public static function render(string $view, array $data = []): string
    {
        $viewFile = self::$viewPath . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("View [{$view}] not found at {$viewFile}");
        }

        $content = file_get_contents($viewFile);
        $compiled = self::compile($content);

        extract($data);

        // Use output buffering to capture executed PHP
        ob_start();
        try {
            eval ('?>' . $compiled);
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return ob_get_clean();
    }

    protected static function compile(string $content): string
    {
        $directives = [
            '/\{\{\s*(.+?)\s*\}\}/' => '<?php echo htmlspecialchars($1); ?>',
            '/\{!!\s*(.+?)\s*!!\}/' => '<?php echo $1; ?>',
            '/@if\s*\((.+?)\)/' => '<?php if($1): ?>',
            '/@elseif\s*\((.+?)\)/' => '<?php elseif($1): ?>',
            '/@else/' => '<?php else: ?>',
            '/@endif/' => '<?php endif; ?>',
            '/@foreach\s*\((.+?)\)/' => '<?php foreach($1): ?>',
            '/@endforeach/' => '<?php endforeach; ?>',
            '/@extends\s*\(\'(.+?)\'\)/' => '<?php // Layout: $1 ?>', // Layouts handled differently or via simple includes
            '/@yield\s*\(\'(.+?)\'\)/' => '<?php echo $sections[\'$1\'] ?? \'\'; ?>',
            '/@section\s*\(\'(.+?)\'\)/' => '<?php ob_start(); $currentSection = \'$1\'; ?>',
            '/@endsection/' => '<?php $sections[$currentSection] = ob_get_clean(); ?>',
            '/@include\s*\(\'(.+?)\'\)/' => '<?php echo \App\Core\View::render(\'$1\', get_defined_vars()); ?>',
        ];

        foreach ($directives as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }
}
