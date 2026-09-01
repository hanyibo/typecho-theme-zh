<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<main class="zh-main" id="main">
    <article class="zh-post">
        <header class="zh-post-head">
            <h1 class="zh-post-title"><?php $this->title() ?></h1>
            <div class="zh-post-meta">
                <span class="zh-post-author"><?php echo htmlspecialchars((string) $this->author->screenName, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="zh-dot" aria-hidden="true"></span>
                <time datetime="<?php echo date('c', $this->created); ?>"><?php $this->date('Y-m-d'); ?></time>
                <span class="zh-dot" aria-hidden="true"></span>
                <span><?php $this->category(',', false, '未分类'); ?></span>
                <span class="zh-dot" aria-hidden="true"></span>
                <a href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('暂无评论', '1 条评论', '%d 条评论'); ?></a>
            </div>
        </header>

        <div class="zh-content">
            <?php if (zh_cache_start('post', $this)): else: ?><?php $this->content(); ?><?php zh_cache_end(); endif; ?>
        </div>

        <footer class="zh-post-foot">
            <?php ob_start(); $this->tags(', ', true, ''); $zh_tags_html = trim(ob_get_clean()); ?>
            <?php if ($zh_tags_html !== ''): ?>
            <div class="zh-post-tags"><?php echo $zh_tags_html; ?></div>
            <?php endif; ?>

            <nav class="zh-post-nav" aria-label="上一篇下一篇">
                <div class="zh-post-nav-item zh-prev"><?php $this->thePrev('« %s', ''); ?></div>
                <div class="zh-post-nav-item zh-next"><?php $this->theNext('%s »', ''); ?></div>
            </nav>
        </footer>

        <?php $this->need('comments.php'); ?>
    </article>
</main>
<?php $this->need('footer.php'); ?>
