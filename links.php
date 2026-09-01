<?php
/**
 * 友情链接
 *
 * 独立页面模板：以卡片展示外观设置中维护的友链。
 * 使用方法：新建独立页面 → 自定义模板选「友情链接」→ 在外观设置「友情链接」中维护
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');

$zh_links = zh_parse_lines((string) $this->options->friendLinks, 2);
?>
<main class="zh-main" id="main">
    <header class="zh-page-head">
        <h1 class="zh-page-title"><?php $this->title() ?></h1>
        <?php if (!empty($zh_links)): ?>
        <p class="zh-page-sub">共 <?php echo count($zh_links); ?> 个站点</p>
        <?php endif; ?>
    </header>

    <?php if (empty($zh_links)): ?>
    <div class="zh-empty"><p>还没有添加友链，请在外观设置「友情链接」中按 名称|链接|描述 逐行填写。</p></div>
    <?php else: ?>
    <div class="zh-links-grid">
        <?php foreach ($zh_links as $zh_link): ?>
        <a class="zh-link-card" href="<?php echo htmlspecialchars($zh_link[1], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
            <span class="zh-link-avatar" aria-hidden="true"><?php echo htmlspecialchars(zh_first_char($zh_link[0]), ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="zh-link-info">
                <span class="zh-link-name"><?php echo htmlspecialchars($zh_link[0], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php if (isset($zh_link[2]) && $zh_link[2] !== ''): ?>
                <span class="zh-link-desc"><?php echo htmlspecialchars($zh_link[2], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (trim((string) $this->content) !== ''): ?>
    <div class="zh-content zh-page-content"><?php $this->content(); ?></div>
    <?php endif; ?>
</main>
<?php $this->need('footer.php'); ?>
