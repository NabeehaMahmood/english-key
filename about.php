<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();
$teachers = $db->query('SELECT * FROM teachers WHERE is_active = 1 ORDER BY sort_order')->fetchAll();
$achievers = $db->query('SELECT * FROM alumni WHERE is_active = 1 ORDER BY sort_order LIMIT 3')->fetchAll();

$statLearners = getSetting('stat_learners');
$statPositions = getSetting('stat_positions');
$statYears = getSetting('stat_years');
$statSince = getSetting('stat_since');
$methodSteps = getContentBlock('about', 'method_steps');
?>

<div class="phero">
  <div class="wrap reveal">
    <div class="kick">About Us</div>
    <h1>Where words <span class="hl">build futures.</span></h1>
    <p class="sub">EnglishKeys Academy exists to bring first-position-quality preparation to every FBISE student in Pakistan, taught live, with the discipline and care of a single expert instructor.</p>
  </div>
</div>

<div class="band">
  <div class="wrap bg4 reveal">
    <div class="bs"><b><?= e($statLearners) ?></b><span>Learners in our community</span></div>
    <div class="bs"><b><?= e($statPositions) ?></b><span>Consecutive HSSC 1st positions</span></div>
    <div class="bs"><b><?= e($statYears) ?></b><span>Teaching FBISE online</span></div>
    <div class="bs"><b><?= e($statSince) ?></b><span>Teaching languages since</span></div>
  </div>
</div>

<section>
  <div class="wrap">
    <div class="reveal" style="text-align:center">
      <div class="kick" style="justify-content:center">Our Team</div>
      <h2 class="t" style="margin-left:auto;margin-right:auto">Meet Our <span class="hl">Team.</span></h2>
      <p class="sub" style="text-align:center;margin-left:auto;margin-right:auto">The people behind EnglishKeys Academy, and the standard of teaching that produced three consecutive HSSC first positions.</p>
    </div>
    <div class="g3 reveal" style="max-width:900px;margin:0 auto">
      <?php foreach ($teachers as $t): ?>
        <a class="bcard" href="#<?= e(slugify($t['name'])) ?>" style="text-align:center">
          <?php if ($t['photo']): ?><div class="pcrop"><img src="<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>"></div><?php endif; ?>
          <div class="bbody" style="align-items:center">
            <h3 style="margin-bottom:2px"><?= e($t['name']) ?></h3>
            <span style="font-size:11px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--purple)"><?= e($t['role_title']) ?></span>
          </div>
        </a>
      <?php endforeach; ?>
      <div class="bcard" style="text-align:center;border-style:dashed">
        <div class="pcrop placeholder-avatar" style="display:grid;place-items:center;background:var(--bg);color:var(--muted);aspect-ratio:1/1"><?= icon('plus', 'icon') ?></div>
        <div class="bbody" style="align-items:center">
          <h3 style="margin-bottom:2px;color:var(--muted)">Growing Team</h3>
          <span style="font-size:11px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--muted)">New members coming soon</span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php foreach ($teachers as $i => $t): ?>
<section class="<?= $i % 2 === 0 ? 'soft' : '' ?>" id="<?= e(slugify($t['name'])) ?>">
  <div class="wrap">
    <div class="split <?= $i % 2 === 1 ? 'rev' : '' ?>">
      <div class="reveal">
        <div class="pw" style="margin:0 auto">
          <?php if ($t['photo']): ?><div class="pframe"><img src="<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>"></div><?php endif; ?>
          <div class="pr"><?= e($t['name']) ?><small><?= e($t['role_title']) ?></small></div>
        </div>
      </div>
      <div class="bio reveal">
        <div class="kick"><?= e($t['role_title']) ?></div>
        <h2 class="t">Meet <?= e($t['name']) ?></h2>
        <?php foreach (explode("\n\n", (string)$t['detail_bio']) as $para): ?>
          <p><?= e($para) ?></p>
        <?php endforeach; ?>
        <?php if ($t['credentials']): ?>
          <h3 style="font-size:19px;margin:22px 0 14px">Portfolio</h3>
          <div class="creds">
            <?php foreach (explode("\n", $t['credentials']) as $cred): ?><div class="cred"><?= e($cred) ?></div><?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endforeach; ?>

<?php if ($achievers): ?>
<section class="dark">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">The Hat-Trick</div>
      <h2 class="t">Three years. <span class="hl">Three first positions.</span></h2>
      <p class="sub">Verifiable federal board results, not fabricated testimonials.</p>
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
  </div>
</section>
<?php endif; ?>

<?php if ($methodSteps['content']): ?>
<section class="soft">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Our Method</div>
      <h2 class="t">A learning cycle that ends in first-class answers.</h2>
    </div>
    <div class="g3 reveal" style="margin-top:30px;grid-template-columns:repeat(3,1fr)">
      <?php $n = 0; foreach (explode("\n", $methodSteps['content']) as $line):
        [$title, $desc] = array_pad(explode('|', $line, 2), 2, ''); $n++; ?>
        <div class="mcard">
          <div class="mno">0<?= $n ?></div>
          <h3><?= e($title) ?></h3>
          <p><?= e($desc) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
