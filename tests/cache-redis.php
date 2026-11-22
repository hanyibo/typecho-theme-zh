<?php
/** Run with php tests/cache-redis.php; uses an in-memory Redis stub, no server required. */
namespace {
    if (!class_exists('Redis', false)) {
        class Redis
        {
            const OPT_READ_TIMEOUT = 0;

            public static $keys = array();
            public static $last = null;

            public $host = '';
            public $port = 0;
            public $password = '';
            public $database = 0;
            public $options = array();

            public function setOption($name, $value)
            {
                $this->options[$name] = $value;
                return true;
            }

            public function connect($host, $port, $timeout)
            {
                $this->host = $host;
                $this->port = (int) $port;
                self::$last = $this;
                return true;
            }

            public function auth($password)
            {
                $this->password = $password;
                return true;
            }

            public function select($database)
            {
                $this->database = (int) $database;
                return true;
            }

            public function del($keys)
            {
                $count = 0;
                foreach ((array) $keys as $key) {
                    if (isset(self::$keys[$key])) {
                        unset(self::$keys[$key]);
                        $count++;
                    }
                }
                return $count;
            }

            public function scan(&$iterator, $pattern, $count = 0)
            {
                $keys = array_keys(self::$keys);
                if ($iterator === null) {
                    $iterator = 0;
                }
                $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
                $matches = array();
                for ($i = (int) $iterator; $i < count($keys); $i++) {
                    if (preg_match($regex, $keys[$i])) {
                        $matches[] = $keys[$i];
                    }
                    if ($count > 0 && count($matches) >= $count) {
                        $iterator = $i + 1;
                        return $matches;
                    }
                }
                $iterator = 0;
                return $matches ?: false;
            }
        }
    } else {
        echo "phpredis extension loaded, Redis stub test skipped\n";
        exit(0);
    }
}

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

namespace Widget {
    class User
    {
        public static function alloc()
        {
            return new self();
        }

        public function hasLogin()
        {
            return false;
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

    $root = sys_get_temp_dir() . '/zh-redis-test-' . bin2hex(random_bytes(8));
    define('__TYPECHO_ROOT_DIR__', $root);
    require dirname(__DIR__) . '/functions.php';

    \Typecho\Widget::$options = (object) array(
        'siteUrl' => 'https://example.com/',
        'cacheDriver' => 'redis',
        'redisHost' => '127.0.0.1',
        'redisPort' => '6380',
        'redisPassword' => 'test-pass',
        'redisDatabase' => '2',
        'redisPrefix' => 'site:',
    );

    try {
        zh_test_check(zh_cache_redis_selected(), 'Redis driver was not selected');

        $redis = zh_cache_redis();
        zh_test_check($redis instanceof \Redis, 'Redis connection was not established');
        zh_test_check($redis->host === '127.0.0.1' && $redis->port === 6380, 'Host or port mismatch');
        zh_test_check($redis->password === 'test-pass', 'Password mismatch');
        zh_test_check($redis->database === 2, 'Database mismatch');
        zh_test_check($redis->options[\Redis::OPT_READ_TIMEOUT] === ZH_CACHE_REDIS_TIMEOUT,
            'Read timeout was not set');

        $prefix = zh_cache_prefix();
        zh_test_check($prefix === 'site:' . substr(md5('https://example.com/'), 0, 8) . ':',
            'Unexpected key prefix');

        \Redis::$keys = array(
            $prefix . 'index_aaa' => 'cached index',
            $prefix . 'post_bbb' => 'cached post',
            'other:keep' => 'keep',
            'site:00000000:other' => 'keep',
        );
        zh_test_check(zh_cache_redis_count() === 2, 'Redis key count mismatch');

        mkdir(ZH_CACHE_DIR, 0755, true);
        file_put_contents(ZH_CACHE_DIR . 'index.html', '');
        file_put_contents(ZH_CACHE_DIR . 'post_test.html', 'file cache');
        file_put_contents(ZH_CACHE_DIR . 'unrelated.txt', 'keep');

        $failed = 0;
        $unavailable = false;
        $count = zh_cache_clear($failed, $unavailable);
        zh_test_check($count === 3, 'Clear count mismatch: ' . $count);
        zh_test_check($failed === 0 && !$unavailable, 'Clear reported a failure');
        zh_test_check(count(\Redis::$keys) === 2
            && isset(\Redis::$keys['other:keep'])
            && isset(\Redis::$keys['site:00000000:other']),
            'Clear deleted keys outside the site prefix');
        zh_test_check(!file_exists(ZH_CACHE_DIR . 'post_test.html')
            && file_exists(ZH_CACHE_DIR . 'index.html')
            && file_exists(ZH_CACHE_DIR . 'unrelated.txt'),
            'File cache clear behavior is wrong');

        echo "Redis cache checks passed\n";
    } finally {
        foreach (array('index.html', 'post_test.html', 'unrelated.txt') as $file) {
            if (is_file(ZH_CACHE_DIR . $file)) {
                unlink(ZH_CACHE_DIR . $file);
            }
        }
        foreach (array(ZH_CACHE_DIR, $root . '/usr/cache', $root . '/usr', $root) as $directory) {
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
    }
}
