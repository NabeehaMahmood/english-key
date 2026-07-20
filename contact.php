<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDb();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (honeypotTripped()) {
        redirectWithMessage('contact.php', 'Thank you for reaching out! We will get back to you soon.');
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') $errors[] = 'Please enter your name.';
    if ($message === '') $errors[] = 'Please enter a message.';
    if (!humanCheckPassed()) $errors[] = 'Human check failed, please try again.';

    if (!$errors) {
        $stmt = $db->prepare('INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?,?,?,?,?)');
        $stmt->execute([$name, $email, $phone, $subject, $message]);

        notifyAdmin(
            'New contact message - ' . getSetting('site_name', 'EnglishKeys Academy'),
            "Name: $name\nEmail: $email\nPhone: $phone\nSubject: $subject\nMessage: $message"
        );

        redirectWithMessage('contact.php', 'Thank you for reaching out! We will get back to you soon.');
    }
}

require_once __DIR__ . '/includes/header.php';

$phone = getSetting('phone');
$phone2 = getSetting('phone_2');
$email = getSetting('email');
$whatsapp = getSetting('whatsapp_number');
$officeHours = getSetting('office_hours');
$humanQuestion = humanCheckQuestion();
?>

<?php renderPageHero('contact'); ?>

<section>
  <div class="wrap ef-wrap">
    <form class="ef-form reveal" method="post" action="contact.php" novalidate>
      <?= csrfField() ?>
      <input type="text" name="website" value="" class="ef-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
      <?php if ($errors): ?>
        <div class="flash flash-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>
      <div class="ef-h">Send us a message</div>
      <div class="ef-grid">
        <div class="ef-field"><label for="c-name">Your name <span class="ef-req">*</span></label><input id="c-name" name="name" type="text" maxlength="80" required></div>
        <div class="ef-field"><label for="c-email">Email</label><input id="c-email" name="email" type="email" maxlength="120"></div>
        <div class="ef-field"><label for="c-phone">Phone / WhatsApp</label><input id="c-phone" name="phone" type="text" maxlength="30" placeholder="03xx-xxxxxxxx"></div>
        <div class="ef-field"><label for="c-subject">Subject</label><input id="c-subject" name="subject" type="text" maxlength="120" placeholder="e.g. Fee query, class timing"></div>
      </div>
      <div class="ef-h">Your message</div>
      <div class="ef-field"><label for="c-message">How can we help? <span class="ef-req">*</span></label><textarea id="c-message" name="message" maxlength="2000" required placeholder="Write your question here..."></textarea></div>
      <div class="ef-field" style="max-width:200px;margin-top:14px">
        <label for="c-human">Quick check: <?= e($humanQuestion) ?> = ? <span class="ef-req">*</span></label>
        <input id="c-human" name="human" type="text" inputmode="numeric" maxlength="2" required>
      </div>
      <div class="ef-actions">
        <button class="btn btn-o ef-submit" type="submit">Send message</button>
        <a class="btn btn-ghost" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Or WhatsApp us</a>
      </div>
    </form>
    <aside class="ef-side reveal">
      <h3>Reach us directly</h3>
      <div class="ef-mini">
        <b>WhatsApp (fastest)</b>
        <p>+<?= e($whatsapp) ?> - we reply within 3 hours.</p>
        <a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener" style="width:100%;justify-content:center">Chat on WhatsApp</a>
      </div>
      <div class="ef-mini"><b>Phone</b><p><?= e($phone) ?><br><?= e($phone2) ?></p></div>
      <div class="ef-mini"><b>Email</b><p><?= e($email) ?></p></div>
      <p class="ef-hint">All classes are online, on Pakistan Standard Time. <?= e($officeHours) ?>.</p>
    </aside>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
