<?php
/** Run with php tests/cache-clear.php; only a temporary cache directory is touched. */
namespace Typecho {
    class Widget
    {
        public static function widget($name)
        {
            return (object) array('secret' => 'test-site-secret');
        }
    }
}

namespace Widget {
    class User
    {
        public static $administrator = true;
        public static $unavailable = false;

        public static function alloc()
        {
            if (self::$unavailable) {
                throw new \RuntimeException('Test authentication failure');
            }
            return new self();
        }

        public function pass($group, $return)
        {
            return self::$administrator && $group === 'administrator' && $return;
        }
    }

    class Security
    {
        public static function alloc()
        {
            return new self();
        }

        public function getToken($suffix)
        {
            return hash('sha256', 'test-admin-session|' . $suffix);
        }
    }
}

namespace {
    function _t($message, ...$args)
    {
        return $args ? vsprintf($message, $args) : $message;
    }

    function zh_test_check($condition, $message)
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }

    $root = sys_get_temp_dir() . '/zh-cache-test-' . bin2hex(random_bytes(8));
    define('__TYPECHO_ROOT_DIR__', $root);
    require dirname(__DIR__) . '/functions.php';

    try {
        mkdir(ZH_CACHE_DIR, 0755, true);
        file_put_contents(ZH_CACHE_DIR . 'index.html', '');
        file_put_contents(ZH_CACHE_DIR . '.htaccess', 'Require all denied');
        file_put_contents(ZH_CACHE_DIR . 'post_test.html', 'cached article');
        file_put_contents(ZH_CACHE_DIR . 'archive_test.html', 'cached list');
        file_put_contents(ZH_CACHE_DIR . 'unrelated.txt', 'keep');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['zh_cache_clear'] = zh_cache_clear_token();
        zh_test_check(zh_cache_maybe_clear() === null, 'Frontend POST must not clear caches');
        zh_test_check(is_file(ZH_CACHE_DIR . 'post_test.html'), 'Frontend request deleted a cache');

        define('__TYPECHO_ADMIN__', true);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['zh_cache_clear'] = zh_cache_clear_token();
        zh_test_check(zh_cache_maybe_clear() === null, 'GET must not clear caches');
        zh_test_check(is_file(ZH_CACHE_DIR . 'post_test.html'), 'GET deleted a cache');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array();
        zh_test_check(zh_cache_maybe_clear() === null, 'Normal settings save must not clear caches');

        foreach (array('', 'invalid', array('invalid')) as $token) {
            $_POST['zh_cache_clear'] = $token;
            zh_test_check(strpos(zh_cache_maybe_clear(), '安全令牌无效') !== false, 'Invalid token must show an error');
            zh_test_check(is_file(ZH_CACHE_DIR . 'post_test.html'), 'Invalid token deleted a cache');
        }

        $_POST['zh_cache_clear'] = zh_cache_clear_token();
        \Widget\User::$administrator = false;
        zh_test_check(strpos(zh_cache_maybe_clear(), '管理员') !== false, 'Non-admin must be rejected');
        zh_test_check(is_file(ZH_CACHE_DIR . 'post_test.html'), 'Non-admin deleted a cache');
        \Widget\User::$administrator = true;
        \Widget\User::$unavailable = true;
        zh_test_check(strpos(zh_cache_maybe_clear(), '无法验证') !== false, 'Authentication failure must be visible');
        zh_test_check(is_file(ZH_CACHE_DIR . 'post_test.html'), 'Authentication failure deleted a cache');
        \Widget\User::$unavailable = false;

        zh_test_check(zh_cache_maybe_clear() === '已清空 2 个页面缓存文件。', 'Success must report actual deletion count');
        zh_test_check(!file_exists(ZH_CACHE_DIR . 'post_test.html')
            && !file_exists(ZH_CACHE_DIR . 'archive_test.html'), 'Cache files were not deleted');
        zh_test_check(file_exists(ZH_CACHE_DIR . 'index.html')
            && file_get_contents(ZH_CACHE_DIR . '.htaccess') === 'Require all denied'
            && file_get_contents(ZH_CACHE_DIR . 'unrelated.txt') === 'keep', 'Protection and unrelated files must survive');
        zh_test_check(zh_cache_maybe_clear() === '已清空 0 个页面缓存文件。', 'Repeated clear should succeed with zero');

        // A matching directory cannot be unlinked, exercising the filesystem failure report on Windows and Unix.
        mkdir(ZH_CACHE_DIR . 'blocked.html');
        file_put_contents(ZH_CACHE_DIR . 'post_test.html', 'cached article');
        zh_test_check(strpos(zh_cache_maybe_clear(), '已清理 1 个页面缓存文件，另有 1 个清理失败') === 0,
            'Partial failure must report both counts');
        rmdir(ZH_CACHE_DIR . 'blocked.html');
        echo "Cache clear checks passed\n";
    } finally {
        foreach (array('index.html', '.htaccess', 'post_test.html', 'archive_test.html', 'unrelated.txt') as $file) {
            if (is_file(ZH_CACHE_DIR . $file)) {
                unlink(ZH_CACHE_DIR . $file);
            }
        }
        if (is_dir(ZH_CACHE_DIR . 'blocked.html')) {
            rmdir(ZH_CACHE_DIR . 'blocked.html');
        }
        foreach (array(ZH_CACHE_DIR, $root . '/usr/cache', $root . '/usr', $root) as $directory) {
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
    }
}
