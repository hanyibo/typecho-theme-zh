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

    /* 缓存域名白名单：只允许站点域名及其 www 变体，其他 Host 一律不缓存 */
    unset($_SERVER['SERVER_NAME']);
    $hostCases = array(
        'www.example.com' => true,
        'example.com' => true,
        'www.example.com:443' => true,
        '910vr.com' => false,
        'example.com.evil.test' => false,
        '' => false,
    );
    foreach ($hostCases as $host => $expected) {
        $_SERVER['HTTP_HOST'] = (string) $host;
        if (zh_cache_host_allowed() !== $expected) {
            throw new \RuntimeException('Unexpected cache host result for ' . json_encode($host));
        }
    }

    $_SERVER['HTTP_HOST'] = 'www.example.com';
    $wwwHostKey = zh_cache_host_key();
    $_SERVER['HTTP_HOST'] = 'example.com';
    if ($wwwHostKey === zh_cache_host_key()) {
        throw new \RuntimeException('Cache keys were not isolated by host');
    }
    unset($_SERVER['HTTP_HOST']);

    if (zh_absolute_url('/usr/uploads/a.png') !== 'https://example.com/usr/uploads/a.png') {
        throw new \RuntimeException('Relative URL was not expanded to absolute');
    }

    $external = zh_link_tag(array(
        0 => '<a href="https://external.test/a" rel="nofollow">x</a>',
        1 => ' href="https://external.test/a" rel="nofollow"',
        2 => 'x',
    ));
    if (strpos($external, 'noopener') === false || strpos($external, 'nofollow') === false) {
        throw new \RuntimeException('External link rel hardening failed');
    }

    $internal = '<a href="https://example.com/a">x</a>';
    if (zh_link_tag(array(0 => $internal, 1 => ' href="https://example.com/a"', 2 => 'x')) !== $internal) {
        throw new \RuntimeException('Internal link was modified');
    }

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
