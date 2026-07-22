<?php
require_once __DIR__ . '/includes/header.php';

$phone = getSetting('phone');
$phone2 = getSetting('phone_2');
$email = getSetting('email');
$whatsapp = getSetting('whatsapp_number');
$officeHours = getSetting('office_hours');

$howTo = getContentBlock('courses', 'how_to_enrol_steps');
$termsConditions = getContentBlock('courses', 'terms_conditions');
$bankName = getSetting('bank_name');
$bankTitle = getSetting('bank_title');
$bankIban = getSetting('bank_iban');
$easypaisaName = getSetting('easypaisa_name');
$easypaisaNumber = getSetting('easypaisa_number');
?>

<?php renderPageHero('contact'); ?>

<section>
  <div class="wrap ef-wrap">
    <form class="ef-form reveal" id="contact-form" novalidate>
      <div class="ef-h">Send us a message</div>
      <div class="ef-grid">
        <div class="ef-field"><label for="c-name">Your name <span class="ef-req">*</span></label><input id="c-name" name="name" type="text" maxlength="80"></div>
        <div class="ef-field"><label for="c-email">Email</label><input id="c-email" name="email" type="email" maxlength="120"></div>
        <div class="ef-field"><label for="c-phone">Phone / WhatsApp</label><input id="c-phone" name="phone" type="text" maxlength="30" placeholder="03xx-xxxxxxxx"></div>
        <div class="ef-field"><label for="c-subject">Subject</label><input id="c-subject" name="subject" type="text" maxlength="120" placeholder="e.g. Fee query, class timing"></div>
      </div>
      <div class="ef-h">Your message</div>
      <div class="ef-field"><label for="c-message">How can we help? <span class="ef-req">*</span></label><textarea id="c-message" name="message" maxlength="2000" placeholder="Write your question here..."></textarea></div>
      <div class="ef-actions">
        <button class="btn btn-o ef-submit" type="button" id="c-send-gmail" data-to="<?= e($email) ?>">Send message</button>
        <button class="btn btn-ghost" type="button" id="c-send-wa" data-wa="<?= e($whatsapp) ?>">Or WhatsApp us</button>
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
<section class="soft" id="how-to-enrol">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">How to Enrol</div>
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
          <?= icon('meta-calendar', 'icon notebar-icon') ?>
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

<script src="assets/js/contact.js" defer></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
