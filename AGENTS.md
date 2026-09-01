# AGENTS.md — ZH Typecho 主题

## 项目定位

「ZH」是一款**简约单栏 Typecho 博客主题**（当前 1.0.0，作者 hanyb，演示站 https://www.hanyibo.com）。纯 PHP 模板 + 原生 CSS/JS，目标环境 **Typecho 1.3 / PHP 8.0+ / MySQL 8.0+**。README.md（中文）是安装、外观设置、自定义字段的权威文档。

## 目录与文件

- 根目录平铺全部 Typecho 模板：`index.php`（首页列表）、`header.php`（SEO head + 导航）、`footer.php`（页脚 + 脚本）、`post.php`/`page.php`/`archive.php`/`comments.php`/`404.php`，以及三个自定义页面模板 `categories.php`/`archives.php`/`links.php`。
- `functions.php`：主题设置（`themeConfig`）、文章自定义字段（`themeFields`）、钩子（`themeInit`）和全部 `zh_*` 辅助函数。新增辅助函数放这里。
- `assets/css/style.css`：**唯一**样式表（约 1400 行，明暗双色板）。`assets/js/main.js`：唯一交互脚本。`assets/vendor/prism/`：本地内置 Prism 1.29.0（代码高亮）。

## 构建与验证

无 package.json / composer / 测试 / CI。改动即部署（拷贝整个 `ZH/` 到站点 `usr/themes/`）。改 PHP 后用 `php -l <file>` 做语法检查；前端改动手动在浏览器验证明暗两套主题与移动端表现。

## 硬性约定（改代码前必读）

- **零外部 CDN / 零运行时依赖**：不引入 jQuery、不引外部字体/脚本/样式。第三方库必须本地放入 `assets/vendor/`。
- **每个 PHP 模板第一行必须是** `if (!defined('__TYPECHO_ROOT_DIR__')) exit;`。
- 命名前缀：PHP 函数 `zh_`；CSS 类 `zh-`；CSS 变量 `--zh-`；JS 内 `zh-` id/data 属性。深色模式靠 `[data-theme="dark"]` 切换 CSS 变量，新增样式必须同时覆盖两种主题。
- JS 为 ES5 风格 IIFE + `'use strict'`（无构建步骤，不使用 ES module/箭头函数等新语法时保持与现有代码一致）。
- 所有动态输出用 `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` 转义；JSON-LD 一律经 `zh_json_ld()` 输出（内部会转义 `</`）。
- 面向用户的文案用中文，可翻译字符串包 `_t()`。
- **不依赖 mbstring**：UTF-8 截断等字符串操作用 `zh_substr()`/`preg_*`（见 functions.php 现有写法）。

## 页面缓存（zh_cache_*，functions.php 末尾）

- 模板接入方式：`<?php if (zh_cache_start('<scope>')): else: ?>` … 缓存区域 … `<?php zh_cache_end(); endif; ?>`。scope 取值 `index`/`archive`/`post`/`page`，对应四个独立外观设置开关（`cacheIndex`/`cacheArchive`/`cachePost`/`cachePage`，默认全关）。
- **缓存区域必须排除评论区与评论表单**（comments.php 永远在缓存区外实时渲染）；登录用户、非 GET 请求自动跳过。
- 失效机制靠 `zh_cache_fingerprint()` 内容指纹（文章篇数/modified、评论总数、分类标签、页面 modified、主题设置快照），**不使用编辑钩子**——Typecho 1.3 后台请求不加载主题 functions.php，钩子注册不可靠（已查证源码：仅 `Widget\Archive::execute()` 前端渲染时加载）。
- 缓存文件写在 `usr/cache/ZH-theme/`（勿提交到 git）；新增开关或新增影响页面输出的设置项时，记得同步更新指纹的 `theme=` 哈希输入。

## Typecho 陷阱

- 在普通函数（非模板 `$this` 作用域）里取全局配置必须用 `\Typecho\Widget::widget('\Widget\Options')` 静态获取；`$widget->options` 是 protected，类外 `__get` 会返回 null（见 `zh_current_url()` 注释）。
- SEO 是本主题卖点：每页唯一 `<h1>`、独立 title/description/keywords、`rel=canonical`、Open Graph、JSON-LD、搜索页 `noindex`。改动 header.php 时保持这些输出完整。
- 导航「分类/归档/关于」链接靠 slug（`categories`/`archives`/`about`）自动匹配页面，回退到 `themeConfig` 设置——逻辑在 header.php 顶部的 `$zh_pagesMap`。
- 正文过滤（懒加载、外链新窗口）走 `themeInit` → `zh_filter_content()`，只对 single 页生效。
