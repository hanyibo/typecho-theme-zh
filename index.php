<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * ZH · 简约单栏主题
 *
 * 一款单栏、简约技术博客主题：有图才显示的文章列表、深色模式、站内搜索、
 * 图片灯箱、代码高亮、归档/分类/友链页面模板、完整 SEO 支持。
 *
 * @package ZH
 * @author hanyb
 * @version 1.0.0
 * @link https://www.hanyibo.com
 */
$this->need('header.php');
?>
<main class="zh-main zh-main-list" id="main">
    <?php if (zh_cache_start('index')): else: ?>
    <?php if ($this->have()): ?>
    <div class="zh-post-list">
        <?php while ($this->next()): ?>
        <?php zh_cache_protect_row($this); ?>
        <?php $zh_thumb_fallback = false; $zh_thumb_url = zh_thumb_src($this, $zh_thumb_fallback); ?>
        <article class="zh-card<?php echo $zh_thumb_fallback ? ' zh-card-text-only' : ''; ?>">
            <?php if (!$zh_thumb_fallback): ?>
            <a class="zh-card-thumb" href="<?php $this->permalink() ?>" aria-hidden="true" tabindex="-1"><img class="zh-thumb" src="<?php echo htmlspecialchars($zh_thumb_url, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) $this->title, ENT_QUOTES, 'UTF-8'); ?>" width="160" height="100" loading="lazy" decoding="async"></a>
            <?php endif; ?>
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
