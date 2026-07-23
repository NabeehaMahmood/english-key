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

$howTo = getContentBlock('courses', 'how_to_enroll_steps');
$termsConditions = getContentBlock('courses', 'terms_conditions');
$bankName = getSetting('bank_name');
$bankTitle = getSetting('bank_title');
$bankIban = getSetting('bank_iban');
$easypaisaName = getSetting('easypaisa_name');
$easypaisaNumber = getSetting('easypaisa_number');
$faqs = $db->query("SELECT * FROM faqs WHERE page_slug = 'courses' AND is_active = 1 ORDER BY sort_order, id")->fetchAll();
?>
<main class="page-contact">
<?php renderPageHero('contact'); ?>

<section>
  <div class="wrap ef-wrap">
    <form class="ef-form reveal" method="post" action="contact.php" novalidate>
      <?= csrfField() ?>
      <input type="text" name="website" value="" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off" aria-hidden="true">
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
        <a class="btn btn-ghost" href="<?= e(waLink($whatsapp)) ?>" target="_blank" rel="noopener">Or WhatsApp us</a>
      </div>
    </form>
    <aside class="ef-side reveal">
      <h3>Reach us directly</h3>
      <div class="ef-mini">
        <b>WhatsApp (fastest)</b>
        <p>+<?= e($whatsapp) ?> - we reply within 3 hours.</p>
        <a class="btn btn-o" href="<?= e(waLink($whatsapp)) ?>" target="_blank" rel="noopener" style="width:100%;justify-content:center">Chat on WhatsApp</a>
      </div>
      <div class="ef-mini"><b>Phone</b><p><?= e($phone) ?><br><?= e($phone2) ?></p></div>
      <div class="ef-mini"><b>Email</b><p><?= e($email) ?></p></div>
      <p class="ef-hint">All classes are online, on Pakistan Standard Time. <?= e($officeHours) ?>.</p>
    </aside>
  </div>
</section>

<?php if ($howTo['content']): ?>
<section class="soft" id="how-to-enroll">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">How to Enroll</div>
      <h2 class="t">Four simple steps to your seat.</h2>
    </div>
    <div class="stepper reveal" style="margin-top:36px">
      <?php $n = 0; foreach (explode("\n", $howTo['content']) as $line):
        if (trim($line) === '') continue;
        [$title, $desc] = array_pad(explode('|', $line, 2), 2, ''); $n++;
        $title = preg_replace('/^\d+\.\s*/', '', $title); ?>
        <div class="step">
          <div class="step-circle">0<?= $n ?></div>
          <h3><?= e(trim($title)) ?></h3>
          <p><?= e(trim($desc)) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="soft">
  <div class="wrap">
    <div class="g2 reveal">
      <div class="card">
        <h3 style="font-size:19px;margin-bottom:18px"><span class="gic-sm"><?= icon('card', 'icon') ?></span> Payment details</h3>
        <?php if ($bankName): ?>
          <div style="background:linear-gradient(135deg,var(--bg),#fff);border:1px solid var(--line);border-radius:12px;padding:16px 18px;margin-bottom:12px;border-left:4px solid var(--navy)">
            <b style="display:block;margin-bottom:4px;font-size:15px"><?= e($bankName) ?></b>
            <span style="font-size:14px;color:var(--muted)">Title: <?= e($bankTitle) ?><br>IBAN: <?= e($bankIban) ?></span>
          </div>
        <?php endif; ?>
        <?php if ($easypaisaName): ?>
          <div style="background:linear-gradient(135deg,var(--bg),#fff);border:1px solid var(--line);border-radius:12px;padding:16px 18px;margin-bottom:12px;border-left:4px solid var(--orange)">
            <b style="display:block;margin-bottom:4px;font-size:15px">EasyPaisa</b>
            <span style="font-size:14px;color:var(--muted)">Name: <?= e($easypaisaName) ?><br>Number: <?= e($easypaisaNumber) ?></span>
          </div>
        <?php endif; ?>
        <div class="notebar" style="margin-top:14px">
          <?= icon('calendar', 'icon notebar-icon') ?>
          <p>Send your payment screenshot on WhatsApp to activate your seat the same day.</p>
          <a class="btn btn-o" href="<?= e(waLink($whatsapp)) ?>" target="_blank" rel="noopener">Send screenshot on WhatsApp</a>
        </div>
      </div>
      <?php if ($termsConditions['content']): ?>
      <div class="card">
        <h3 style="font-size:19px;margin-bottom:18px"><span class="gic-sm"><?= icon('document', 'icon') ?></span> Terms &amp; conditions</h3>
        <?php foreach (explode("\n", $termsConditions['content']) as $term): if (trim($term) === '') continue; ?>
          <div class="check"><?= e(trim($term)) ?></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($faqs): ?>
<section id="faqs">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">FAQs</div>
      <h2 class="t">Frequently Asked Questions</h2>
    </div>
    <div class="faq-list reveal" style="margin-top:36px">
      <?php foreach ($faqs as $i => $f): $n = $i + 1; ?>
      <div class="faq-item">
        <button type="button" class="faq-q" aria-expanded="false" aria-controls="faq-panel-<?= $n ?>" id="faq-btn-<?= $n ?>">
          <span><?= e($f['question']) ?></span>
          <span class="faq-toggle" aria-hidden="true"></span>
        </button>
        <div class="faq-a" id="faq-panel-<?= $n ?>" role="region" aria-labelledby="faq-btn-<?= $n ?>">
          <div class="faq-a-inner"><p><?= e($f['answer']) ?></p></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
