<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();
$subjects = $db->query("SELECT * FROM courses WHERE category = 'subject' AND is_active = 1 ORDER BY sort_order LIMIT 4")->fetchAll();
$trackRecords = getTrackRecords();
$testimonials = $db->query('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order LIMIT 3')->fetchAll();
$featured = $db->query("SELECT * FROM courses WHERE category = 'featured' AND is_active = 1 ORDER BY sort_order LIMIT 1")->fetch();

$googleUrl = getSetting('google_reviews_url');
$googleRating = getSetting('google_rating');
$googleCount = getSetting('google_review_count');
$aboutQuote = getContentBlock('home', 'quote');

$whyCards = $db->query('SELECT * FROM home_why_cards WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();

$heroCta1Label = getSetting('hero_cta1_label', 'Explore Courses');
$heroCta1Link = getSetting('hero_cta1_link', 'courses.php');
$heroCta2Label = getSetting('hero_cta2_label', 'See Our Results');
$heroCta2Link = getSetting('hero_cta2_link', '#results');

$trackHeading = getContentBlock('home', 'track_record_heading')['content'] ?: 'Three years. Three first positions.';
$trackParts = explode('. ', $trackHeading);
$trackLast = array_pop($trackParts);
$trackRest = $trackParts ? implode('. ', $trackParts) . '. ' : '';
$trackDescription = getContentBlock('home', 'track_record_description')['content'] ?: 'Not testimonials, verifiable federal board results.';
$trackBgImage = getContentBlock('home', 'track_record_bg')['image_path'] ?? null;

$foundersHeading = getContentBlock('home', 'founders_heading')['content'] ?: 'Founders’ Vision';
$foundersTeacherId = (int) getSetting('founders_vision_teacher_id');
$foundersTeacher = null;
if ($foundersTeacherId > 0) {
    $stmt = $db->prepare('SELECT * FROM teachers WHERE id = ? AND is_active = 1');
    $stmt->execute([$foundersTeacherId]);
    $foundersTeacher = $stmt->fetch();
}
if (!$foundersTeacher) {
    $foundersTeacher = $db->query('SELECT * FROM teachers WHERE is_active = 1 ORDER BY sort_order, id LIMIT 1')->fetch();
}

$whyHeading = getContentBlock('home', 'why_heading')['content'] ?: 'A planned, year-round path from foundation to final paper.';
?>
<main class="page-home">
<section class="hero">
  <div class="wrap hg">
    <div class="reveal">
      <div class="kick"><?= e(getSetting('kicker')) ?></div>
      <?php
        $heroTitle = getSetting('hero_title', 'Where words build futures.');
        $heroWords = explode(' ', $heroTitle);
        $heroLastWord = array_pop($heroWords);
      ?>
      <h1><?= e(implode(' ', $heroWords)) ?> <span class="hl hl-o"><?= e($heroLastWord) ?></span></h1>
      <p class="sub"><?= e(getSetting('hero_subtitle')) ?></p>
      <div class="hctas">
        <a class="btn btn-o ar" href="<?= e($heroCta1Link) ?>"><?= e($heroCta1Label) ?>&nbsp;</a>
        <a class="btn btn-l" href="<?= e($heroCta2Link) ?>"><?= e($heroCta2Label) ?></a>
      </div>
      <p class="micro"><?= e(getSetting('hero_micro')) ?></p>
    </div>
    <div class="reveal">
      <div class="pw">
        <div class="pbars" aria-hidden="true"><span class="pb1"></span><span class="pb2"></span><span class="pb3"></span></div>
        <?php
          $heroPhotoName = getSetting('hero_photo_name', 'Mr. Naeem Haider');
          $heroPhotoRole = getSetting('hero_photo_role', 'Co-Founder & Lead Instructor');
        ?>
        <div class="pframe"><img src="<?= e(getSetting('hero_image')) ?>" alt="<?= e($heroPhotoName) ?>"></div>
        <div class="pr"><?= e($heroPhotoName) ?><small><?= e($heroPhotoRole) ?></small></div>
      </div>
    </div>
  </div>
</section>

<?php renderHomeStatsBand(); ?>

<?php if ($subjects): ?>
<section id="courses">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Our Courses</div>
      <h2 class="t">Four subjects. One standard: <span class="hl">first position.</span></h2>
      <p class="sub">Complete FBISE preparation for Classes 9-12, from the first lecture to the final board paper.</p>
    </div>
    <div class="g2">
      <?php foreach ($subjects as $i => $s): ?>
        <div class="card scard reveal"<?= revealDelay($i) ?> style="--c:<?= e($s['accent_color']) ?>">
          <div class="num" style="color:<?= e($s['accent_color']) ?>">0<?= (int)$s['sort_order'] ?>, <?= e($s['level']) ?></div>
          <h3><?= e($s['title']) ?></h3>
          <p><?= e($s['description']) ?></p>
          <?php if ($s['tag_line']): ?>
            <div class="tags"><?php foreach (explode(' - ', $s['tag_line']) as $tag): ?><span class="tag"><?= e($tag) ?></span><?php endforeach; ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:26px" class="reveal"><a class="vlink" href="courses.php">View all courses →</a></p>
  </div>
</section>
<?php endif; ?>

<?php if ($trackRecords): ?>
<section class="dark trackrecord" id="results">
  <div class="tr-bg" aria-hidden="true">
    <?php if ($trackBgImage): ?>
      <img src="<?= e($trackBgImage) ?>" alt="">
    <?php else: ?>
      <?= icon('trophy', 'tr-icon tr-i1') ?><?= icon('medal', 'tr-icon tr-i2') ?><?= icon('award', 'tr-icon tr-i3') ?><?= icon('trophy', 'tr-icon tr-i4') ?>
    <?php endif; ?>
  </div>
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Proven Track Record</div>
      <h2 class="t"><?= e($trackRest) ?><span class="hl"><?= e($trackLast) ?></span></h2>
      <p class="sub"><?= e($trackDescription) ?></p>
    </div>
    <div class="g3 reveal">
      <?php foreach ($trackRecords as $i => $r): ?>
        <?= renderTrackRecordCard($r, 'tcard reveal', revealDelay($i)) ?>
      <?php endforeach; ?>
    </div>
    <?php if ($googleUrl): ?>
    <div class="gbar reveal">
      <span class="big"><?= e($googleRating) ?><?= icon('star', 'icon star-icon') ?></span><span class="st"><?= str_repeat(' '.icon('star', 'icon star-icon'), 5) ?></span><small><?= e($googleCount) ?> Google reviews</small>
      <p>Rated <?= e($googleRating) ?><?= icon('star', 'icon star-icon') ?> by <?= e($googleCount) ?> students, parents and teachers on Google.</p>
      <a class="btn btn-w ar" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See Reviews&nbsp;</a>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($aboutQuote['content'] && $foundersTeacher): ?>
<section class="soft fv">
  <div class="wrap">
    <div class="reveal fv-center">
      <div class="kick"><?= e($foundersHeading) ?></div>
      <p class="fv-quote">&ldquo;<?= e($aboutQuote['content']) ?>&rdquo;</p>
      <div class="fv-by"><b><?= e($foundersTeacher['name']) ?></b><?php if (!empty($foundersTeacher['role_title'])): ?><span><?= e($foundersTeacher['role_title']) ?></span><?php endif; ?></div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($whyCards): ?>
<section class="why">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Why EnglishKeys</div>
      <h2 class="t"><?= e($whyHeading) ?></h2>
    </div>
    <div class="g3" style="margin-top:34px">
      <?php foreach ($whyCards as $i => $card): ?>
        <div class="card reveal"<?= revealDelay($i) ?>>
          <div class="why-icon"><?= icon($card['icon'] ?: 'grad-cap', '') ?></div>
          <h3 style="font-size:18px;margin-bottom:8px"><?= e($card['title']) ?></h3>
          <p style="color:var(--muted);font-size:14px"><?= e($card['description']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($testimonials): ?>
<section class="soft" id="student-stories">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Student Stories</div>
      <h2 class="t">In their own words.</h2>
      <p class="sub">Genuine, permission-granted reviews from students and parents across Pakistan.</p>
    </div>
    <div class="g3">
      <?php foreach ($testimonials as $i => $t): ?>
        <div class="rcard reveal"<?= revealDelay($i) ?>>
          <div class="stars"><?= starRow((int)($t['rating'] ?: 5)) ?></div>
          <p>&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
          <div><b><?= e($t['name']) ?></b><br><span><?= e($t['source_label']) ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:34px;display:flex;gap:14px;flex-wrap:wrap" class="reveal">
      <?php if ($googleUrl): ?><a class="btn btn-n ar" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See Reviews on Google&nbsp;</a><?php endif; ?>
      <a class="btn btn-l ar" href="testimonials.php">See All Testimonials&nbsp;</a>
    </p>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>

<?php
$fcPopupBg = getContentBlock('home', 'fc_popup_bg')['image_path'] ?? null;
$fcPopupWidth = (int) getSetting('fc_popup_card_width', '430') ?: 430;
$fcPopupBtn1Label = getSetting('fc_popup_btn1_label', 'Enroll Now');
$fcPopupBtn1Link = getSetting('fc_popup_btn1_link', 'contact.php');
$fcPopupBtn2Label = getSetting('fc_popup_btn2_label', 'See All Courses');
$fcPopupBtn2Link = getSetting('fc_popup_btn2_link', 'courses.php');
?>
<?php if ($featured): ?>
<!-- A4: featured-course popup. Rendered only when an active featured course exists,
     so the JS in assets/js/site.js simply finds nothing on pages without it. -->
<div class="fc-pop" id="fcPop" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="fcTitle">
  <div class="fc-back" data-fc-close></div>
  <div class="card fcard fc-card<?= $fcPopupBg ? ' fc-card-bg' : '' ?>" style="max-width:<?= $fcPopupWidth ?>px<?= $fcPopupBg ? ";background-image:linear-gradient(rgba(255,255,255,.93),rgba(255,255,255,.93)),url('" . e($fcPopupBg) . "')" : '' ?>">
    <button class="fc-x" type="button" data-fc-close aria-label="Close popup">&times;</button>
    <span class="fbadge">Enrolling Now</span>
    <h3 class="ptitle" id="fcTitle"><?= e($featured['title']) ?><?php if (!empty($featured['tag_line'])): ?>, <span class="hl"><?= e($featured['tag_line']) ?></span><?php endif; ?></h3>
    <?php if (!empty($featured['description'])): ?>
      <p class="pdesc"><?= e($featured['description']) ?></p>
    <?php endif; ?>
    <?php
      // same detail order and icons as the featured card on courses.php
      $fcRows = [
        ['calendar', $featured['duration'] ?? ''],
        ['person',   $featured['eligibility'] ?: ($featured['level'] ?? '')],
        ['book',     $featured['mode'] ?? ''],
        ['card',     $featured['price'] ?? ''],
        ['ticket',   $featured['seats_info'] ?? ''],
      ];
      $fcMeta = '';
      foreach ($fcRows as [$fcIcon, $fcVal]) {
        if ($fcVal !== '' && $fcVal !== null) {
          $fcMeta .= '<span>' . icon($fcIcon) . ' ' . e($fcVal) . '</span>';
        }
      }
    ?>
    <?php if ($fcMeta): ?><div class="meta"><?= $fcMeta ?></div><?php endif; ?>
    <div class="fcta">
      <a class="btn btn-o" href="<?= e($fcPopupBtn1Link) ?>"><?= e($fcPopupBtn1Label) ?></a>
      <a class="btn btn-l" href="<?= e($fcPopupBtn2Link) ?>"><?= e($fcPopupBtn2Label) ?></a>
    </div>
  </div>
</div>
<?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
