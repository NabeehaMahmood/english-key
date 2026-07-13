<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();
$featured = $db->query("SELECT * FROM courses WHERE category = 'featured' AND is_active = 1 ORDER BY sort_order LIMIT 1")->fetch();
$subjects = $db->query("SELECT * FROM courses WHERE category = 'subject' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$programmes = $db->query("SELECT * FROM courses WHERE category = 'programme' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$testimonials = $db->query('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order LIMIT 6')->fetchAll();

$whatsapp = getSetting('whatsapp_number');
$howTo = getContentBlock('courses', 'how_to_enrol_steps');
$terms = getContentBlock('courses', 'terms_conditions');
$bankName = getSetting('bank_name');
$bankTitle = getSetting('bank_title');
$bankIban = getSetting('bank_iban');
$easypaisaName = getSetting('easypaisa_name');
$easypaisaNumber = getSetting('easypaisa_number');
$googleUrl = getSetting('google_reviews_url');
$googleRating = getSetting('google_rating');
$googleCount = getSetting('google_review_count');
?>

<div class="phero">
  <div class="wrap reveal">
    <div class="kick">Courses</div>
    <h1>Built around the FBISE syllabus, <span class="hl">nothing wasted.</span></h1>
    <p class="sub">Complete preparation for Classes 9-12 across four subjects, plus seasonal intensives, bootcamps and crash courses.</p>
  </div>
</div>

<?php if ($featured): ?>
<section>
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Featured, Enrolling Now</div>
      <h2 class="t"><?= e($featured['title']) ?>, <span class="hl"><?= e($featured['tag_line']) ?></span></h2>
      <p class="sub"><?= e($featured['description']) ?></p>
    </div>
    <div class="g2 reveal">
      <div class="card">
        <?php if ($featured['schedule_info']): ?>
          <div class="detgrid">
            <?php foreach (explode(' - ', $featured['schedule_info']) as $part): ?>
              <div class="det"><b><?= e($part) ?></b></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:10px">
          <a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Enrol on WhatsApp</a>
          <a class="btn btn-l" href="contact.php">Ask a Question</a>
        </div>
        <?php if ($featured['seats_info']): ?><p style="color:var(--orange);font-weight:700;font-size:13.5px"><?= e($featured['seats_info']) ?></p><?php endif; ?>
      </div>
      <div class="card">
        <h3 style="font-size:20px;margin-bottom:16px">Course highlights</h3>
        <?php foreach (explode("\n", (string)$featured['highlights']) as $h): ?><div class="check"><?= e($h) ?></div><?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($subjects): ?>
<section class="soft">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Core Subjects</div>
      <h2 class="t">Four subjects, mapped to your class.</h2>
    </div>
    <div class="g2 reveal" style="margin-top:30px">
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
  </div>
</section>
<?php endif; ?>

<?php if ($programmes): ?>
<section>
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Seasonal &amp; Intensive</div>
      <h2 class="t">Programmes for every stage of the year.</h2>
    </div>
    <div class="g3 reveal" style="margin-top:30px">
      <?php foreach ($programmes as $p): ?>
        <div class="card pcard">
          <?php if ($p['tag_line']): ?><span class="ntag"><?= e($p['tag_line']) ?></span><?php endif; ?>
          <h3 class="ptitle"><?= e($p['title']) ?></h3>
          <p class="pdesc"><?= e($p['description']) ?></p>
          <?php if ($p['highlights']): ?>
            <ul class="pfeat"><?php foreach (explode("\n", $p['highlights']) as $h): ?><li><?= e($h) ?></li><?php endforeach; ?></ul>
          <?php endif; ?>
          <div class="meta">
            <?php if ($p['eligibility']): ?><span><?= icon('person') ?> <?= e($p['eligibility']) ?></span><?php endif; ?>
            <?php if ($p['duration']): ?><span><?= icon('calendar') ?> <?= e($p['duration']) ?></span><?php endif; ?>
            <?php if ($p['price']): ?><span><?= icon('card') ?> <?= e($p['price']) ?></span><?php endif; ?>
            <?php if ($p['seats_info']): ?><span><?= icon('ticket') ?> <?= e($p['seats_info']) ?></span><?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="notebar reveal" style="margin-top:26px">
      <?= icon('calendar', 'icon notebar-icon') ?>
      <p>Seats are limited each term to protect teaching quality. Message us on WhatsApp with your class and subjects for the current schedule and fees.</p>
      <a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Ask on WhatsApp</a>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="soft">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">How to Enrol</div>
      <h2 class="t">Four simple steps to your seat.</h2>
    </div>
    <?php if ($howTo['content']): ?>
    <div class="g4 reveal" style="margin-top:30px">
      <?php $n = 0; foreach (explode("\n", $howTo['content']) as $line):
        [$title, $desc] = array_pad(explode('|', $line, 2), 2, ''); $n++;
        $title = preg_replace('/^\d+\.\s*/', '', $title); ?>
        <div class="mcard">
          <div class="mno">0<?= $n ?></div>
          <h3><?= e($title) ?></h3>
          <p><?= e($desc) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="g2 reveal" style="margin-top:22px">
      <div class="card">
        <h3 style="font-size:19px;margin-bottom:16px;display:flex;align-items:center;gap:8px"><?= icon('card') ?> Payment details</h3>
        <?php if ($bankIban): ?>
        <div style="background:var(--bg);border-radius:12px;padding:16px 18px;margin-bottom:12px">
          <b style="display:block;margin-bottom:4px"><?= e($bankName) ?></b>
          <span style="font-size:14px;color:var(--muted)">Title: <?= e($bankTitle) ?><br>IBAN: <?= e($bankIban) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($easypaisaNumber): ?>
        <div style="background:var(--bg);border-radius:12px;padding:16px 18px;margin-bottom:12px">
          <b style="display:block;margin-bottom:4px">EasyPaisa</b>
          <span style="font-size:14px;color:var(--muted)">Name: <?= e($easypaisaName) ?><br>Number: <?= e($easypaisaNumber) ?></span>
        </div>
        <?php endif; ?>
        <p style="font-size:13.5px;color:var(--muted)">Send your payment screenshot on WhatsApp to activate your seat the same day.</p>
      </div>
      <?php if ($terms['content']): ?>
      <div class="card">
        <h3 style="font-size:19px;margin-bottom:16px">Terms &amp; conditions</h3>
        <?php foreach (explode("\n", $terms['content']) as $term): ?><div class="check"><?= e($term) ?></div><?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($testimonials): ?>
<section>
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Student Reviews</div>
      <h2 class="t">What students say, by subject &amp; course.</h2>
      <?php if ($googleUrl): ?><p class="sub"><b style="color:var(--orange)"><?= icon('star', 'icon star-icon') ?> <?= e($googleRating) ?></b> from <?= e($googleCount) ?> genuine Google reviews.</p><?php endif; ?>
    </div>
    <div class="g3">
      <?php foreach ($testimonials as $t): ?>
        <div class="rcard reveal">
          <?php if ($t['category']): ?><span class="ntag"><?= e($t['category']) ?></span><?php endif; ?>
          <p>&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
          <div><b><?= e($t['name']) ?></b><br><span><?= e($t['source_label']) ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if ($googleUrl): ?>
      <p style="margin-top:32px" class="reveal"><a class="btn btn-n" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See Reviews on Google</a></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
