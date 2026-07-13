<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();
$subjects = $db->query("SELECT * FROM courses WHERE category = 'subject' AND is_active = 1 ORDER BY sort_order LIMIT 4")->fetchAll();
$achievers = $db->query('SELECT * FROM alumni WHERE is_active = 1 ORDER BY sort_order LIMIT 3')->fetchAll();
$teachers = $db->query('SELECT * FROM teachers WHERE is_active = 1 ORDER BY sort_order LIMIT 2')->fetchAll();
$testimonials = $db->query("SELECT * FROM testimonials WHERE is_active = 1 AND category != 'Parent' ORDER BY sort_order LIMIT 6")->fetchAll();
$parentTestimonials = $db->query("SELECT * FROM testimonials WHERE is_active = 1 AND category = 'Parent' ORDER BY sort_order LIMIT 2")->fetchAll();

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
      <div class="hctas">
        <a class="btn btn-o" href="courses.php">Explore Courses</a>
        <a class="btn btn-l" href="#results">See Our Results</a>
      </div>
      <p class="micro"><?= e(getSetting('hero_micro')) ?></p>
      <div class="hfacts">
        <div class="hf"><b><?= e($statPositions) ?></b><span>HSSC 1st Positions</span></div>
        <div class="hf"><b><?= e($statLearners) ?></b><span>Learners</span></div>
        <div class="hf"><b><?= e($statYoutube) ?></b><span>YouTube Subscribers</span></div>
        <div class="hf"><b>15+</b><span>Years of Transforming Minds</span></div>
      </div>
    </div>
    <div class="reveal">
      <div class="pw">
        <div class="pframe"><img src="<?= e(getSetting('hero_image')) ?>" alt="Lead Instructor"></div>
        <div class="pr">Mr. Naeem Haider<small>Co-Founder &amp; Lead Instructor</small></div>
      </div>
    </div>
  </div>
</section>

<div class="band">
  <div class="wrap bg4 reveal">
    <div class="bs"><b><?= e($statLearners) ?></b><span>Learners in our community</span></div>
    <div class="bs"><b><?= e($statPositions) ?></b><span>Consecutive HSSC 1st positions</span></div>
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
    <div class="g2 reveal">
      <?php foreach ($subjects as $s): ?>
        <div class="card scard" style="--c:<?= e($s['accent_color']) ?>">
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

<?php if ($teachers): ?>
<section class="soft">
  <div class="wrap">
    <div class="reveal" style="text-align:center;max-width:760px;margin:0 auto 14px">
      <div class="kick" style="justify-content:center">The Founders</div>
      <h2 class="t" style="margin-left:auto;margin-right:auto">One academy. <span class="hl">One family.</span></h2>
      <p class="sub" style="text-align:center;margin:0 auto 18px">EnglishKeys Academy is founded and run by a husband and wife, a partnership of vision and teaching that treats every student like family.</p>
    </div>
    <?php if ($aboutQuote['content']): ?>
      <div class="pquote reveal" style="max-width:720px;margin:0 auto 44px;text-align:center;border-left:none;border-top:5px solid var(--orange)">
        <p style="font-style:italic;margin:0">&ldquo;<?= e($aboutQuote['content']) ?>&rdquo;</p>
      </div>
    <?php endif; ?>
    <div class="g2 reveal" style="max-width:920px;margin:0 auto">
      <?php foreach ($teachers as $t): ?>
        <div class="bcard">
          <?php if ($t['photo']): ?><div class="pcrop"><img src="<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>"></div><?php endif; ?>
          <div class="bbody">
            <span style="font-family:'Manrope',sans-serif;font-size:11px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:var(--purple)"><?= e($t['role_title']) ?></span>
            <h3><?= e($t['name']) ?></h3>
            <p><?= e($t['bio']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:36px" class="reveal"><a class="btn btn-n" href="about.php">Meet the founders</a></p>
  </div>
</section>
<?php endif; ?>

<section>
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Why EnglishKeys</div>
      <h2 class="t">A planned, year-round path from foundation to final paper.</h2>
    </div>
    <div class="g3 reveal" style="margin-top:34px">
      <div class="card">
        <?= icon('cap', 'icon feature-icon') ?>
        <h3 style="font-size:18px;margin:12px 0 8px">Taught by one expert, not a rotating panel</h3>
        <p style="color:var(--muted);font-size:14px">Every class is led by Mr. Naeem Haider himself, an M.Phil. English Linguistics scholar with 14+ years of teaching.</p>
      </div>
      <div class="card">
        <?= icon('target', 'icon feature-icon') ?>
        <h3 style="font-size:18px;margin:12px 0 8px">Mapped exactly to the FBISE syllabus</h3>
        <p style="color:var(--muted);font-size:14px">Nothing wasted. Smart notes, model papers and MCQ banks built around the current board pattern.</p>
      </div>
      <div class="card">
        <?= icon('people', 'icon feature-icon') ?>
        <h3 style="font-size:18px;margin:12px 0 8px">A community of <?= e($statLearners) ?> learners</h3>
        <p style="color:var(--muted);font-size:14px">Followed across Facebook, YouTube and Instagram, a proven, trusted place to prepare.</p>
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
    <?php if ($parentTestimonials): ?>
      <h3 class="mini reveal">From parents.</h3>
      <div class="g2">
        <?php foreach ($parentTestimonials as $t): ?>
          <div class="pquote reveal">
            <p style="font-style:italic;font-size:14.5px;margin-bottom:16px">&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
            <b><?= e($t['name']) ?></b><br><span style="color:var(--muted);font-size:12.5px"><?= e($t['source_label']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php if ($googleUrl): ?>
      <p style="margin-top:34px" class="reveal"><a class="btn btn-n" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See all reviews on Google</a></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
