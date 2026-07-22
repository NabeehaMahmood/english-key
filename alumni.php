<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDb();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (honeypotTripped()) {
        redirectWithMessage('alumni.php#share', 'Thank you for sharing your story!');
    }

    $name = trim($_POST['name'] ?? '');
    $batch = trim($_POST['batch'] ?? '');
    $achievement = trim($_POST['achievement'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $story = trim($_POST['story'] ?? '');

    if ($name === '') $errors[] = 'Please enter your full name.';
    if ($batch === '') $errors[] = 'Please enter your class/batch.';
    if ($story === '') $errors[] = 'Please write your story.';
    if (!humanCheckPassed()) $errors[] = 'Human check failed, please try again.';

    if (!$errors) {
        try {
            $photo = handleImageUpload('photo', 'gallery');
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        $stmt = $db->prepare('INSERT INTO alumni (name, achievement, batch_info, photo, story, contact, is_active) VALUES (?,?,?,?,?,?,0)');
        $stmt->execute([$name, $achievement, $batch, $photo ?? null, $story, $contact]);
        redirectWithMessage('alumni.php#share', 'Thank you! Your story has been submitted and will appear here once our team reviews it.');
    }
}

require_once __DIR__ . '/includes/header.php';

$trackRecords = getTrackRecords();
$stories = $db->query("SELECT * FROM alumni WHERE is_active = 1 AND story IS NOT NULL AND story != '' ORDER BY sort_order DESC")->fetchAll();
$humanQuestion = humanCheckQuestion();
?>

<?php renderPageHero('alumni'); ?>

<?php if ($trackRecords): ?>
<section class="dark" style="padding:64px 0">
  <div class="wrap">
    <div class="g3 reveal">
      <?php foreach ($trackRecords as $i => $r): ?>
        <?= renderTrackRecordCard($r, 'tcard reveal', revealDelay($i)) ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section id="stories">
  <div class="wrap">
    <div class="reveal" style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:34px">
      <div>
        <div class="kick">Alumni Stories</div>
        <h2 class="t" style="margin-bottom:0">In their own words, published by them.</h2>
      </div>
      <a class="btn btn-o" href="#share">Share your story</a>
    </div>
    <?php if ($stories): ?>
      <div class="g3">
        <?php foreach ($stories as $s): ?>
          <div class="rcard reveal">
            <?php if ($s['photo']): ?><img src="<?= e($s['photo']) ?>" alt="<?= e($s['name']) ?>" class="avatar"><?php endif; ?>
            <p>&ldquo;<?= e(mb_strimwidth($s['story'], 0, 220, '...')) ?>&rdquo;</p>
            <div><b><?= e($s['name']) ?></b><br><span><?= e($s['batch_info']) ?><?= $s['achievement'] ? ' · ' . e($s['achievement']) : '' ?></span></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="card reveal" style="text-align:center;padding:44px">
        <p style="color:var(--muted)">No alumni stories yet, be the first to share yours!</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="soft" id="share">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Share Your Story</div>
      <h2 class="t">Add your story to the wall.</h2>
      <p class="sub">Fill in your basic information, add a photo if you like, and write your story. Our team checks every submission before it goes live.</p>
    </div>
    <?php if ($errors): ?>
      <div class="flash flash-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <form class="alform" method="post" action="alumni.php#share" enctype="multipart/form-data">
      <?= csrfField() ?>
      <input type="text" name="website" value="" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off" aria-hidden="true">
      <div class="fg2">
        <label>Full name *<input type="text" name="name" maxlength="60" required></label>
        <label>Class / batch *<input type="text" name="batch" maxlength="30" required placeholder="e.g. Class of 2026"></label>
      </div>
      <div class="fg2">
        <label>Result / current status<input type="text" name="achievement" maxlength="80" placeholder="e.g. HSSC 1st Position"></label>
        <label>WhatsApp or email (private, for verification only)<input type="text" name="contact" maxlength="80" placeholder="not shown on the website"></label>
      </div>
      <label>Your photo (optional, JPG/PNG/WEBP, max 5 MB)<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></label>
      <label>Your story *<textarea name="story" maxlength="1200" rows="6" required placeholder="Your journey, your result, or a word of advice for current students..."></textarea></label>
      <div class="fg2">
        <label>Human check: what is <?= e($humanQuestion) ?>? *<input type="text" name="human" maxlength="3" required inputmode="numeric"></label>
        <div style="display:flex;align-items:flex-end"><button class="btn btn-o" type="submit">Publish my story</button></div>
      </div>
      <p style="font-size:12.5px;color:var(--muted)">Stories are reviewed by our team before they appear on the website. By submitting, you give <?= e($siteName) ?> permission to publish your name, photo and story on this page.</p>
    </form>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
