<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDb();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (honeypotTripped()) {
        redirectWithMessage('enroll.php', 'Thank you! Your enrolment enquiry has been received.');
    }

    $student = trim($_POST['student'] ?? '');
    $guardian = trim($_POST['guardian'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $whatsappField = trim($_POST['whatsapp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $klass = trim($_POST['klass'] ?? '');
    $board = trim($_POST['board'] ?? '');
    $subjects = implode(', ', array_map('trim', $_POST['subjects'] ?? []));
    $programme = trim($_POST['programme'] ?? '');
    $start = trim($_POST['start'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($student === '') $errors[] = 'Please enter the student\'s full name.';
    if ($klass === '') $errors[] = 'Please select a class.';
    if ($phone === '' && $whatsappField === '') $errors[] = 'Please give at least one contact number.';
    if (!humanCheckPassed()) $errors[] = 'Human check failed, please try again.';

    if (!$errors) {
        $stmt = $db->prepare(
            'INSERT INTO enrollments (student_name, guardian_name, phone, whatsapp, email, city, class_level, board, subjects, programme, preferred_start, notes)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([$student, $guardian, $phone, $whatsappField, $email, $city, $klass, $board, $subjects, $programme, $start, $notes]);

        notifyAdmin(
            'New enrolment enquiry - ' . getSetting('site_name', 'EnglishKeys Academy'),
            "Student: $student\nGuardian: $guardian\nPhone: $phone\nWhatsApp: $whatsappField\nEmail: $email\nCity: $city\nClass: $klass\nBoard: $board\nSubjects: $subjects\nProgramme: $programme\nPreferred start: $start\nNotes: $notes"
        );

        redirectWithMessage('enroll.php', 'Thank you! Your enrolment enquiry has been received, we will reply within 3 hours.');
    }
}

require_once __DIR__ . '/includes/header.php';

$whatsapp = getSetting('whatsapp_number');
$bankName = getSetting('bank_name');
$bankTitle = getSetting('bank_title');
$bankIban = getSetting('bank_iban');
$easypaisaName = getSetting('easypaisa_name');
$easypaisaNumber = getSetting('easypaisa_number');
$humanQuestion = humanCheckQuestion();

$classes = ['Class 9', 'Class 10', 'Class 11', 'Class 12', 'Other'];
$subjectOptions = ['English', 'Urdu', 'Islamiat', 'Tarjuma-tul-Quran'];
$programmeOptions = ['Full syllabus (regular)', 'Summer Intensive', 'FBISE Bootcamp', 'Exam Marathon', 'Crash Course', 'Deen Camp (Islamiat & Quran)', 'MDCAT / NUMS English'];
?>

<?php renderPageHero('enroll'); ?>

<section>
  <div class="wrap ef-wrap">
    <form class="ef-form reveal" method="post" action="enroll.php" novalidate>
      <?= csrfField() ?>
      <input type="text" name="website" value="" class="ef-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
      <?php if ($errors): ?>
        <div class="flash flash-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <div class="ef-h">1 · Student details</div>
      <div class="ef-grid">
        <div class="ef-field"><label for="student">Student's full name <span class="ef-req">*</span></label><input id="student" name="student" type="text" maxlength="80" required></div>
        <div class="ef-field"><label for="guardian">Parent / guardian name</label><input id="guardian" name="guardian" type="text" maxlength="80"></div>
        <div class="ef-field"><label for="phone">Phone number</label><input id="phone" name="phone" type="tel" maxlength="30" placeholder="03xx-xxxxxxx"></div>
        <div class="ef-field"><label for="whatsapp">WhatsApp number</label><input id="whatsapp" name="whatsapp" type="tel" maxlength="30" placeholder="+92 3xx xxxxxxx"></div>
        <div class="ef-field"><label for="email">Email (optional)</label><input id="email" name="email" type="email" maxlength="120"></div>
        <div class="ef-field"><label for="city">City</label><input id="city" name="city" type="text" maxlength="60"></div>
      </div>
      <p class="ef-hint">Give us at least one number, phone or WhatsApp, so we can confirm your seat.</p>

      <div class="ef-h">2 · Class &amp; board</div>
      <div class="ef-grid">
        <div class="ef-field">
          <label for="klass">Class <span class="ef-req">*</span></label>
          <select id="klass" name="klass" required>
            <option value="">Select class...</option>
            <?php foreach ($classes as $c): ?><option value="<?= e($c) ?>"><?= e($c) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="ef-field"><label for="board">Board</label><input id="board" name="board" type="text" maxlength="60" placeholder="FBISE (Federal Board)"></div>
      </div>

      <div class="ef-h">3 · Subjects</div>
      <p class="ef-hint" style="margin-top:0">Pick the subjects you want to study. You can choose more than one.</p>
      <div class="ef-checks">
        <?php foreach ($subjectOptions as $subj): ?>
          <label class="ef-check"><input type="checkbox" name="subjects[]" value="<?= e($subj) ?>"><span><?= e($subj) ?></span></label>
        <?php endforeach; ?>
      </div>

      <div class="ef-h">4 · Programme &amp; timing</div>
      <div class="ef-grid">
        <div class="ef-field">
          <label for="programme">Programme</label>
          <select id="programme" name="programme">
            <option value="">Select a programme...</option>
            <?php foreach ($programmeOptions as $p): ?><option value="<?= e($p) ?>"><?= e($p) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="ef-field"><label for="start">Preferred start</label><input id="start" name="start" type="text" maxlength="60" placeholder="e.g. As soon as possible"></div>
      </div>
      <div class="ef-field" style="margin-top:6px"><label for="notes">Anything else? (optional)</label><textarea id="notes" name="notes" rows="4" maxlength="1000" placeholder="Questions, goals, availability, exam dates..."></textarea></div>
      <div class="ef-grid" style="margin-top:6px">
        <div class="ef-field"><label for="human">Quick check: <?= e($humanQuestion) ?> = ? <span class="ef-req">*</span></label><input id="human" name="human" type="text" inputmode="numeric" maxlength="3" required></div>
      </div>

      <div class="ef-actions">
        <button class="btn btn-o ef-submit" type="submit">Send enrolment enquiry</button>
        <a class="btn btn-l" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Prefer WhatsApp? Message us</a>
      </div>
      <p class="ef-hint">By sending this you agree we may contact you about enrolment. We only use your details to arrange your classes.</p>
    </form>

    <aside class="ef-side reveal">
      <h3>What happens next</h3>
      <ol class="ef-steps">
        <li><b>You send this form.</b> It reaches the academy inbox instantly.</li>
        <li><b>We reply within 3 hours</b> to confirm your seat and the current schedule and fee.</li>
        <li><b>You pay</b> via <?= e($bankName) ?> or EasyPaisa and send the screenshot on WhatsApp.</li>
        <li><b>You're in.</b> We share the class link and joining instructions.</li>
      </ol>
      <div class="ef-mini">
        <b>Payment details</b>
        <p><?= e($bankName) ?> - <?= e($bankTitle) ?><br>IBAN: <?= e($bankIban) ?></p>
        <p>EasyPaisa - <?= e($easypaisaName) ?><br><?= e($easypaisaNumber) ?></p>
      </div>
      <p class="ef-hint" style="margin:0">Seats are limited each term to protect teaching quality, so enrol early.</p>
    </aside>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
