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
    if (false === stripos($tag, 'loading=')) {
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

    if (false === stripos($attrs, 'target=')) {
        $attrs .= ' target="_blank"';
    }
    if (false === stripos($attrs, 'rel=')) {
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
        return $custom;
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
        return $default;
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
