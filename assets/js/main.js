/* ============================================================
   ZH · 简约单栏主题 —— 前端交互（无依赖 vanilla JS）
   深色切换 / 移动菜单 / 搜索展开 / 返回顶部 / 灯箱 / 代码复制
   ============================================================ */
(function () {
    'use strict';

    var root = document.documentElement;
    var header = document.getElementById('zh-header');
    var backtop = document.getElementById('zh-backtop');

    /* ---------- 深色模式切换 ---------- */
    var themeToggle = document.getElementById('zh-theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('zh-theme', next); } catch (e) { /* 隐私模式忽略 */ }
        });
    }

    /* ---------- 滚动状态：头部投影 + 返回顶部 ---------- */
    function onScroll() {
        var y = window.scrollY || window.pageYOffset || 0;
        if (header) {
            header.classList.toggle('zh-scrolled', y > 8);
        }
        if (backtop) {
            backtop.classList.toggle('show', y > 480);
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (backtop) {
        backtop.addEventListener('click', function () {
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
        });
    }

    /* ---------- 移动端菜单 ---------- */
    var menuBtn = document.getElementById('zh-menu-btn');
    if (menuBtn && header) {
        menuBtn.addEventListener('click', function () {
            var open = header.classList.toggle('zh-nav-open');
            menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        document.addEventListener('click', function (e) {
            if (header.classList.contains('zh-nav-open') && !header.contains(e.target)) {
                header.classList.remove('zh-nav-open');
                menuBtn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && header.classList.contains('zh-nav-open')) {
                header.classList.remove('zh-nav-open');
                menuBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* 移动端：点击「分类」先展开子菜单 */
    document.querySelectorAll('.zh-has-sub > a').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!window.matchMedia('(max-width: 960px)').matches) {
                return;
            }
            var li = link.parentElement;
            if (!li.classList.contains('open')) {
                e.preventDefault();
                li.classList.add('open');
            }
        });
    });

    /* ---------- 搜索框展开 ---------- */
    var search = document.getElementById('zh-search');
    var searchBtn = document.getElementById('zh-search-btn');
    if (search && searchBtn) {
        searchBtn.addEventListener('click', function () {
            var open = search.classList.toggle('zh-search-open');
            searchBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) {
                var input = search.querySelector('input');
                if (input) {
                    input.focus();
                }
            }
        });

        document.addEventListener('click', function (e) {
            if (search.classList.contains('zh-search-open') && !search.contains(e.target)) {
                search.classList.remove('zh-search-open');
                searchBtn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && search.classList.contains('zh-search-open')) {
                search.classList.remove('zh-search-open');
                searchBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* ---------- 图片灯箱 ---------- */
    var lightbox = null;

    function closeLightbox() {
        if (lightbox && lightbox.parentNode) {
            lightbox.parentNode.removeChild(lightbox);
        }
        lightbox = null;
        document.removeEventListener('keydown', onLightboxKey);
    }

    function onLightboxKey(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    }

    function openLightbox(src, alt) {
        closeLightbox();
        lightbox = document.createElement('div');
        lightbox.className = 'zh-lightbox';
        lightbox.setAttribute('role', 'dialog');
        lightbox.setAttribute('aria-label', '图片查看');

        var img = document.createElement('img');
        img.src = src;
        img.alt = alt || '';
        lightbox.appendChild(img);

        lightbox.addEventListener('click', closeLightbox);
        document.body.appendChild(lightbox);
        document.addEventListener('keydown', onLightboxKey);
    }

    document.addEventListener('click', function (e) {
        var img = e.target && e.target.tagName === 'IMG' ? e.target : null;
        if (!img) {
            return;
        }
        var content = img.closest('.zh-content');
        if (!content || img.closest('.zh-lightbox')) {
            return;
        }
        /* 图片外面包了链接：链接指向图片则拦截预览，否则交给链接 */
        var link = img.closest('a');
        if (link) {
            var href = link.getAttribute('href') || '';
            var isImageHref = /^data:image/i.test(href)
                || /\.(png|jpe?g|gif|webp|avif|svg|bmp)(\?|#|$)/i.test(href);
            if (!isImageHref) {
                return;
            }
        }
        var src = img.currentSrc || img.src;
        if (!src) {
            return;
        }
        e.preventDefault();
        openLightbox(src, img.alt);
    });

    /* ---------- 代码块复制按钮 ---------- */
    function copyText(text, done) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () { done(true); }, function () { done(false); });
            return;
        }
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        var ok = false;
        try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
        document.body.removeChild(textarea);
        done(ok);
    }

    document.querySelectorAll('.zh-content pre').forEach(function (pre) {
        if (pre.querySelector('.zh-copy-btn')) {
            return;
        }
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'zh-copy-btn';
        btn.textContent = '复制';
        btn.setAttribute('aria-label', '复制代码');
        btn.addEventListener('click', function () {
            copyText(pre.innerText, function (ok) {
                btn.textContent = ok ? '已复制' : '复制失败';
                btn.classList.toggle('zh-copied', ok);
                setTimeout(function () {
                    btn.textContent = '复制';
                    btn.classList.remove('zh-copied');
                }, 1600);
            });
        });
        pre.appendChild(btn);
    });
})();
