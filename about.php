<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();

['founder' => $founder, 'cofounder' => $cofounder] = getFounderAndCofounder($db);

$excludeIds = array_values(array_filter([$founder['id'] ?? null, $cofounder['id'] ?? null]));
if ($excludeIds) {
    $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
    $facultyStmt = $db->prepare("SELECT * FROM teachers WHERE is_active = 1 AND id NOT IN ($placeholders) ORDER BY sort_order, id");
    $facultyStmt->execute($excludeIds);
    $faculty = $facultyStmt->fetchAll();
} else {
    $faculty = $db->query('SELECT * FROM teachers WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();
}

// Unlimited (no LIMIT) so the grid grows automatically as records are
// added in Admin -> Homepage Track Record, wrapping into new rows via
// CSS grid - no template change ever needed.
$trackRecords = getTrackRecords();

$statLearners = getSetting('stat_learners');
$statPositions = getSetting('stat_positions');
$statYears = getSetting('stat_years');
$statSince = getSetting('stat_since');
$methodSteps = getContentBlock('about', 'method_steps');
?>

<?php renderPageHero('about'); ?>

<div class="band">
  <div class="wrap bg4 reveal">
    <div class="bs"><b><?= e($statLearners) ?></b><span>Learners in our community</span></div>
    <div class="bs"><b><?= e($statPositions) ?></b><span>Consecutive HSSC 1st positions</span></div>
    <div class="bs"><b><?= e($statYears) ?></b><span>Teaching FBISE online</span></div>
    <div class="bs"><b><?= e($statSince) ?></b><span>Teaching languages since</span></div>
  </div>
</div>

<?php $teamCards = array_filter([$founder, $cofounder]); if ($teamCards): ?>
<section id="team">
  <div class="wrap">
    <div class="reveal" style="text-align:center">
      <div class="kick" style="justify-content:center">Our Team</div>
      <h2 class="t" style="margin-left:auto;margin-right:auto">Meet Our <span class="hl">Team.</span></h2>
      <p class="sub" style="text-align:center;margin-left:auto;margin-right:auto">The people behind EnglishKeys Academy, and the standard of teaching that produced three consecutive HSSC first positions.</p>
    </div>
    <div class="g2 reveal" style="max-width:600px;margin:0 auto">
      <?php foreach ($teamCards as $t): ?>
        <a class="bcard" href="#<?= e(slugify($t['name'])) ?>" style="text-align:center">
          <?php if ($t['photo']): ?><div class="pcrop"><img src="<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>"></div><?php endif; ?>
          <div class="bbody" style="align-items:center">
            <h3 style="margin-bottom:2px"><?= e($t['name']) ?></h3>
            <span style="font-size:11px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--purple)"><?= e($t['role_title']) ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
// Founder then Co-Founder profile sections - identical layout, only the
// image side and background alternate, matching the site's established
// alternating-section convention.
$profiles = array_filter([
    ['t' => $founder, 'rev' => false, 'soft' => true],
    ['t' => $cofounder, 'rev' => true, 'soft' => false],
], fn($p) => $p['t']);
foreach ($profiles as $p): $t = $p['t'];
?>
<section class="profile-section<?= $p['soft'] ? ' soft' : '' ?>" id="<?= e(slugify($t['name'])) ?>">
  <div class="profile-decor" aria-hidden="true">
    <?= icon('open-book', 'profile-decor-icon pd-1') ?>
    <?= icon('grad-cap', 'profile-decor-icon pd-2') ?>
    <?= icon('star-badge', 'profile-decor-icon pd-3') ?>
  </div>
  <div class="wrap">
    <div class="split <?= $p['rev'] ? 'rev' : '' ?>">
      <div class="reveal">
        <div class="founder-frame">
          <?php if ($t['photo']): ?><img src="<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>"><?php endif; ?>
        </div>
      </div>
      <div class="bio reveal">
        <div class="kick"><?= e($t['role_title']) ?></div>
        <h2 class="t">Meet <?= e($t['name']) ?></h2>
        <?php foreach (explode("\n\n", (string)$t['detail_bio']) as $para): if (trim($para) === '') continue; ?>
          <p><?= e($para) ?></p>
        <?php endforeach; ?>
        <?php if ($t['credentials']): ?>
          <h3 style="font-size:19px;margin:22px 0 14px">Portfolio &amp; Qualifications</h3>
          <div class="creds">
            <?php foreach (explode("\n", $t['credentials']) as $cred): if (trim($cred) === '') continue; ?><div class="cred"><?= e($cred) ?></div><?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endforeach; ?>

<?php if ($faculty): ?>
<section class="soft" id="faculty">
  <div class="wrap">
    <div class="reveal" style="text-align:center">
      <div class="kick" style="justify-content:center">Our Faculty</div>
      <h2 class="t" style="margin-left:auto;margin-right:auto">Meet Our <span class="hl">Faculty.</span></h2>
      <p class="sub" style="text-align:center;margin-left:auto;margin-right:auto">The instructors behind EnglishKeys Academy's standard of teaching.</p>
    </div>
    <div class="faculty-grid reveal">
      <?php foreach ($faculty as $i => $t): ?>
        <div class="faculty-card"<?= revealDelay($i) ?>>
          <?php if ($t['photo']): ?><div class="pcrop"><img src="<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>"></div><?php endif; ?>
          <h3><?= e($t['name']) ?></h3>
          <?php if ($t['role_title']): ?><span class="faculty-role"><?= e($t['role_title']) ?></span><?php endif; ?>
          <?php if ($t['qualification']): ?><p class="faculty-qual"><?= e($t['qualification']) ?></p><?php endif; ?>
          <?php if ($t['bio']): ?><p class="faculty-desc"><?= e($t['bio']) ?></p><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($trackRecords): ?>
<section class="dark">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">The Hat-Trick</div>
      <h2 class="t">Three years. <span class="hl">Three first positions.</span></h2>
      <p class="sub">Verifiable federal board results, not fabricated testimonials.</p>
    </div>
    <div class="g3 reveal">
      <?php foreach ($trackRecords as $i => $r): ?>
        <?= renderTrackRecordCard($r, 'tcard reveal', revealDelay($i)) ?>
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

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
