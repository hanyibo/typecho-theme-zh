<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<main class="zh-main" id="main">
    <article class="zh-post">
        <header class="zh-post-head">
            <h1 class="zh-post-title"><?php $this->title() ?></h1>
        </header>

        <div class="zh-content">
            <?php $this->content(); ?>
        </div>

        <?php $this->need('comments.php'); ?>
    </article>
</main>
<?php $this->need('footer.php'); ?>
