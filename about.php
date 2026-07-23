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

$methodSteps = getContentBlock('about', 'method_steps');
?>

<?php renderPageHero('about'); ?>

<?php $leaders = array_filter([$founder, $cofounder]); if ($leaders || $faculty): ?>
<section id="team">
  <div class="wrap">
    <div class="reveal" style="text-align:center">
      <div class="kick" style="justify-content:center">Our Team</div>
      <h2 class="t" style="margin-left:auto;margin-right:auto">Meet Our <span class="hl">Team.</span></h2>
      <p class="sub" style="text-align:center;margin-left:auto;margin-right:auto">The people behind EnglishKeys Academy, and the standard of teaching that produced three consecutive HSSC first positions.</p>
    </div>
    <div class="team-grid reveal">
      <?php foreach ($leaders as $t): ?>
        <a class="bcard" href="#<?= e(slugify($t['name'])) ?>" style="text-align:center">
          <?php if ($t['photo']): ?><div class="pcrop<?= in_array($t['photo'], STOCK_AVATARS, true) ? ' avatar-fallback' : '' ?>"><img src="<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>"></div><?php endif; ?>
          <div class="bbody" style="align-items:center">
            <h3 style="margin-bottom:2px"><?= e($t['name']) ?></h3>
            <span style="font-size:11px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--purple)"><?= e($t['role_title']) ?></span>
            <?php if ($t['qualification']): ?><span class="team-qual"><?= e($t['qualification']) ?></span><?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
      <?php foreach ($faculty as $t): ?>
        <div class="bcard" style="text-align:center">
          <?php if ($t['photo']): ?><div class="pcrop<?= in_array($t['photo'], STOCK_AVATARS, true) ? ' avatar-fallback' : '' ?>"><img src="<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>"></div><?php endif; ?>
          <div class="bbody" style="align-items:center">
            <h3 style="margin-bottom:2px"><?= e($t['name']) ?></h3>
            <span style="font-size:11px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--purple)"><?= e($t['role_title']) ?></span>
            <?php if ($t['qualification']): ?><span class="team-qual"><?= e($t['qualification']) ?></span><?php endif; ?>
          </div>
        </div>
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
        <div class="founder-frame<?= ($t['photo'] && in_array($t['photo'], STOCK_AVATARS, true)) ? ' avatar-fallback' : '' ?>">
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

<?php if ($methodSteps['content']): ?>
<section class="soft">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Our Method</div>
      <h2 class="t">A learning cycle that ends in first-class answers.</h2>
    </div>
    <div class="g3 reveal" style="margin-top:30px">
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
