<?php
/**
 * 分类
 *
 * 独立页面模板：展示全部分类及文章数。
 * 使用方法：新建独立页面 → 自定义模板选「分类」→ slug 建议设为 categories
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<main class="zh-main" id="main">
    <?php if (zh_cache_start('page', $this)): else: ?>
    <header class="zh-page-head">
        <h1 class="zh-page-title"><?php $this->title() ?></h1>
    </header>

    <?php $this->widget('\Widget\Metas\Category\Rows@zhcats')->to($zh_cats); ?>
    <?php if ($zh_cats->have()): ?>
    <div class="zh-cat-grid">
        <?php while ($zh_cats->next()): ?>
        <a class="zh-cat-card" href="<?php $zh_cats->permalink(); ?>">
            <span class="zh-cat-name"><?php $zh_cats->name(); ?></span>
            <span class="zh-cat-count"><?php $zh_cats->count(); ?> 篇</span>
            <?php if (trim((string) $zh_cats->description) !== ''): ?>
            <span class="zh-cat-desc"><?php echo htmlspecialchars((string) $zh_cats->description, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
        </a>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="zh-empty"><p>还没有创建分类。</p></div>
    <?php endif; ?>

    <?php if (trim((string) $this->content) !== ''): ?>
    <div class="zh-content zh-page-content"><?php $this->content(); ?></div>
    <?php endif; ?>
    <?php zh_cache_end(); endif; ?>
</main>
<?php $this->need('footer.php'); ?>
