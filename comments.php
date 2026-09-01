<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/** 评论头像地址（cravatar.cn 国内镜像） */
function zh_gravatar_url($mail, $size = 88)
{
    return 'https://cravatar.cn/avatar/' . md5(strtolower(trim((string) $mail))) . '?s=' . (int) $size . '&d=mp';
}

/** 嵌套评论渲染回调（被 Typecho listComments 自动调用） */
function threadedComments($comments, $options)
{
    $classes = 'zh-comment';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $classes .= ' zh-comment-by-author';
        } else {
            $classes .= ' zh-comment-by-user';
        }
    }
    ob_start();
    $comments->alt(' zh-odd', ' zh-even');
    $classes .= ob_get_clean();

    echo '<li id="li-' . $comments->theId . '" class="' . $classes . '">';
    echo '<div class="zh-comment-avatar"><img src="' . zh_gravatar_url($comments->mail) . '" alt="" width="44" height="44" loading="lazy" decoding="async"></div>';
    echo '<div class="zh-comment-main">';
    echo '<div class="zh-comment-meta">';
    if ($comments->url) {
        /* 评论者填写的网址属于不可信输入，过滤危险协议后再生成为链接 */
        $zh_safe = zh_safe_url((string) $comments->url);
        if ($zh_safe !== '#') {
            echo '<a class="zh-comment-author" href="' . htmlspecialchars($zh_safe, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener nofollow">' . htmlspecialchars((string) $comments->author, ENT_QUOTES, 'UTF-8') . '</a>';
        } else {
            echo '<span class="zh-comment-author">' . htmlspecialchars((string) $comments->author, ENT_QUOTES, 'UTF-8') . '</span>';
        }
    } else {
        echo '<span class="zh-comment-author">' . htmlspecialchars((string) $comments->author, ENT_QUOTES, 'UTF-8') . '</span>';
    }
    echo '<time datetime="' . date('c', $comments->created) . '">' . date('Y-m-d H:i', $comments->created) . '</time>';
    echo '</div>';
    echo '<div class="zh-comment-content">';
    $comments->content();
    echo '</div>';
    echo '<div class="zh-comment-actions">';
    $comments->reply();
    echo '</div>';

    if ($comments->children) {
        echo '<div class="zh-comment-children">';
        $comments->threadedComments();
        echo '</div>';
    }
    echo '</div>';
    echo '</li>';
}
?>
<div id="comments" class="zh-comments">
    <h2 class="zh-comments-title" id="comments-title"><?php $this->commentsNum('暂无评论', '1 条评论', '%d 条评论'); ?></h2>

    <?php $this->comments()->to($comments); ?>
    <?php if ($comments->have()): ?>
    <ol class="zh-comment-list">
        <?php $comments->listComments(); ?>
    </ol>
    <?php $comments->pageNav('‹', '›'); ?>
    <?php endif; ?>

    <?php if ($this->allow('comment')): ?>
    <div class="zh-cancel-reply">
        <?php $comments->cancelReply(); ?>
    </div>
    <div id="<?php $this->respondId(); ?>" class="zh-respond">
        <h3 class="zh-respond-title">发表评论</h3>
        <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
            <?php if ($this->user->hasLogin()): ?>
            <p class="zh-respond-user">
                已登录为 <a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>
                <a class="zh-respond-logout" href="<?php $this->options->logoutUrl(); ?>">退出</a>
            </p>
            <?php else: ?>
            <div class="zh-form-row">
                <input type="text" name="author" id="author" placeholder="称呼 *" required value="<?php $this->remember('author'); ?>">
                <input type="email" name="mail" id="mail" placeholder="邮箱（不公开）<?php if ($this->options->commentsRequireMail): ?> *<?php endif; ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> value="<?php $this->remember('mail'); ?>">
                <input type="url" name="url" id="url" placeholder="网站<?php if ($this->options->commentsRequireURL): ?> *<?php endif; ?>"<?php if ($this->options->commentsRequireURL): ?> required<?php endif; ?> value="<?php $this->remember('url'); ?>">
            </div>
            <?php endif; ?>
            <textarea name="text" id="textarea" rows="5" placeholder="写下你的评论…" required><?php $this->remember('text'); ?></textarea>
            <div class="zh-form-actions">
                <button type="submit" class="zh-btn-submit">提交评论</button>
                <span class="zh-form-hint">Ctrl / ⌘ + Enter 快速提交</span>
            </div>
        </form>
    </div>
    <?php else: ?>
    <p class="zh-comments-closed">评论已关闭。</p>
    <?php endif; ?>
</div>
