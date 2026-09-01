<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/** @var Widget_Archive $this */

$zh_options = $this->options;
$zh_is_post = $this->is('single') && !$this->is('page');
$zh_icp = trim((string) $zh_options->icp);
$zh_footer_text = trim((string) $zh_options->footerText);
?>
<footer class="zh-footer">
    <div class="zh-footer-inner">
        <p class="zh-footer-line">
            &copy; <?php echo date('Y'); ?> <?php $zh_options->title(); ?>
            <?php if ($zh_footer_text !== ''): ?>
            <span class="zh-footer-sep">·</span><?php echo htmlspecialchars($zh_footer_text, ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </p>
        <p class="zh-footer-line zh-footer-meta">
            <a href="<?php $zh_options->feedUrl(); ?>">RSS 订阅</a>
            <span class="zh-footer-sep">·</span>
            <a href="https://www.hanyibo.com" target="_blank" rel="noopener">ZH 主题</a>
            <span class="zh-footer-sep">·</span>
            <a href="https://typecho.org" target="_blank" rel="noopener">Powered by Typecho</a>
            <?php if ($zh_icp !== ''): ?>
            <span class="zh-footer-sep">·</span>
            <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener nofollow"><?php echo htmlspecialchars($zh_icp, ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endif; ?>
        </p>
    </div>
</footer>
</div><!-- /.zh-site -->

<button type="button" class="zh-backtop" id="zh-backtop" aria-label="返回顶部">
    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M12 19V6M6.5 11.5L12 6l5.5 5.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>

<script src="<?php $zh_options->themeUrl('/assets/js/main.js'); ?>" defer></script>
<?php if ($zh_is_post): ?>
<script src="<?php $zh_options->themeUrl('/assets/vendor/prism/prism-bundle.min.js'); ?>" defer></script>
<?php endif; ?>
<?php echo trim((string) $zh_options->customFooter); ?>
<?php $this->footer(); ?>
</body>
</html>
