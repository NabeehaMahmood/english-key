<?php
require_once __DIR__ . '/includes/header.php';

$testimonials = getDb()->query('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order')->fetchAll();
$googleUrl = getSetting('google_reviews_url');
$googleRating = getSetting('google_rating');
$googleCount = getSetting('google_review_count');
?>

<div class="phero">
  <div class="wrap reveal">
    <div class="kick">Student Reviews</div>
    <h1>What students <span class="hl">actually say.</span></h1>
    <?php if ($googleUrl): ?><p class="sub">Rated <?= e($googleRating) ?> <?= icon('star', 'icon star-icon') ?> from <?= e($googleCount) ?> genuine Google reviews.</p><?php endif; ?>
  </div>
</div>

<section>
  <div class="wrap">
    <div class="g3">
      <?php foreach ($testimonials as $t): ?>
        <div class="rcard reveal">
          <?php if ($t['category']): ?><span class="ntag"><?= e($t['category']) ?></span><?php endif; ?>
          <p>&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
          <div><b><?= e($t['name']) ?></b><br><span><?= e($t['source_label']) ?></span></div>
        </div>
      <?php endforeach; ?>
      <?php if (!$testimonials): ?><p>Reviews are coming soon.</p><?php endif; ?>
    </div>
    <?php if ($googleUrl): ?>
      <p style="margin-top:32px" class="reveal"><a class="btn btn-n" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See all reviews on Google</a></p>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
