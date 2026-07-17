<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$db  = getDb();
$slug = trim($_GET['slug'] ?? '');

// ── 1. Fetch the requested post ──────────────────────────────────────────────
if ($slug !== '') {
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'");
    $stmt->execute([$slug]);
    $post = $stmt->fetch() ?: null;
} else {
    $post = null;
}

// ── Per-post <title>/meta description/keywords, read by header.php ──────────
// Every post is authored with the same standard fields (meta description,
// primary/secondary keywords), so this is the one place those fields turn
// into actual <head> tags — the same for every post, not a one-off.
if ($post) {
    $pageTitle = $post['title'];
    $pageDescription = !empty($post['meta_description']) ? $post['meta_description'] : $post['excerpt'];
    $pageKeywords = trim(implode(', ', array_filter([
        $post['primary_keyword'] ?? '',
        $post['secondary_keywords'] ?? '',
    ], fn($v) => trim((string)$v) !== '')));
}

// header.php's nav/logo/CSS links are relative (e.g. "assets/css/style.css"),
// which only resolve correctly when the browser's URL has one path segment
// (blog-post.php). This page is also reachable at /blog/<slug> (two
// segments, via .htaccess), so a <base> tag pointing back at the site root
// is injected here to keep those relative links working either way, without
// changing header.php itself.
ob_start();
require_once __DIR__ . '/includes/header.php';
$headerHtml = ob_get_clean();
$basePath = rtrim((string)parse_url(SITE_URL, PHP_URL_PATH), '/') . '/';
echo str_replace('<head>', '<head><base href="' . e($basePath) . '">', $headerHtml);

// ── 2. Hard 404 if not found ─────────────────────────────────────────────────
if (!$post) {
    http_response_code(404);
?>
<div class="phero phero-dark">
  <div class="wrap reveal" style="text-align:center;">
    <div class="kick">404 — Not Found</div>
    <h1>This article doesn&rsquo;t exist&thinsp;&mdash;<span class="hl">yet.</span></h1>
    <p class="sub">It may have been removed, unpublished, or the link could be wrong.</p>
    <div style="margin-top:28px;">
      <a class="btn btn-o" href="blog">&larr;&nbsp;Back to Blog</a>
    </div>
  </div>
</div>
<?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// ── 3. Reading time (rough: ~200 words/min) ───────────────────────────────────
$readingMins = blogReadingMinutes($post['content']);
$whatsapp    = getSetting('whatsapp_number');
?>

<?php /* ── Hero ─────────────────────────────────────────────────────────────── */ ?>
<div class="phero phero-dark">
  <div class="wrap reveal">
    <a href="blog" class="back-blog">&larr;&nbsp;All articles</a>
    <?php if ($post['category']): ?><div class="kick"><?= e($post['category']) ?></div><?php endif; ?>
    <h1><?= e($post['title']) ?></h1>
    <?php if ($post['excerpt']): ?><p class="sub"><?= e($post['excerpt']) ?></p><?php endif; ?>
  </div>
</div>

<main>
<?php if ($post['image']): ?>
  <div class="wrap">
    <div class="post-hero-img reveal">
      <img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>">
    </div>
  </div>
<?php endif; ?>

  <?php /* ── Article body ──────────────────────────────────────────────────── */ ?>
  <section style="padding-top:<?= $post['image'] ? '36px' : '56px' ?>;">
    <div class="wrap">
      <article class="article-body">
        <div class="abyline">
          <?php if ($post['category']): ?><span><?= e($post['category']) ?></span><span>&middot;</span><?php endif; ?>
          <span><?= $readingMins ?>&nbsp;min read</span>
        </div>

        <?= $post['content'] /* already sanitized by HTMLPurifier on save */ ?>

        <?php if (!$post['content']): ?>
          <p><em>This article has no content yet — check back soon.</em></p>
        <?php endif; ?>

        <div class="abyline" style="margin-top:40px;padding-top:24px;border-top:1px solid var(--line);justify-content:space-between">
          <a href="blog" class="vlink">&larr; Back to all articles</a>
          <a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Ask us on WhatsApp</a>
        </div>
      </article>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
