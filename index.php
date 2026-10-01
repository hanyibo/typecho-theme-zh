<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
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
$this->need('header.php');
?>
<main class="zh-main zh-main-list" id="main">
    <header class="zh-home-head">
        <div class="zh-home-intro">
            <p class="zh-eyebrow"><?php echo _t('记录与分享'); ?></p>
            <h1 class="zh-home-title"><?php echo htmlspecialchars((string) $this->options->title, ENT_QUOTES, 'UTF-8'); ?></h1>
            <?php $zh_intro = trim((string) $this->options->description); ?>
            <?php if ($zh_intro !== ''): ?>
            <p class="zh-home-description"><?php echo htmlspecialchars($zh_intro, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>
        <a class="zh-home-feed" href="<?php echo htmlspecialchars((string) $this->options->feedUrl, ENT_QUOTES, 'UTF-8'); ?>">
            <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><circle cx="5" cy="19" r="1.5" fill="currentColor"/><path d="M4 11a9 9 0 0 1 9 9M4 4a16 16 0 0 1 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <?php echo _t('订阅更新'); ?>
        </a>
    </header>
    <div class="zh-section-head">
        <h2 class="zh-section-title"><?php echo _t('文章列表'); ?></h2>
        <span class="zh-section-count"><?php echo (int) $this->getTotal(); ?> <?php echo _t('篇文章'); ?></span>
    </div>
    <?php if (zh_cache_start('index')): else: ?>
    <?php if ($this->have()): ?>
    <div class="zh-post-list">
        <?php while ($this->next()): ?>
        <?php zh_cache_protect_row($this); ?>
        <?php $zh_thumb_fallback = false; $zh_thumb_url = zh_thumb_src($this, $zh_thumb_fallback); ?>
        <article class="zh-card<?php echo $zh_thumb_fallback ? ' zh-card-text-only' : ''; ?>">
            <?php if (!$zh_thumb_fallback): ?>
            <a class="zh-card-thumb" href="<?php $this->permalink() ?>" aria-hidden="true" tabindex="-1"><img class="zh-thumb" src="<?php echo htmlspecialchars($zh_thumb_url, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) $this->title, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async" /></a>
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
