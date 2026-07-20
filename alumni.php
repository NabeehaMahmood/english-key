<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDb();
$errors = [];

// Defaults for a fresh (GET) load. On a failed POST these are overwritten
// below with the submitted values, so the form re-renders with everything
// the user typed intact - only the captcha is ever reset (same pattern as
// enroll.php).
$name = $batch = $achievement = $contact = $story = '';
$consent = false;

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
    $consent = !empty($_POST['consent']);

    if ($name === '') $errors[] = 'Please enter your full name.';
    if ($batch === '') $errors[] = 'Please enter your class/batch.';
    if ($story === '') $errors[] = 'Please write your story.';
    if (!enrolCaptchaPassed()) $errors[] = 'Security check failed, please solve the sum and try again.';

    if (!$errors) {
        try {
            $photo = handleImageUpload('photo', 'gallery');
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        $stmt = $db->prepare("INSERT INTO alumni (name, type, achievement, batch_info, photo, story, contact, is_active, status) VALUES (?,'story',?,?,?,?,?,0,'pending')");
        $stmt->execute([$name, $achievement, $batch, $photo ?? null, $story, $contact]);
        redirectWithMessage('alumni.php#share', 'Thank you! Your story has been submitted and is awaiting admin approval.');
    }
}

require_once __DIR__ . '/includes/header.php';

$achievers = $db->query("SELECT * FROM alumni WHERE type = 'achiever' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$stories = $db->query("SELECT * FROM alumni WHERE type = 'story' AND status = 'approved' AND is_active = 1 ORDER BY sort_order DESC")->fetchAll();
$captchaQuestion = enrolCaptchaQuestion();
?>

<div class="phero phero-navy">
  <div class="wrap reveal">
    <h1>Once EnglishKeys, <span class="hl">always EnglishKeys.</span></h1>
    <p class="sub">Our alumni carry the academy's standard into medical colleges, universities and careers. This corner belongs to them, their journeys, milestones and advice for the students following behind.</p>
  </div>
</div>

<nav class="jumpnav wrap reveal" aria-label="Section navigation">
  <span class="jumpnav-label"><?= icon('list', 'icon') ?> On this page</span>
  <?php if ($achievers): ?><a href="#achievers"><span>Alumni Achievers</span></a><?php endif; ?>
  <a href="#stories"><span>Alumni Stories</span></a>
  <a href="#share"><span>Share Your Story</span></a>
</nav>

<?php if ($achievers): ?>
<section class="dark" id="achievers" style="padding:64px 0">
  <div class="wrap">
    <div class="g4 ag4 reveal">
      <?php foreach ($achievers as $a): ?>
        <div class="acard">
          <?php if (!empty($a['photo'])): ?>
            <img src="<?= e($a['photo']) ?>" alt="<?= e($a['name']) ?>" class="acard-photo">
          <?php else: ?>
            <div class="acard-photo acard-photo-ph"><?= e(mb_strtoupper(mb_substr($a['name'], 0, 1))) ?></div>
          <?php endif; ?>
          <b><?= e($a['name']) ?></b>
          <span class="acard-batch"><?= e($a['batch_info']) ?></span>
          <span class="acard-year">Batch <?= e($a['passing_year'] ?: substr($a['batch_info'], -4)) ?></span>
          <?php if ($a['achievement']): ?><p class="acard-achv"><?= e($a['achievement']) ?></p><?php endif; ?>
        </div>
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
    <?php if (!$stories): ?>
      <p class="sub reveal">No stories have been published yet. Be the first to share yours below.</p>
    <?php endif; ?>
    <div class="g3">
      <?php foreach ($stories as $s): ?>
        <div class="story-card reveal">
          <div class="story-head">
            <?php if (!empty($s['photo'])): ?>
              <img src="<?= e($s['photo']) ?>" alt="<?= e($s['name']) ?>" class="story-avatar">
            <?php else: ?>
              <div class="story-avatar-ph"><?= e(mb_strtoupper(mb_substr($s['name'], 0, 1))) ?></div>
            <?php endif; ?>
            <div>
              <b class="story-name"><?= e($s['name']) ?></b>
              <span class="story-batch"><?= e($s['batch_info']) ?></span>
            </div>
          </div>
          <?php if (!empty($s['achievement'])): ?>
            <span class="story-badge"><?= icon('star-badge') ?><?= e($s['achievement']) ?></span>
          <?php endif; ?>
          <p class="story-text"><?= e($s['story']) ?></p>
          <button type="button" class="story-toggle">Read More</button>
          <?php if (!empty($s['published'])): ?>
            <div class="story-foot">Published <?= e($s['published']) ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php $shareOpen = !empty($errors); ?>
<section class="soft" id="share">
  <div class="wrap">
    <div class="share-cta reveal">
      <div class="kick">Share Your Story</div>
      <h2 class="t">Add your story to the wall.</h2>
      <p class="sub">Fill in your basic information, add a photo if you like, and write your story. Our team checks every submission before it goes live.</p>
      <button type="button" class="btn btn-o btn-share" id="shareToggle" aria-expanded="<?= $shareOpen ? 'true' : 'false' ?>" aria-controls="sharePanel">
        Share Your Story <span class="chev" aria-hidden="true">&#9660;</span>
      </button>
    </div>

    <div class="share-panel<?= $shareOpen ? ' open' : '' ?>" id="sharePanel">
      <?php if ($errors): ?>
        <div class="flash flash-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>
      <form class="alform" method="post" action="alumni.php#share" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="text" name="website" value="" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="fg2">
          <label>Full name *<input type="text" name="name" maxlength="60" value="<?= e($name) ?>" required></label>
          <label>Class / batch *<input type="text" name="batch" maxlength="30" value="<?= e($batch) ?>" required placeholder="e.g. Class of 2026"></label>
        </div>
        <div class="fg2">
          <label>Result / current status<input type="text" name="achievement" maxlength="80" value="<?= e($achievement) ?>" placeholder="e.g. HSSC 1st Position"></label>
          <label>WhatsApp or email (private, for verification only)<input type="text" name="contact" maxlength="80" value="<?= e($contact) ?>" placeholder="not shown on the website"></label>
        </div>
        <label class="al-upload">Profile photo (optional)
          <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" id="alPhoto">
          <span class="al-hint">JPG, PNG or WEBP, max 5 MB.</span>
        </label>
        <label>Your story *
          <textarea name="story" maxlength="1200" rows="6" required placeholder="Your journey, your result, or a word of advice for current students..." id="alStory"><?= e($story) ?></textarea>
        </label>
        <div class="al-counter"><span id="alCounterNum">0</span>/1200 characters</div>
        <label>Security check *
          <div class="ef-captcha-row">
            <span class="ef-captcha-question" id="captchaQuestion" aria-live="polite"><?= e($captchaQuestion) ?> = ?</span>
            <button type="button" class="ef-captcha-refresh" id="captchaRefresh" aria-label="Get a new question">
              <?= icon('refresh') ?>
            </button>
            <input id="captcha_answer" name="captcha_answer" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="3" autocomplete="off" aria-describedby="alCaptchaHint" required>
          </div>
        </label>
        <div class="al-hint" id="alCaptchaHint">Solve the sum above to confirm you're not a robot. Don't like this one? Tap refresh for another.</div>
        <label class="al-consent">
          <input type="checkbox" name="consent" <?= $consent ? 'checked' : '' ?> required>
          <span>I agree to let <?= e($siteName) ?> publish my name, photo and story on this page. Every submission is reviewed by our team before it goes live.</span>
        </label>
        <div class="al-actions"><button class="btn btn-o" type="submit">Publish my story</button></div>
      </form>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
