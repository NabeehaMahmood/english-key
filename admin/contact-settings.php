<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

// Everything the public site shows for "how to reach / pay us": the footer
// Get-in-Touch column, the Contact page, the Enroll page's payment sidebar,
// the WhatsApp floating button and the chatbot's contact answers all read
// these same keys, so one save here updates every page at once.
$contactFields = [
    'phone' => 'Phone',
    'phone_2' => 'Phone (second number)',
    'whatsapp_number' => 'WhatsApp Number (digits only, e.g. 923111537563)',
    'email' => 'Email',
    'address' => 'Address / Location Line',
    'office_hours' => 'Office Hours (e.g. Office hours 10 AM - 10 PM PKT)',
];
$paymentFields = [
    'bank_name' => 'Bank Name',
    'bank_title' => 'Bank Account Title',
    'bank_iban' => 'Bank IBAN',
    'easypaisa_name' => 'EasyPaisa Account Name',
    'easypaisa_number' => 'EasyPaisa Number',
    'jazzcash_name' => 'JazzCash Account Name (optional)',
    'jazzcash_number' => 'JazzCash Number (optional)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $stmt = $db->prepare(
        'INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    foreach (array_merge($contactFields, $paymentFields) as $key => $label) {
        $stmt->execute([$key, trim($_POST[$key] ?? '')]);
    }

    // The Contact page's hero intro paragraph, How-to-Enroll steps and terms.
    $blockStmt = $db->prepare(
        'INSERT INTO content_blocks (page_slug, block_key, content) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE content = VALUES(content)'
    );
    $blockStmt->execute(['contact', 'intro', trim($_POST['contact_intro'] ?? '')]);
    $blockStmt->execute(['courses', 'how_to_enroll_steps', trim($_POST['how_to_enroll_steps'] ?? '')]);
    $blockStmt->execute(['courses', 'terms_conditions', trim($_POST['terms_conditions'] ?? '')]);

    try {
        $qrPath = handleImageUpload('qr_code', 'payments');
        if ($qrPath) {
            $stmt->execute(['qr_code_image', $qrPath]);
        }
        redirectWithMessage('contact-settings.php', 'Contact info updated.');
    } catch (RuntimeException $e) {
        redirectWithMessage('contact-settings.php', $e->getMessage(), 'error');
    }
}

$settings = [];
foreach ($db->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$contactIntro = getContentBlock('contact', 'intro')['content'] ?? '';
$howTo = getContentBlock('courses', 'how_to_enroll_steps')['content'] ?? '';
$terms = getContentBlock('courses', 'terms_conditions')['content'] ?? '';
?>
<h1>Contact Info &amp; Payments</h1>
<p class="admin-page-intro">One place for every phone number, email and payment account shown on the site: the footer, the Contact page and the chatbot all update together when you save here.</p>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>

  <h2>Contact details</h2>
  <?php foreach ($contactFields as $key => $label): ?>
    <label><?= e($label) ?>
      <input type="text" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>">
    </label>
  <?php endforeach; ?>

  <label>Contact Page Intro (the paragraph under the Contact page heading)
    <textarea name="contact_intro" rows="3"><?= e($contactIntro) ?></textarea>
  </label>

  <h2>Payment accounts</h2>
  <?php foreach ($paymentFields as $key => $label): ?>
    <label><?= e($label) ?>
      <input type="text" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>">
    </label>
  <?php endforeach; ?>

  <label>Payment QR Code (optional image shown with payment details)
    <?php if (!empty($settings['qr_code_image'])): ?>
      <div><img src="../<?= e($settings['qr_code_image']) ?>" alt="Current QR code" style="max-height:120px;"></div>
    <?php endif; ?>
    <input type="file" name="qr_code" accept=".jpg,.jpeg,.png,.webp">
  </label>

  <h2>How to Enroll</h2>
  <label>Steps shown on the Contact page (one per line, format: 01. Title|Description)
    <textarea name="how_to_enroll_steps" rows="6"><?= e($howTo) ?></textarea>
  </label>

  <h2>Terms &amp; conditions</h2>
  <label>Terms shown on the Contact page (one line per term)
    <textarea name="terms_conditions" rows="6"><?= e($terms) ?></textarea>
  </label>

  <button type="submit">Save Contact Info</button>
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
