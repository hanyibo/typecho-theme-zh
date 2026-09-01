<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * ZH · 简约单栏主题 —— 辅助函数
 *
 * @package ZH
 */

/* ===================== 主题外观设置 ===================== */

function themeConfig($form)
{
    $defaultThumb = new \Typecho\Widget\Helper\Form\Element\Text(
        'defaultThumb',
        null,
        '',
        _t('默认缩略图地址'),
        _t('文章没有图片时使用的占位图，留空则使用主题内置默认图；同时作为 og:image 的兜底图')
    );
    $form->addInput($defaultThumb->addRule('url', _t('请填写合法的图片地址，或留空')));

    $faviconUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'faviconUrl',
        null,
        '',
        _t('Favicon 图标地址'),
        _t('浏览器标签页图标，ico / png / svg 均可；留空则不输出，浏览器会尝试站点根目录的 /favicon.ico')
    );
    $form->addInput($faviconUrl->addRule('url', _t('请填写合法的图标地址，或留空')));

    $seoDescription = new \Typecho\Widget\Helper\Form\Element\Text(
        'seoDescription',
        null,
        '',
        _t('首页 SEO 描述'),
        _t('显示在搜索结果中的首页描述，留空则使用 Typecho 设置中的站点描述')
    );
    $form->addInput($seoDescription);

    $seoKeywords = new \Typecho\Widget\Helper\Form\Element\Text(
        'seoKeywords',
        null,
        '',
        _t('首页 SEO 关键词'),
        _t('多个关键词用英文逗号分隔，留空则不输出首页关键词')
    );
    $form->addInput($seoKeywords);

    $archivesUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'archivesUrl',
        null,
        '/archives.html',
        _t('「归档」页链接'),
        _t('导航「归档」指向的地址；若已创建 slug 为 archives 的页面会自动识别，无需填写')
    );
    $form->addInput($archivesUrl);

    $categoriesUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'categoriesUrl',
        null,
        '/categories.html',
        _t('「分类」页链接'),
        _t('导航「分类」点击后进入的分类索引页地址；若已创建 slug 为 categories 的页面会自动识别')
    );
    $form->addInput($categoriesUrl);

    $aboutUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'aboutUrl',
        null,
        '/about.html',
        _t('「关于」页链接'),
        _t('导航「关于」指向的地址；若已创建 slug 为 about 的页面会自动识别')
    );
    $form->addInput($aboutUrl);

    $linksUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'linksUrl',
        null,
        '',
        _t('「友链」页链接'),
        _t('导航「友链」指向的地址；若已创建 slug 为 links 的页面会自动识别。页面与此处均未设置时导航不显示「友链」入口')
    );
    $form->addInput($linksUrl);

    $friendLinks = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'friendLinks',
        null,
        '',
        _t('友情链接'),
        _t('每行一个，格式：名称|链接|描述（描述可省略），由「友情链接」页面模板展示')
    );
    $form->addInput($friendLinks);

    $extraNav = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'extraNav',
        null,
        '',
        _t('额外导航项'),
        _t('追加到导航栏末尾，每行一个，格式：名称|链接')
    );
    $form->addInput($extraNav);

    $icp = new \Typecho\Widget\Helper\Form\Element\Text(
        'icp',
        null,
        '',
        _t('ICP 备案号'),
        _t('留空则不显示，填写后展示在页脚并链接到工信部备案网站')
    );
    $form->addInput($icp);

    $footerText = new \Typecho\Widget\Helper\Form\Element\Text(
        'footerText',
        null,
        '',
        _t('页脚文案'),
        _t('追加在版权信息之后的一句话，留空则不显示')
    );
    $form->addInput($footerText);

    $customHead = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'customHead',
        null,
        '',
        _t('自定义 head 代码'),
        _t('输出在 </head> 之前，可放置搜索引擎站点验证、统计代码等')
    );
    $form->addInput($customHead);

    $customFooter = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'customFooter',
        null,
        '',
        _t('自定义页脚代码'),
        _t('输出在 </body> 之前，可放置统计脚本、客服组件等')
    );
    $form->addInput($customFooter);

    /* ---- 页面缓存 ---- */

    $cacheIndex = new \Typecho\Widget\Helper\Form\Element\Radio(
        'cacheIndex',
        array(1 => _t('启用'), 0 => _t('关闭')),
        0,
        _t('首页缓存'),
        _t('为访客缓存首页列表区域，内容变更时自动失效，默认关闭')
    );
    $form->addInput($cacheIndex);

    $cacheArchive = new \Typecho\Widget\Helper\Form\Element\Radio(
        'cacheArchive',
        array(1 => _t('启用'), 0 => _t('关闭')),
        0,
        _t('归档页缓存'),
        _t('缓存分类、标签、日期、搜索等归档列表页，默认关闭')
    );
    $form->addInput($cacheArchive);

    $cachePost = new \Typecho\Widget\Helper\Form\Element\Radio(
        'cachePost',
        array(1 => _t('启用'), 0 => _t('关闭')),
        0,
        _t('文章页缓存'),
        _t('缓存文章正文区域（评论区始终实时渲染），默认关闭')
    );
    $form->addInput($cachePost);

    $cachePage = new \Typecho\Widget\Helper\Form\Element\Radio(
        'cachePage',
        array(1 => _t('启用'), 0 => _t('关闭')),
        0,
        _t('独立页缓存'),
        _t('缓存独立页面，含「归档 / 分类 / 友情链接」自定义模板，默认关闭')
    );
    $form->addInput($cachePage);

    $cacheTtl = new \Typecho\Widget\Helper\Form\Element\Text(
        'cacheTtl',
        null,
        '3600',
        _t('缓存有效期（秒）'),
        _t('超过该时长的缓存自动重建；留空或 0 视为 3600')
    );
    $form->addInput($cacheTtl->addRule('isInteger', _t('缓存有效期必须是非负整数')));
}

/* ===================== 文章编辑页自定义字段 ===================== */

function themeFields($layout)
{
    $thumb = new \Typecho\Widget\Helper\Form\Element\Text(
        'thumb',
        null,
        null,
        _t('自定义缩略图'),
        _t('优先级最高；留空则自动取正文第一张图片，再退回默认缩略图')
    );
    $layout->addItem($thumb);

    $description = new \Typecho\Widget\Helper\Form\Element\Text(
        'description',
        null,
        null,
        _t('SEO 描述'),
        _t('留空则自动截取正文前 110 字')
    );
    $layout->addItem($description);

    $keywords = new \Typecho\Widget\Helper\Form\Element\Text(
        'keywords',
        null,
        null,
        _t('SEO 关键词'),
        _t('留空则自动使用文章标签')
    );
    $layout->addItem($keywords);
}

/* ===================== 钩子 ===================== */

function themeInit($archive)
{
    // 正文统一处理：图片懒加载、外链新窗口打开
    if ($archive->is('single')) {
        $archive->content = zh_filter_content($archive->content);
    }
}

/* ===================== 正文过滤 ===================== */

function zh_filter_content($content)
{
    if (trim((string)$content) === '') {
        return $content;
    }
    $content = preg_replace_callback('/<img\b[^>]*>/i', 'zh_img_tag', $content);
    $content = preg_replace_callback('/<a\b([^>]*)>([\s\S]*?)<\/a>/i', 'zh_link_tag', $content);
    return $content;
}

function zh_img_tag($matches)
{
    $tag = $matches[0];
    /* 用断言匹配完整属性名，避免误命中 data-loading= 之类 */
    if (!preg_match('/(?<![\w-])loading\s*=/i', $tag)) {
        $tag = str_ireplace('<img', '<img loading="lazy" decoding="async"', $tag);
    }
    return $tag;
}

function zh_link_tag($matches)
{
    $attrs = $matches[1];
    $inner = $matches[2];

    if (!preg_match('/\bhref\s*=\s*("|\')([^"\']*)\1/i', $attrs, $hrefMatch)) {
        return $matches[0];
    }
    $href = trim($hrefMatch[2]);
    if (stripos($href, 'http://') !== 0 && stripos($href, 'https://') !== 0) {
        return $matches[0];
    }

    $siteHost = (string) parse_url(\Typecho\Widget::widget('\Widget\Options')->siteUrl, PHP_URL_HOST);
    $hrefHost = (string) parse_url($href, PHP_URL_HOST);
    if ($hrefHost !== '' && strcasecmp($hrefHost, $siteHost) === 0) {
        return $matches[0];
    }

    if (!preg_match('/(?<![\w-])target\s*=/i', $attrs)) {
        $attrs .= ' target="_blank"';
    }
    if (!preg_match('/(?<![\w-])rel\s*=/i', $attrs)) {
        $attrs .= ' rel="noopener"';
    }
    return '<a' . $attrs . '>' . $inner . '</a>';
}

/* ===================== 缩略图 ===================== */

/**
 * 取文章缩略图地址（不输出）
 * 优先级：自定义字段 thumb > 正文第一张图 > 附件图片 > 默认缩略图
 */
function zh_thumb_src($widget)
{
    $fields = $widget->fields;
    $custom = ($fields && isset($fields->thumb)) ? trim((string) $fields->thumb) : '';
    if ($custom !== '') {
        $custom = zh_safe_url($custom);
        if ($custom !== '#') {
            return $custom;
        }
    }

    if (preg_match('/<img\b[^>]*src\s*=\s*("|\')([^"\']+)\1[^>]*>/i', (string) $widget->content, $m)) {
        return trim($m[2]);
    }

    $attachment = $widget->attachments(1)->attachment;
    if ($attachment && $attachment->isImage) {
        return $attachment->url;
    }

    $options = \Typecho\Widget::widget('\Widget\Options');
    $default = trim((string) $options->defaultThumb);
    if ($default !== '') {
        $default = zh_safe_url($default);
        if ($default !== '#') {
            return $default;
        }
    }

    return $options->themeUrl . '/assets/img/default-thumb.svg';
}

/** 输出缩略图 <img> */
function zh_thumb($widget, $class = 'zh-thumb')
{
    $src = zh_thumb_src($widget);
    $alt = htmlspecialchars((string) $widget->title, ENT_QUOTES, 'UTF-8');
    echo '<img class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" src="'
        . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="' . $alt
        . '" loading="lazy" decoding="async" />';
}

/* ===================== SEO 辅助 ===================== */

/** UTF-8 安全截断（不依赖 mbstring） */
function zh_substr($str, $length)
{
    $str = (string) $str;
    if (preg_match('/^.{0,' . (int) $length . '}/us', $str, $m)) {
        return $m[0];
    }
    return $str;
}

/** 取字符串首字符（用于友链占位头像） */
function zh_first_char($str)
{
    return (preg_match('/./u', (string) $str, $m)) ? $m[0] : '链';
}

/**
 * 当前页面完整 URL（用于 canonical / og:url）
 * 去掉查询参数，保留分页路径
 *
 * 注意：本函数在普通函数作用域内运行，不能通过 $widget->options
 * 访问（protected 属性在类外会走 __get 返回 null），必须静态获取 Options。
 */
function zh_current_url($widget)
{
    $path = (string) (parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH) ?: '/');

    $siteUrl = (string) \Typecho\Widget::widget('\Widget\Options')->siteUrl;
    $parts = parse_url($siteUrl);

    if (is_array($parts) && !empty($parts['scheme']) && !empty($parts['host'])) {
        $sitePath = isset($parts['path']) ? rtrim($parts['path'], '/') : '';
        if ($sitePath !== '' && strpos($path, $sitePath) === 0) {
            $path = '/' . ltrim(substr($path, strlen($sitePath)), '/');
        }
        return $parts['scheme'] . '://' . $parts['host']
            . (isset($parts['port']) ? ':' . $parts['port'] : '')
            . $sitePath . '/' . ltrim($path, '/');
    }

    // 兜底：直接根据当前请求构造
    $https = (isset($_SERVER['HTTPS']) && '' !== $_SERVER['HTTPS'] && 'off' !== strtolower((string) $_SERVER['HTTPS']))
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']))
        || (isset($_SERVER['REQUEST_SCHEME']) && 'https' === strtolower((string) $_SERVER['REQUEST_SCHEME']))
        || (isset($_SERVER['SERVER_PORT']) && 443 === (int) $_SERVER['SERVER_PORT']);
    $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST']
        : (isset($_SERVER['SERVER_NAME']) ? (string) $_SERVER['SERVER_NAME'] : '');
    /* Host 头由客户端可控，剔除非法字符防止注入到 canonical / og:url */
    $host = preg_replace('/[^a-zA-Z0-9.:\[\]-]/', '', $host);
    if ($host === '') {
        return '';
    }
    return ($https ? 'https' : 'http') . '://' . $host . '/' . ltrim($path, '/');
}

/**
 * 导航高亮：比较当前请求路径与目标链接路径（兼容子目录安装）
 */
function zh_nav_active($link)
{
    $parts = parse_url((string) \Typecho\Widget::widget('\Widget\Options')->siteUrl);
    $sitePath = isset($parts['path']) ? rtrim($parts['path'], '/') : '';

    $cur = (string) (parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH) ?: '/');
    if ($sitePath !== '' && strpos($cur, $sitePath) === 0) {
        $cur = '/' . ltrim(substr($cur, strlen($sitePath)), '/');
    }

    $linkPath = parse_url((string) $link, PHP_URL_PATH);
    if ($linkPath === null || $linkPath === '') {
        return false;
    }
    if ($sitePath !== '' && strpos($linkPath, $sitePath) === 0) {
        $linkPath = '/' . ltrim(substr($linkPath, strlen($sitePath)), '/');
    }
    $linkPath = '/' . ltrim((string) $linkPath, '/');

    return rtrim($cur, '/') === rtrim($linkPath, '/');
}

/** 输出 JSON-LD 结构化数据（转义 </script> 防注入） */
function zh_json_ld($data)
{
    echo str_replace('</', '<\\/', (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

/**
 * 校正不可信来源的链接地址：仅放行 http(s) 与相对地址，
 * 其余协议（javascript: / vbscript: / data: 等）一律替换为 #。
 * 先剔除浏览器解析 URL 时会忽略的控制字符，防止 "jav\tascript:" 之类绕过。
 */
function zh_safe_url($url)
{
    $url = trim(str_replace(array("\r", "\n", "\t", "\0", "\x0B"), '', (string) $url));
    if ($url === '') {
        return '#';
    }
    if (preg_match('/^([a-zA-Z][a-zA-Z0-9+.\-]*):/s', $url, $m)
        && strtolower($m[1]) !== 'http' && strtolower($m[1]) !== 'https') {
        return '#';
    }
    return $url;
}

/** 解析「名称|链接|描述」行格式为二维数组 */
function zh_parse_lines($raw, $minParts = 2)
{
    $result = array();
    $lines = preg_split('/\r\n|\r|\n/', (string) $raw);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) < $minParts || $parts[0] === '' || $parts[1] === '') {
            continue;
        }
        $result[] = $parts;
    }
    return $result;
}

/* ===================== 页面缓存 ===================== */

/**
 * 以文件方式缓存模板中 <main> 区域的输出（参考 rizhi 主题的 StartCache/EndCache 思路）。
 *
 * 与 rizhi 直接按 REQUEST_URI 哈希 + 定时过期不同，这里在命中时先校验
 * 「内容指纹」：全站文章篇数、最后修改时间、评论总数、分类/标签变动序号
 * 以及主题设置快照。任何内容变更都会让指纹变化，下一次访问即重建缓存，
 * 无需依赖 Typecho 编辑钩子（后台请求不加载主题 functions.php，钩子不可靠）。
 *
 * 用法（模板内）：
 *   if (zh_cache_start('post', $this)): ... 正常输出 ... <?php endif; ?>
 *   ... 缓存区域内任意输出 ...
 *   <?php zh_cache_end(); ?>
 */

define('ZH_CACHE_DIR', __TYPECHO_ROOT_DIR__ . '/usr/cache/ZH-theme/');
define('ZH_CACHE_TTL_DEFAULT', 3600);

/** 是否启用页面缓存（后台登录用户跳过，保证所见即所得） */
function zh_cache_enabled($scope)
{
    try {
        if (\Widget\User::alloc()->hasLogin()) {
            return false;
        }
    } catch (\Throwable $e) {
        return false;
    }

    $options = \Typecho\Widget::widget('\Widget\Options');
    switch ($scope) {
        case 'index':
            return (int) $options->cacheIndex === 1;
        case 'archive':
            return (int) $options->cacheArchive === 1;
        case 'post':
            return (int) $options->cachePost === 1;
        case 'page':
            return (int) $options->cachePage === 1;
        default:
            return false;
    }
}

/** 缓存有效期（秒），非法值回落 3600 */
function zh_cache_ttl()
{
    $options = \Typecho\Widget::widget('\Widget\Options');
    $ttl = (int) trim((string) $options->cacheTtl);
    return $ttl > 0 ? $ttl : ZH_CACHE_TTL_DEFAULT;
}

/**
 * 内容指纹：把影响页面输出的全站性数据压缩成一个短哈希。
 * 文章新增/编辑/删除会改变 modified 或篇数；评论增删改审核会改变
 * commentsNum 之和（Typecho 会同步 contents.commentsNum）；分类、
 * 标签、菜单、主题设置变化会改变 option 值。
 */
function zh_cache_fingerprint()
{
    static $memo = null;
    if ($memo !== null) {
        return $memo;
    }

    $db = \Typecho\Db::get();

    $posts = $db->fetchRow($db->select(array('COUNT(cid)' => 'cnt', 'MAX(modified)' => 'mtime'))
        ->from('table.contents')
        ->where('type = ? AND status = ?', 'post', 'publish'));

    $comments = $db->fetchRow($db->select(array('SUM(commentsNum)' => 'cmt'))
        ->from('table.contents')
        ->where('type = ?', 'post'));

    $metas = $db->fetchRow($db->select(array('COUNT(mid)' => 'cnt', 'MAX(mid)' => 'maxid'))
        ->from('table.metas'));

    // 独立页面（关于页、自定义模板页等）的修改时间
    $pages = $db->fetchRow($db->select(array('MAX(modified)' => 'mtime'))
        ->from('table.contents')
        ->where('type = ?', 'page'));

    $options = \Typecho\Widget::widget('\Widget\Options');

    $raw = implode('|', array(
        'posts=' . ($posts ? (int) $posts['cnt'] . ':' . (int) $posts['mtime'] : '0:0'),
        'comments=' . ($comments ? (int) $comments['cmt'] : 0),
        'metas=' . ($metas ? (int) $metas['cnt'] . ':' . (int) $metas['maxid'] : '0:0'),
        'pages=' . ($pages ? (int) $pages['mtime'] : 0),
        'theme=' . md5(json_encode(array(
            $options->cacheTtl,
            $options->friendLinks,
            $options->extraNav,
            $options->defaultThumb,
            $options->faviconUrl,
            $options->linksUrl,
            $options->icp,
            $options->footerText,
        ))),
    ));

    return $memo = substr(md5($raw), 0, 10);
}

/** 当前请求对应的缓存文件路径（按 scope + 路径 + 分页参数区分） */
function zh_cache_path($scope)
{
    $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
    $key = $scope . '_' . md5($uri) . '_' . zh_cache_fingerprint();
    return ZH_CACHE_DIR . $key . '.html';
}

/**
 * 尝试输出缓存；命中返回 true（模板应跳过区域渲染），
 * 未命中开启输出缓冲并返回 false（模板继续正常渲染，结尾调 zh_cache_end）。
 * 仅缓存 GET 请求；POST（提交评论等）一律实时渲染。
 */
function zh_cache_start($scope)
{
    if (!zh_cache_enabled($scope) || ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return false;
    }

    $path = zh_cache_path($scope);
    if (is_file($path) && (time() - (int) filemtime($path)) <= zh_cache_ttl()) {
        $content = file_get_contents($path);
        if ($content !== false) {
            echo $content;
            return true;
        }
    }

    ob_start();
    $GLOBALS['zh_cache_scope'] = $scope;
    return false;
}

/** 结束缓存区域：把缓冲内容落盘并输出 */
function zh_cache_end()
{
    if (!isset($GLOBALS['zh_cache_scope'])) {
        return;
    }
    $scope = $GLOBALS['zh_cache_scope'];
    unset($GLOBALS['zh_cache_scope']);

    $content = ob_get_clean();
    if ($content === false || $content === '') {
        return;
    }

    echo $content;

    $dir = ZH_CACHE_DIR;
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $tmp = tempnam($dir, 'w');
    if ($tmp !== false) {
        file_put_contents($tmp, $content);
        $dest = zh_cache_path($scope);
        // Windows 上 rename() 不能覆盖已存在文件，先删旧缓存
        if (is_file($dest)) {
            @unlink($dest);
        }
        @rename($tmp, $dest); // 原子替换，避免并发写坏文件
    }
}

/** 手动清空全部页面缓存（换主题、批量导入等场景使用） */
function zh_cache_clear()
{
    if (!is_dir(ZH_CACHE_DIR)) {
        return 0;
    }
    $count = 0;
    foreach (glob(ZH_CACHE_DIR . '*.html') ?: array() as $file) {
        if (@unlink($file)) {
            $count++;
        }
    }
    return $count;
}
