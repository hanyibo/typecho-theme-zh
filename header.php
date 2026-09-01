<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/** @var Widget_Archive $this */

$options = $this->options;
$siteName = (string) $options->title;

/* 当前页码（翻页标题去重） */
$zh_page = 1;
if (isset($this->_currentPage)) {
    $zh_page = max(1, (int) $this->_currentPage);
}

/* ---- 自动识别导航目标页面（按 slug 匹配，找不到回退到主题设置） ---- */
$zh_pagesMap = array();
$this->widget('\Widget\Contents\Page\Rows')->to($zh_pages);
while ($zh_pages->next()) {
    $zh_pagesMap[$zh_pages->slug] = $zh_pages->permalink;
}

$zh_nav_archives = isset($zh_pagesMap['archives'])
    ? $zh_pagesMap['archives']
    : (trim((string) $options->archivesUrl) !== '' ? trim((string) $options->archivesUrl) : '/archives.html');
$zh_nav_categories = isset($zh_pagesMap['categories'])
    ? $zh_pagesMap['categories']
    : (trim((string) $options->categoriesUrl) !== '' ? trim((string) $options->categoriesUrl) : '/categories.html');
$zh_nav_about = isset($zh_pagesMap['about'])
    ? $zh_pagesMap['about']
    : (trim((string) $options->aboutUrl) !== '' ? trim((string) $options->aboutUrl) : '/about.html');
/* 「友链」与上不同：页面 slug 与设置均未提供时不显示导航项，避免死链 */
$zh_nav_links = isset($zh_pagesMap['links'])
    ? $zh_pagesMap['links']
    : (trim((string) $options->linksUrl) !== '' ? trim((string) $options->linksUrl) : '');

$zh_extra_nav = zh_parse_lines((string) $options->extraNav);

/* ---- SEO：标题 / 描述 / 关键词 ---- */
$zh_is_index = $this->is('index');
$zh_is_single = $this->is('single');
$zh_is_search = $this->is('search');

$zh_arch_name = '';
if (!$zh_is_index && !$zh_is_single) {
    ob_start();
    $this->archiveTitle(array(
        'category' => _t('分类「%s」'),
        'tag'      => _t('标签「%s」'),
        'search'   => _t('搜索「%s」'),
        'date'     => _t('「%s」的存档'),
        'author'   => _t('「%s」的文章'),
    ), '', '');
    $zh_arch_name = trim(ob_get_clean());
}

if ($zh_is_index) {
    $zh_title = $siteName;
    $zh_home_desc = trim((string) $options->description);
    if ($zh_home_desc !== '') {
        $zh_title .= ' - ' . $zh_home_desc;
    }
    $zh_desc = trim((string) $options->seoDescription);
    if ($zh_desc === '') {
        $zh_desc = $zh_home_desc;
    }
    $zh_keywords = trim((string) $options->seoKeywords);
} elseif ($zh_is_single) {
    $zh_title = (string) $this->title . ' - ' . $siteName;

    $zh_fields = $this->fields;
    $zh_desc = ($zh_fields && isset($zh_fields->description)) ? trim((string) $zh_fields->description) : '';
    if ($zh_desc === '') {
        $zh_desc = zh_substr(trim(preg_replace('/\s+/u', ' ', strip_tags((string) $this->content))), 110);
    }

    $zh_keywords = ($zh_fields && isset($zh_fields->keywords)) ? trim((string) $zh_fields->keywords) : '';
    if ($zh_keywords === '') {
        ob_start();
        $this->tags(',', false, '');
        $zh_keywords = trim(ob_get_clean(), " \t\n\r\0\x0B,");
    }
} else {
    $zh_title = ($zh_arch_name !== '' ? $zh_arch_name . ' - ' : '') . $siteName;
    $zh_desc = ($zh_arch_name !== '' ? $zh_arch_name . '，' : '') . zh_substr(trim((string) $options->description), 96);
    $zh_keywords = $zh_arch_name;
}

if ($zh_page > 1) {
    $zh_title .= ' - 第 ' . $zh_page . ' 页';
}

$zh_canonical = zh_current_url($this);
$zh_is_post = $zh_is_single && !$this->is('page');

/* 代码高亮资源按需加载：正文包含 <pre> 代码块时才输出 Prism（文章与独立页面） */
$zh_load_prism = $zh_is_single && stripos((string) $this->content, '<pre') !== false;
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
<meta charset="<?php $options->charset(); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?php echo htmlspecialchars($zh_title, ENT_QUOTES, 'UTF-8'); ?></title>
<?php $this->header('description=&keywords=&author=&generator=&template=&pingback=&wlw=&xmlrpc=&rss1=&atom='); ?>
<meta name="description" content="<?php echo htmlspecialchars($zh_desc, ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($zh_keywords !== ''): ?>
<meta name="keywords" content="<?php echo htmlspecialchars($zh_keywords, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>
<?php if ($zh_is_search): ?>
<meta name="robots" content="noindex, follow">
<?php endif; ?>
<link rel="canonical" href="<?php echo htmlspecialchars($zh_canonical, ENT_QUOTES, 'UTF-8'); ?>">

<?php $zh_favicon = zh_safe_url(trim((string) $options->faviconUrl)); ?>
<?php if ($zh_favicon !== '#'): ?>
<link rel="icon" href="<?php echo htmlspecialchars($zh_favicon, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>

<meta name="theme-color" content="#f7f8fa" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0f1216" media="(prefers-color-scheme: dark)">

<meta property="og:site_name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($zh_title, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($zh_canonical, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($zh_desc, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($zh_is_single ? zh_thumb_src($this) : ($options->defaultThumb ? $options->defaultThumb : $options->themeUrl . '/assets/img/default-thumb.svg'), ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($zh_is_post): ?>
<meta property="og:type" content="article">
<meta property="article:published_time" content="<?php echo date('c', $this->created); ?>">
<meta property="article:modified_time" content="<?php echo date('c', $this->modified); ?>">
<meta name="twitter:card" content="summary_large_image">
<?php else: ?>
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary">
<?php endif; ?>

<?php if ($zh_is_post): ?>
<script type="application/ld+json"><?php zh_json_ld(array(
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => (string) $this->title,
    'description' => $zh_desc,
    'image' => zh_thumb_src($this),
    'datePublished' => date('c', $this->created),
    'dateModified' => date('c', $this->modified),
    'author' => array('@type' => 'Person', 'name' => (string) $this->author->screenName),
    'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => $zh_canonical),
)); ?></script>
<script type="application/ld+json"><?php zh_json_ld(array(
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array(
        array('@type' => 'ListItem', 'position' => 1, 'name' => $siteName, 'item' => (string) $options->siteUrl),
        array('@type' => 'ListItem', 'position' => 2, 'name' => (string) $this->title),
    ),
)); ?></script>
<?php elseif ($zh_is_index): ?>
<script type="application/ld+json"><?php zh_json_ld(array(
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $siteName,
    'url' => (string) $options->siteUrl,
    'potentialAction' => array(
        '@type' => 'SearchAction',
        'target' => array('@type' => 'EntryPoint', 'urlTemplate' => rtrim((string) $options->siteUrl, '/') . '/search/{search_term_string}/'),
        'query-input' => 'required name=search_term_string',
    ),
)); ?></script>
<?php endif; ?>

<script>
(function () {
    var theme = null;
    try { theme = localStorage.getItem('zh-theme'); } catch (e) {}
    if (theme !== 'dark' && theme !== 'light') {
        theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    document.documentElement.setAttribute('data-theme', theme);
})();
</script>
<link rel="stylesheet" href="<?php $options->themeUrl('/assets/css/style.css'); ?>">
<?php if ($zh_load_prism): ?>
<link rel="stylesheet" href="<?php $options->themeUrl('/assets/vendor/prism/prism-okaidia.min.css'); ?>">
<?php endif; ?>
<?php echo trim((string) $options->customHead); ?>
</head>
<body>
<a class="zh-skip" href="#main">跳到主要内容</a>

<header class="zh-header" id="zh-header">
    <div class="zh-header-inner">
        <?php if ($zh_is_index): ?>
        <h1 class="zh-brand"><a href="<?php $options->siteUrl(); ?>"><?php $options->title(); ?></a></h1>
        <?php else: ?>
        <a class="zh-brand" href="<?php $options->siteUrl(); ?>"><?php $options->title(); ?></a>
        <?php endif; ?>

        <nav class="zh-nav" id="zh-nav" aria-label="主导航">
            <ul class="zh-menu">
                <li class="zh-item<?php echo ($zh_is_index && $zh_page < 2) || (zh_nav_active($options->siteUrl) && !$zh_is_single) ? ' active' : ''; ?>">
                    <a href="<?php $options->siteUrl(); ?>">首页</a>
                </li>
                <li class="zh-item zh-has-sub<?php echo $this->is('category') || zh_nav_active($zh_nav_categories) ? ' active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars(zh_safe_url($zh_nav_categories), ENT_QUOTES, 'UTF-8'); ?>">分类<svg class="zh-caret" viewBox="0 0 16 16" width="10" height="10" aria-hidden="true"><path d="M3 6l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    <ul class="zh-submenu">
                        <?php $this->widget('\Widget\Metas\Category\Rows@zhnav')->to($zh_cats); ?>
                        <?php if ($zh_cats->have()): ?>
                        <?php while ($zh_cats->next()): ?>
                        <li><a href="<?php $zh_cats->permalink(); ?>"><?php $zh_cats->name(); ?><span class="zh-submenu-count"><?php $zh_cats->count(); ?></span></a></li>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <li><a href="<?php $options->siteUrl(); ?>">暂无分类</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="zh-item<?php echo zh_nav_active($zh_nav_archives) ? ' active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars(zh_safe_url($zh_nav_archives), ENT_QUOTES, 'UTF-8'); ?>">归档</a>
                </li>
                <li class="zh-item<?php echo zh_nav_active($zh_nav_about) ? ' active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars(zh_safe_url($zh_nav_about), ENT_QUOTES, 'UTF-8'); ?>">关于</a>
                </li>
                <?php if ($zh_nav_links !== ''): ?>
                <li class="zh-item<?php echo zh_nav_active($zh_nav_links) ? ' active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars(zh_safe_url($zh_nav_links), ENT_QUOTES, 'UTF-8'); ?>">友链</a>
                </li>
                <?php endif; ?>
                <?php foreach ($zh_extra_nav as $zh_extra): ?>
                <li class="zh-item<?php echo zh_nav_active($zh_extra[1]) ? ' active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars(zh_safe_url($zh_extra[1]), ENT_QUOTES, 'UTF-8'); ?>"<?php if (preg_match('#^https?://#i', $zh_extra[1])): ?> target="_blank" rel="noopener"<?php endif; ?>><?php echo htmlspecialchars($zh_extra[0], ENT_QUOTES, 'UTF-8'); ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="zh-actions">
            <div class="zh-search" id="zh-search">
                <form method="post" action="<?php $options->siteUrl(); ?>" role="search">
                    <input type="text" name="s" placeholder="搜索文章…" aria-label="站内搜索">
                </form>
                <button type="button" class="zh-icon-btn" id="zh-search-btn" aria-label="搜索" aria-expanded="false">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3.6-3.6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            </div>
            <button type="button" class="zh-icon-btn" id="zh-theme-toggle" aria-label="切换深色模式">
                <svg class="zh-ico-sun" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><circle cx="12" cy="12" r="4.4" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 2.5v2.4M12 19.1v2.4M2.5 12h2.4M19.1 12h2.4M5.3 5.3l1.7 1.7M17 17l1.7 1.7M18.7 5.3L17 7M7 17l-1.7 1.7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <svg class="zh-ico-moon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M20.2 14.2A8.3 8.3 0 0 1 9.8 3.8a8.3 8.3 0 1 0 10.4 10.4z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            </button>
            <button type="button" class="zh-icon-btn zh-burger" id="zh-menu-btn" aria-label="菜单" aria-expanded="false" aria-controls="zh-nav">
                <svg class="zh-ico-bars" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <svg class="zh-ico-close" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        </div>
    </div>
</header>
