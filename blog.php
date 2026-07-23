<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'Blog';
$pageDescription = 'Exam tips, study routines and FBISE board updates from EnglishKeys Academy.';

// This page is also reachable at /blog/ (trailing slash, via .htaccess), a
// two-segment URL, so relative hrefs in the markup (nav links, "courses.php",
// etc.) need a <base> tag pointing back at the site root or the browser
// resolves them against /blog/ instead. CSS/JS/logo don't need this (they're
// root-absolute, see $assetBase in header.php) -- only the plain relative
// links throughout the markup do.
ob_start();
require_once __DIR__ . '/includes/header.php';
$headerHtml = ob_get_clean();
echo str_replace('<head>', '<head><base href="' . e($assetBase) . '">', $headerHtml);

$db = getDb();

// ── Posts query ───────────────────────────────────────────────────────────────
$posts = $db->query(
    "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC"
)->fetchAll();
?>

<?php renderPageHero('blog'); ?>

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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
