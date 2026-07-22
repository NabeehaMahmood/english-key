<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();
$googleUrl = getSetting('google_reviews_url');
$googleRating = getSetting('google_rating');
$googleCount = getSetting('google_review_count');

$categories = $db->query('SELECT * FROM testimonial_categories WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();

$allTestimonials = $db->query('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();
$byCategory = [];
foreach ($allTestimonials as $t) {
    $byCategory[$t['category_id']][] = $t;
}
// Only show tabs that actually have reviews, so the filter bar never opens on an empty panel.
$categories = array_values(array_filter($categories, static function ($cat) use ($byCategory) {
    return !empty($byCategory[$cat['id']]);
}));

$trackRecords = getTrackRecords();

$testimonialsSubtitle = $googleUrl
    ? sprintf('Every quote on this page is a genuine, permission-granted review from students, parents and alumni, part of our %s★ rating from %s Google reviews.', $googleRating, $googleCount)
    : '';
?>
<main class="page-testimonials">
<?php renderPageHero('testimonials', ['subtitle' => $testimonialsSubtitle]); ?>

<?php if ($categories): ?>
  <div class="wrap" style="margin:38px auto 8px">
    <div class="pfilter reveal" role="tablist" aria-label="Filter reviews">
      <?php foreach ($categories as $i => $cat): ?>
        <button type="button" class="pf-btn<?= $i === 0 ? ' is-on' : '' ?>" data-filter="cat-<?= (int)$cat['id'] ?>"><?= e($cat['name']) ?></button>
      <?php endforeach; ?>
    </div>
  </div>

  <?php foreach ($categories as $i => $cat): ?>
    <section class="tpanel<?= $i === 0 ? ' is-on' : '' ?><?= $i % 2 === 1 ? ' soft' : '' ?>" data-tab="cat-<?= (int)$cat['id'] ?>" id="tp-cat-<?= (int)$cat['id'] ?>">
      <div class="wrap">
        <div class="reveal">
          <div class="kick"><?= e($cat['name']) ?></div>
          <h2 class="t"><?= e($cat['heading']) ?></h2>
          <?php if ($cat['sub_text']): ?><p class="sub"><?= e($cat['sub_text']) ?></p><?php endif; ?>
        </div>
        <div class="<?= $cat['card_style'] === 'parent' ? 'g2' : 'g3' ?>"<?= $cat['sub_text'] ? '' : ' style="margin-top:30px"' ?>>
          <?php foreach ($byCategory[$cat['id']] as $ti => $t): ?>
            <?= renderTestimonialCard($t, $cat['card_style'], $ti) ?>
          <?php endforeach; ?>
        </div>
        <?php if ($cat['cta_label'] && $googleUrl): ?>
          <p style="margin-top:32px" class="reveal"><a class="btn btn-n ar" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener"><?= e($cat['cta_label']) ?>&nbsp;</a></p>
        <?php endif; ?>
      </div>
    </section>
  <?php endforeach; ?>
<?php else: ?>
  <section>
    <div class="wrap"><p>Reviews are coming soon.</p></div>
  </section>
<?php endif; ?>

<?php if ($trackRecords): ?>
<section class="dark" id="alumnus-corner">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Alumnus Corner</div>
      <h2 class="t">Once EnglishKeys, <span class="hl">always EnglishKeys.</span></h2>
      <p class="sub">Our alumni carry the academy&rsquo;s standard into medical colleges, universities and careers. This corner belongs to them, their journeys, milestones and advice for the students following behind.</p>
    </div>
    <div class="g3 reveal">
      <?php foreach ($trackRecords as $i => $r): ?>
        <?= renderTrackRecordCard($r, 'tcard reveal', revealDelay($i)) ?>
      <?php endforeach; ?>
    </div>
    <div class="gbar reveal" style="margin-top:26px">
      <p style="min-width:260px">Are you an EnglishKeys alumnus? Share your experience, your result, or a word of advice for current students, we&rsquo;ll feature your story here in the Alumnus Corner.</p>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <a class="btn btn-o ar" href="alumni.php#share">Share your story&nbsp;</a>
        <a class="btn btn-w" href="alumni.php">Visit the Alumnus Corner</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
