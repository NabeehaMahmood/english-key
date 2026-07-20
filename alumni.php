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

$achievers = $db->query("SELECT * FROM alumni WHERE is_active = 1 AND (story IS NULL OR story = '') ORDER BY sort_order")->fetchAll();
$stories = $db->query("SELECT * FROM alumni WHERE is_active = 1 AND story IS NOT NULL AND story != '' ORDER BY sort_order DESC")->fetchAll();
$humanQuestion = humanCheckQuestion();
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
          <span class="acard-year">Batch <?= e(substr($a['batch_info'], -4)) ?></span>
          <?php if ($a['achievement']): ?><p class="acard-achv"><?= e($a['achievement']) ?></p><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
/* ---- DEMO DATA: dummy alumni stories for UI preview only -------------
   $stories above already reads real, approved submissions from the
   `alumni` table. These placeholders exist purely so the section can be
   demonstrated before any real stories are approved, and are shown ONLY
   when $stories is empty (i.e. right now). The moment a real story is
   approved in the Admin, $stories will contain it and this dummy list is
   skipped automatically, no frontend changes required. Delete this block
   once real content is flowing. -------------------------------------- */
$dummyStories = [
    [
        'name' => 'Ayesha Noor',
        'batch_info' => 'Class of 2024',
        'photo' => '',
        'achievement' => '',
        'published' => 'Jun 14, 2026',
        'story' => "Joining EnglishKeys in Class 11 was the turning point of my academic life. The teachers didn't just cover the FBISE syllabus, they made sure every concept actually clicked before moving on. The weekly tests kept me consistent, and the mentoring before my HSSC exams gave me the confidence to aim for medical college instead of settling for less. I'm now in my first year of MBBS, and I still use the note-taking habits I built here.",
    ],
    [
        'name' => 'Hamza Raza',
        'batch_info' => 'Class of 2023',
        'photo' => '',
        'achievement' => '',
        'published' => 'May 2, 2026',
        'story' => "I came to EnglishKeys after struggling with English and Physics for two years elsewhere. What changed everything was how approachable the faculty was, no question ever felt too basic to ask. By the time my board exams came around, subjects I once dreaded had become my strongest ones. I secured a position in the merit list, and I genuinely credit the structured practice and past-paper drilling here for that result.",
    ],
    [
        'name' => 'Sana Malik',
        'batch_info' => 'Class of 2025',
        'photo' => '',
        'achievement' => '',
        'published' => 'Mar 21, 2026',
        'story' => "What stood out to me at EnglishKeys was the balance, serious exam preparation without losing sight of actually understanding the subject. The small batch sizes meant every student got individual feedback on their papers. I'd tell any junior student following behind: trust the process, show up for every test series, and ask for help the moment something feels unclear. It genuinely pays off by the time boards arrive.",
    ],
];
$displayStories = $stories ?: $dummyStories;
?>
<section id="stories">
  <div class="wrap">
    <div class="reveal" style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:34px">
      <div>
        <div class="kick">Alumni Stories</div>
        <h2 class="t" style="margin-bottom:0">In their own words, published by them.</h2>
      </div>
      <a class="btn btn-o" href="#share">Share your story</a>
    </div>
    <div class="g3">
      <?php foreach ($displayStories as $s): ?>
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
