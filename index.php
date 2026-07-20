<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();
$subjects = $db->query("SELECT * FROM courses WHERE category = 'subject' AND is_active = 1 ORDER BY sort_order LIMIT 4")->fetchAll();
$achievers = $db->query('SELECT * FROM alumni WHERE is_active = 1 ORDER BY sort_order LIMIT 3')->fetchAll();

// A3: single query, 3 testimonials total (parent quotes now live on testimonials.php only).
// Use sort_order in admin -> Testimonials to control which 3 surface here.
$testimonials = $db->query('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order LIMIT 3')->fetchAll();

// A4: the homepage popup shows the FIRST featured course.
// courses.php now lists every featured course (its brief removed the limit),
// so the popup deliberately keeps LIMIT 1 and takes the lowest sort_order.
// Agreed with the courses/alumni teammate; see the PR description.
$featured = $db->query("SELECT * FROM courses WHERE category = 'featured' AND is_active = 1 ORDER BY sort_order LIMIT 1")->fetch();

$statLearners = getSetting('stat_learners');
$statPositions = getSetting('stat_positions');
$statYears = getSetting('stat_years');
$statSince = getSetting('stat_since');
$statYoutube = getSetting('stat_youtube_subs');
$googleUrl = getSetting('google_reviews_url');
$googleRating = getSetting('google_rating');
$googleCount = getSetting('google_review_count');
$aboutQuote = getContentBlock('about', 'quote');
?>

<section class="hero">
  <div class="floaties" aria-hidden="true">
    <span class="fl fl1"></span><span class="fl fl2"></span><span class="fl fl3"></span><span class="fl fl4"></span>
  </div>
  <?php if ($googleRating): ?><span class="hb hb1" data-depth="20"><?= e($googleRating) ?><?= icon('star', 'icon star-icon') ?> Google</span><?php endif; ?>
  <?php if ($statLearners): ?><span class="hb hb2" data-depth="-16"><?= e($statLearners) ?> learners</span><?php endif; ?>
  <?php if ($statPositions): ?><span class="hb hb3" data-depth="12"><?= e($statPositions) ?> 1st position</span><?php endif; ?>

  <div class="wrap hg">
    <div class="reveal">
      <div class="kick"><?= e(getSetting('kicker')) ?></div>
      <?php
        $heroTitle = getSetting('hero_title', 'Where words build futures.');
        $heroWords = explode(' ', $heroTitle);
        $heroLastWord = array_pop($heroWords);
      ?>
      <h1><?= e(implode(' ', $heroWords)) ?> <span class="hl"><?= e($heroLastWord) ?></span></h1>
      <p class="sub"><?= e(getSetting('hero_subtitle')) ?></p>
      <p class="micro"><?= e(getSetting('hero_micro')) ?></p>
      <div class="hctas">
        <a class="btn btn-o" href="courses.php">Explore Courses</a>
        <a class="btn btn-l" href="#results">See Our Results</a>
      </div>
    </div>
    <div class="reveal">
      <div class="pw">
        <div class="pdeco" aria-hidden="true">
          <span class="sh sh1"></span><span class="sh sh2"></span><span class="sh sh3"></span><span class="sh sh4"></span>
        </div>
        <div class="pframe pframe-photo"><img src="<?= e(getSetting('hero_image')) ?>" alt="Mr. Naeem Haider"></div>
        <div class="pr pr-orange">Mr. Naeem Haider<small>Co-Founder &amp; Lead Instructor</small></div>
      </div>
    </div>
  </div>
</section>

<div class="band">
  <div class="wrap bg4 reveal">
    <div class="bs"><b><?= e($statLearners) ?></b><span>Learners in our community</span></div>
    <div class="bs"><b><?= e($statYoutube) ?></b><span>YouTube subscribers</span></div>
    <div class="bs"><b><?= e($statYears) ?></b><span>Teaching FBISE online</span></div>
    <div class="bs"><b><?= e($statSince) ?></b><span>Teaching languages since</span></div>
  </div>
</div>

<?php if ($subjects): ?>
<section id="courses">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Our Courses</div>
      <h2 class="t">Four subjects. One standard: <span class="hl">first position.</span></h2>
      <p class="sub">Complete FBISE preparation for Classes 9-12, from the first lecture to the final board paper.</p>
    </div>
    <div class="courses-grid reveal" style="margin-top:30px">
      <?php foreach ($subjects as $s): ?>
        <a href="enroll.php#enrol-form" class="ccard" style="--c:<?= e($s['accent_color']) ?>">
          <div class="ccard-media">
            <?php if (!empty($s['image'])): ?>
              <img src="<?= e($s['image']) ?>" alt="<?= e($s['title']) ?>" loading="lazy">
            <?php else: ?>
              <span class="ccard-media-fallback"><?= icon('book-open', 'icon') ?></span>
            <?php endif; ?>
          </div>
          <div class="ccard-body">
            <div class="ccard-num">0<?= (int)$s['sort_order'] ?>, <?= e($s['level']) ?></div>
            <h3 class="ccard-title"><?= e($s['title']) ?></h3>
            <p class="ccard-desc"><?= e($s['description']) ?></p>
            <?php if ($s['tag_line']): ?>
              <div class="tags"><?php foreach (explode(' - ', $s['tag_line']) as $tag): ?><span class="tag"><?= e(trim($tag)) ?></span><?php endforeach; ?></div>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:26px" class="reveal"><a class="vlink" href="courses.php">View all courses →</a></p>
  </div>
</section>
<?php endif; ?>

<?php if ($achievers): ?>
<section class="dark" id="results">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Proven Track Record</div>
      <h2 class="t">Three years. <span class="hl">Three first positions.</span></h2>
      <p class="sub">Not testimonials, verifiable federal board results.</p>
    </div>
    <div class="g3 reveal">
      <?php foreach ($achievers as $a): ?>
        <div class="tcard">
          <span class="tpos">1ST POSITION</span>
          <div class="tyr"><?= e(substr($a['batch_info'], -4)) ?></div>
          <b><?= e($a['name']) ?></b>
          <span><?= e($a['achievement']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if ($googleUrl): ?>
    <div class="gbar reveal">
      <div><span class="big"><?= e($googleRating) ?> <?= icon('star', 'icon star-icon') ?></span><small><?= e($googleCount) ?> Google reviews</small></div>
      <p>Rated <?= e($googleRating) ?> <?= icon('star', 'icon star-icon') ?> by <?= e($googleCount) ?> students, parents and teachers on Google.</p>
      <a class="btn btn-w" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See Reviews</a>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($aboutQuote['content']): ?>
<section class="vision soft">
  <div class="wrap">
    <div class="vquote reveal">
      <div class="kick">Founders&rsquo; Vision</div>
      <p>&ldquo;<?= e($aboutQuote['content']) ?>&rdquo;</p>
      <div class="vby"><b>Mr. Naeem Haider &amp; Mrs. Naeem Haider</b><span>Founders, <?= e($siteName) ?></span></div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="why">
  <div class="wrap">
    <div class="reveal" style="max-width:720px">
      <div class="kick">Why EnglishKeys</div>
      <h2 class="t">A planned, year-round path from <span class="hl">foundation to final paper.</span></h2>
      <p class="sub">One expert, one syllabus-mapped plan and a community that keeps you accountable, everything designed to move you toward first position.</p>
    </div>
    <div class="g3 whygrid" style="margin-top:38px">
      <div class="whycard reveal" style="--a:var(--orange)">
        <span class="whyn">01</span>
        <span class="whyi"><?= icon('cap', '') ?></span>
        <h3>Taught by one expert, not a rotating panel</h3>
        <p>Every class is led by Mr. Naeem Haider himself, an M.Phil. English Linguistics scholar with 14+ years of teaching.</p>
      </div>
      <div class="whycard reveal" style="--a:var(--purple)">
        <span class="whyn">02</span>
        <span class="whyi"><?= icon('target', '') ?></span>
        <h3>Mapped exactly to the FBISE syllabus</h3>
        <p>Nothing wasted. Smart notes, model papers and MCQ banks built around the current board pattern.</p>
      </div>
      <div class="whycard reveal" style="--a:var(--blue)">
        <span class="whyn">03</span>
        <span class="whyi"><?= icon('people', '') ?></span>
        <h3>A community of <?= e($statLearners) ?> learners</h3>
        <p>Followed across Facebook, YouTube and Instagram, a proven, trusted place to prepare.</p>
      </div>
    </div>
  </div>
</section>

<?php if ($testimonials): ?>
<section class="soft">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Student Stories</div>
      <h2 class="t">In their own words.</h2>
      <p class="sub">Genuine, permission-granted reviews from students and parents across Pakistan.</p>
    </div>
    <div class="g3">
      <?php foreach ($testimonials as $t): ?>
        <div class="rcard reveal">
          <div class="stars"><?= starRow(5) ?></div>
          <p>&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
          <div><b><?= e($t['name']) ?></b><br><span><?= e($t['source_label']) ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if ($googleUrl): ?>
      <p style="margin-top:34px" class="reveal"><a class="btn btn-n" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See all reviews on Google</a></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>


<?php if ($featured): ?>
<!-- A4: featured-course popup. Rendered only when an active featured course exists,
     so the JS in assets/js/site.js simply finds nothing on pages without it. -->
<div class="fc-pop" id="fcPop" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="fcTitle">
  <div class="fc-back" data-fc-close></div>
  <div class="card fcard fc-card">
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
      <a class="btn btn-o" href="enroll.php">Enrol Now</a>
      <a class="btn btn-l" href="courses.php">See All Courses</a>
    </div>
  </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
