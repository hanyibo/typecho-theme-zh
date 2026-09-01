<?php
/**
 * ZH · 简约单栏主题
 *
 * 一款单栏、简约精致的主题：左图右文卡片列表、深色模式、站内搜索、
 * 图片灯箱、代码高亮、归档/分类/友链页面模板、完整 SEO 支持。
 *
 * @package ZH
 * @author hanyb
 * @version 1.0.0
 * @link https://www.hanyibo.com
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<main class="zh-main" id="main">
    <?php if (zh_cache_start('index')): else: ?>
    <?php if ($this->have()): ?>
    <div class="zh-post-list">
        <?php while ($this->next()): ?>
        <?php zh_cache_protect_row($this); ?>
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
        <p>这里还没有文章。</p>
    </div>
    <?php endif; ?>

    <?php $this->pageNav('‹', '›'); ?>
    <?php zh_cache_end(); endif; ?>
</main>
<?php $this->need('footer.php'); ?>
