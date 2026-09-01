<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');

ob_start();
$this->archiveTitle(array(
    'category' => _t('分类「%s」'),
    'tag'      => _t('标签「%s」'),
    'search'   => _t('搜索「%s」'),
    'date'     => _t('「%s」的存档'),
    'author'   => _t('「%s」的文章'),
), '', '');
$zh_arch_name = trim(ob_get_clean());
?>
<main class="zh-main" id="main">
    <header class="zh-page-head">
        <h1 class="zh-page-title"><?php echo $zh_arch_name !== '' ? htmlspecialchars($zh_arch_name, ENT_QUOTES, 'UTF-8') : '归档'; ?></h1>
        <p class="zh-page-sub">共 <?php echo (int) $this->getTotal(); ?> 篇<?php echo $this->is('search') ? ' 相关内容' : ' 文章'; ?></p>
    </header>

    <?php if ($this->have()): ?>
    <div class="zh-post-list">
        <?php while ($this->next()): ?>
        <article class="zh-card">
            <a class="zh-card-thumb" href="<?php $this->permalink() ?>" aria-hidden="true" tabindex="-1"><?php zh_thumb($this); ?></a>
            <div class="zh-card-body">
                <h2 class="zh-card-title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
                <p class="zh-card-excerpt"><?php $this->excerpt(90, '…'); ?></p>
                <div class="zh-card-meta">
                    <time datetime="<?php echo date('c', $this->created); ?>"><?php $this->date('Y-m-d'); ?></time>
                    <span class="zh-dot" aria-hidden="true"></span>
                    <span class="zh-card-cat"><?php $this->category(',', false, '未分类'); ?></span>
                    <span class="zh-dot" aria-hidden="true"></span>
                    <a class="zh-card-comments" href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('抢沙发', '1 条评论', '%d 条评论'); ?></a>
                </div>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="zh-empty">
        <p>没有找到相关内容，换个关键词试试？</p>
    </div>
    <?php endif; ?>

    <?php $this->pageNav('‹', '›'); ?>
</main>
<?php $this->need('footer.php'); ?>
