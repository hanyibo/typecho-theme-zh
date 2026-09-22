<?php
/** Standalone helper checks: php tests/smoke.php (no Typecho installation needed). */
namespace Typecho {
    class Widget
    {
        public static $options;

        public static function widget($name)
        {
            return self::$options;
        }
    }
}

namespace {
    define('__TYPECHO_ROOT_DIR__', dirname(__DIR__));
    require dirname(__DIR__) . '/functions.php';

    \Typecho\Widget::$options = (object) array(
        'siteUrl' => 'https://example.com/',
        'themeUrl' => 'https://example.com/usr/themes/ZH',
        'defaultThumb' => 'javascript:alert(1)',
    );

    $urls = array(
        'https://example.com/photo.png' => 'https://example.com/photo.png',
        '//cdn.example.com/photo.png' => '//cdn.example.com/photo.png',
        '/archives.html' => '/archives.html',
        'images/photo.png' => 'images/photo.png',
        'javascript:alert(1)' => '#',
        'jav' . "\t" . 'ascript:alert(1)' => '#',
        'https:example.com' => '#',
        'https://' => '#',
        '\\evil.example.com' => '#',
        'data:image/svg+xml,<svg/>' => '#',
    );
    foreach ($urls as $input => $expected) {
        if (zh_safe_url($input) !== $expected) {
            throw new \RuntimeException('Unexpected URL result for ' . json_encode($input));
        }
    }

    $widget = new class {
        public $fields;
        public $content = '<img src="javascript:alert(1)">';
        public $title = 'test';

        public function attachments($limit)
        {
            return (object) array('attachment' => null);
        }
    };
    $fallback = false;
    if (zh_thumb_src($widget, $fallback) !== \Typecho\Widget::$options->themeUrl . '/assets/img/default-thumb.svg'
        || !$fallback) {
        throw new \RuntimeException('Unsafe thumbnail did not fall back');
    }
    $widget->content = '<img src="https://example.com/a.png?x=1&amp;y=2">';
    if (zh_thumb_src($widget, $fallback) !== 'https://example.com/a.png?x=1&y=2' || $fallback) {
        throw new \RuntimeException('Thumbnail HTML entities were not decoded');
    }

    ob_start();
    zh_json_ld(array('text' => '</script>', 'bad' => "\xFF"));
    $json = ob_get_clean();
    if (strpos($json, '</script>') !== false || !is_array(json_decode($json, true))) {
        throw new \RuntimeException('JSON-LD escaping failed');
    }

    echo "Helper smoke checks passed\n";
}
