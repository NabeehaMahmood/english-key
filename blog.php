<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'Blog';
$pageDescription = 'Exam tips, study routines and FBISE board updates from EnglishKeys Academy.';

// header.php's nav/logo/CSS links are relative (e.g. "assets/css/style.css"),
// which only resolve correctly against a one-path-segment URL. This page is
// also reachable at /blog/ (trailing slash, via .htaccess), so a <base> tag
// pointing back at the site root is injected here to keep those links
// working from either URL, without changing header.php itself.
ob_start();
require_once __DIR__ . '/includes/header.php';
$headerHtml = ob_get_clean();
$basePath = rtrim((string)parse_url(SITE_URL, PHP_URL_PATH), '/') . '/';
echo str_replace('<head>', '<head><base href="' . e($basePath) . '">', $headerHtml);

$db = getDb();

// ── Posts query ───────────────────────────────────────────────────────────────
$posts = $db->query(
    "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC"
)->fetchAll();
?>

<?php /* ── Hero ─────────────────────────────────────────────────────────────── */ ?>
<div class="phero phero-dark">
  <div class="wrap reveal">
    <div class="kick">Blog</div>
    <h1>Exam tips, study routines &amp; <span class="hl">board updates.</span></h1>
    <p class="sub">Short, practical articles on exam technique and grammar, written to help FBISE students score higher. New pieces published through the term.</p>
  </div>
</div>

<section>
  <div class="wrap">
    <?php if ($posts): ?>
      <div class="blist">
        <?php foreach ($posts as $post):
          $excerpt = ($post['excerpt'] !== '' && $post['excerpt'] !== null)
            ? $post['excerpt']
            : mb_strimwidth(trim(strip_tags((string)$post['content'])), 0, 140, '...');

          $readingMins = blogReadingMinutes($post['content']);
        ?>
          <a class="barticle reveal" href="blog/<?= urlencode($post['slug']) ?>">
            <div class="barticle-meta">
              <?php if ($post['category']): ?><span><?= e($post['category']) ?></span><span class="bdot">&middot;</span><?php endif; ?>
              <span class="bmin"><?= $readingMins ?>&nbsp;min read</span>
            </div>
            <h3><?= e($post['title']) ?></h3>
            <p><?= e($excerpt) ?></p>
            <span class="barticle-cta">Read article &rarr;</span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="sub" style="text-align:center;padding:60px 20px;">Articles are on their way. Check back soon.</p>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
