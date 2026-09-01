<?php
/**
 * 归档
 *
 * 独立页面模板：按年份分组的时间轴归档。
 * 使用方法：新建独立页面 → 自定义模板选「归档」→ slug 建议设为 archives
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');

$zh_groups = array();
$zh_total = 0;
$this->widget('\Widget\Contents\Post\Recent@zharclist', 'pageSize=10000')->to($zh_posts);
while ($zh_posts->next()) {
    $zh_year = date('Y', $zh_posts->created);
    $zh_groups[$zh_year][] = array(
        'url'   => $zh_posts->permalink,
        'title' => (string) $zh_posts->title,
        'date'  => date('m-d', $zh_posts->created),
    );
    $zh_total++;
}

$zh_cat_count = 0;
$this->widget('\Widget\Metas\Category\Rows@zhcatcount')->to($zh_catw);
while ($zh_catw->next()) {
    $zh_cat_count++;
}
krsort($zh_groups);
?>
<main class="zh-main" id="main">
    <header class="zh-page-head">
        <h1 class="zh-page-title"><?php $this->title() ?></h1>
        <p class="zh-page-sub">共 <?php echo $zh_total; ?> 篇文章 · <?php echo $zh_cat_count; ?> 个分类</p>
    </header>

    <?php if (empty($zh_groups)): ?>
    <div class="zh-empty"><p>还没有发布文章。</p></div>
    <?php else: ?>
    <?php foreach ($zh_groups as $zh_year => $zh_items): ?>
    <section class="zh-timeline">
        <h2 class="zh-timeline-year"><?php echo $zh_year; ?> <span>· <?php echo count($zh_items); ?> 篇</span></h2>
        <ul class="zh-timeline-list">
            <?php foreach ($zh_items as $zh_item): ?>
            <li>
                <time class="zh-tl-date" datetime="<?php echo $zh_year; ?>-<?php echo $zh_item['date']; ?>"><?php echo $zh_item['date']; ?></time>
                <a href="<?php echo htmlspecialchars($zh_item['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($zh_item['title'], ENT_QUOTES, 'UTF-8'); ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endforeach; ?>
    <?php endif; ?>
</main>
<?php $this->need('footer.php'); ?>
