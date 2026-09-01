<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<main class="zh-main zh-404" id="main">
    <p class="zh-404-code" aria-hidden="true">404</p>
    <h1 class="zh-404-title">页面不存在</h1>
    <p class="zh-404-text">你访问的页面可能已被删除、改名或暂时不可用。</p>
    <p><a class="zh-btn" href="<?php $this->options->siteUrl(); ?>">返回首页</a></p>
</main>
<?php $this->need('footer.php'); ?>
