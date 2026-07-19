<?php
$ctaWhatsapp = getSetting('whatsapp_number');
$ctaEmail = getSetting('email');
?>
<section class="cta">
  <div class="wrap cgrid">
    <div class="reveal">
      <h2>Ready to start your journey to first position?</h2>
      <p class="lead">One message is all it takes. Tell us your class and subjects, we reply within 3 hours on WhatsApp.</p>
      <div style="display:flex;gap:14px;flex-wrap:wrap">
        <a class="btn btn-o" href="https://wa.me/<?= e($ctaWhatsapp) ?>" target="_blank" rel="noopener">Message on WhatsApp</a>
        <a class="btn btn-w" href="courses.php">Browse Courses</a>
      </div>
    </div>
    <div class="ccard reveal">
      <h3>Admissions Open, FBISE 9-12</h3>
      <div class="cl"><span class="ic"><?= icon('chat') ?></span><div>WhatsApp: +<?= e($ctaWhatsapp) ?><small>Reply within 3 hours</small></div></div>
      <div class="cl"><span class="ic"><?= icon('mail') ?></span><div><?= e($ctaEmail) ?><small>Email us anytime</small></div></div>
      <div class="cl"><span class="ic"><?= icon('book') ?></span><div>English · Urdu · Islamiat · Tarjuma<small>Live classes + notes portal access</small></div></div>
    </div>
  </div>
</section>
