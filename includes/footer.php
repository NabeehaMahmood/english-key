<?php
$phone = getSetting('phone');
$phone2 = getSetting('phone_2');
$email = getSetting('email');
$whatsapp = getSetting('whatsapp_number');
$footerText = getSetting('footer_text');
$footerNote = getSetting('footer_note');
$fb = getSetting('social_facebook');
$ig = getSetting('social_instagram');
$yt = getSetting('social_youtube');
$li = getSetting('social_linkedin');
$ytSubs = getSetting('youtube_subscribers');
$googleUrl = getSetting('google_reviews_url');
$googleRating = getSetting('google_rating');

// Footer "Programs" column: real active seasonal programmes, not a fixed list.
$footerProgrammes = getDb()->query("
    SELECT title FROM courses WHERE category = 'programme' AND is_active = 1 ORDER BY sort_order LIMIT 5
")->fetchAll();
?>
<footer class="footer-dark">
  <div class="wrap">

    <div class="footer-cta reveal">
      <div class="footer-cta-left">
        <h2>Ready to start your journey to first position?</h2>
        <p>One message is all it takes. Tell us your class and subjects, we reply within 3 hours on WhatsApp.</p>
        <div class="footer-cta-btns">
          <a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Message on WhatsApp</a>
          <a class="btn btn-ghost-dark" href="courses.php">Browse Courses</a>
        </div>
      </div>

      <div class="admissions-card">
        <h4>Admissions Open, FBISE 9-12</h4>
        <div class="finfo-row">
          <span class="ficon ficon-wa"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5.1-1.3A10 10 0 1012 2zm0 18.1a8 8 0 01-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1112 20.1zm4.5-6c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8 1-.2.2-.3.2-.5.1-1.4-.7-2.3-1.3-3.3-2.8-.2-.4.2-.4.6-1.2.1-.2 0-.3 0-.5-.1-.1-.6-1.5-.9-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.9 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.6-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2-.1-.1-.3-.2-.5-.3z"/></svg></span>
          <div><b>WhatsApp: +<?= e($whatsapp) ?></b><span>Reply within 3 hours</span></div>
        </div>
        <?php if ($email): ?>
        <div class="finfo-row">
          <span class="ficon ficon-mail"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="2.5" y="4" width="15" height="12" rx="2"/><path d="M2.5 5l7.5 6 7.5-6"/></svg></span>
          <div><b><?= e($email) ?></b><span>Email us anytime</span></div>
        </div>
        <?php endif; ?>
        <div class="finfo-row">
          <span class="ficon ficon-book"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 4.5c1.5-1 4-1 6 0v11c-2-1-4.5-1-6 0v-11zM17 4.5c-1.5-1-4-1-6 0v11c2-1 4.5-1 6 0v-11z"/></svg></span>
          <div><b>English &middot; Urdu &middot; Islamiat &middot; Tarjuma</b><span>Live classes + notes portal access</span></div>
        </div>
      </div>
    </div>

    <div class="footer-links reveal">
      <div>
        <div class="footer-brand-mark">
          <?php if ($logoPath): ?><img src="<?= e($logoPath) ?>" alt="<?= e($siteName) ?>"><?php endif; ?>
        </div>
        <h4><?= e(getSetting('tagline', 'Where Words Build Futures')) ?>.</h4>
        <p class="fdesc">Online coaching in English, Urdu, Islamiat &amp; Tarjuma-tul-Quran for FBISE students, Classes 9-12, taught live across Pakistan.</p>
        <div class="footer-social">
          <?php if ($fb): ?><a class="ico-fb" href="<?= e($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.6h2.6l.4-3h-3v-1.9c0-.87.24-1.46 1.5-1.46h1.6V4.35C15.9 4.24 15 4.16 14 4.16c-2.1 0-3.5 1.28-3.5 3.63v2.03H8v3h2.5V21h3z"/></svg></a><?php endif; ?>
          <?php if ($ig): ?><a class="ico-ig" href="<?= e($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1"/></svg></a><?php endif; ?>
          <?php if ($yt): ?><a class="ico-yt" href="<?= e($yt) ?>" target="_blank" rel="noopener" aria-label="YouTube<?= $ytSubs ? ', ' . e($ytSubs) . ' subscribers' : '' ?>"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.8 8.2c-.2-1.3-1.2-2.3-2.5-2.5C17.6 5.4 12 5.4 12 5.4s-5.6 0-7.3.3C3.4 5.9 2.4 6.9 2.2 8.2 2 9.9 2 12 2 12s0 2.1.2 3.8c.2 1.3 1.2 2.3 2.5 2.5 1.7.3 7.3.3 7.3.3s5.6 0 7.3-.3c1.3-.2 2.3-1.2 2.5-2.5.2-1.7.2-3.8.2-3.8s0-2.1-.2-3.8zM10 15.2V8.8l5.5 3.2-5.5 3.2z"/></svg></a><?php endif; ?>
          <?php if ($li): ?><a class="ico-li" href="<?= e($li) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.94 8.5H4.56V19h2.38V8.5zM5.75 4.6a1.38 1.38 0 100 2.76 1.38 1.38 0 000-2.76zM19.44 19h-2.38v-5.4c0-1.29-.46-2.17-1.6-2.17-.87 0-1.39.59-1.62 1.16-.08.2-.1.49-.1.77V19h-2.38s.03-8.86 0-9.78h2.38v1.39c.32-.49.88-1.18 2.14-1.18 1.56 0 2.73 1.02 2.73 3.21V19z"/></svg></a><?php endif; ?>
        </div>
      </div>

      <div class="footer-col">
        <h5>Explore</h5>
        <a href="index.php">Home</a>
        <a href="courses.php">Courses</a>
        <a href="notes.php">Notes</a>
        <a href="blog.php">Blog</a>
        <a href="testimonials.php">Testimonials</a>
        <a href="alumni.php">Alumni</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
      </div>

      <div class="footer-col">
        <h5>Programs</h5>
        <?php foreach ($footerProgrammes as $p): ?>
          <a href="courses.php#programmes"><?= e($p['title']) ?></a>
        <?php endforeach; ?>
      </div>

      <div class="footer-col">
        <h5>Get in Touch</h5>
        <?php if ($phone): ?><a class="fcontact" href="tel:<?= e(preg_replace('/\D/', '', $phone)) ?>"><?= e($phone) ?></a><?php endif; ?>
        <?php if ($phone2): ?><a class="fcontact" href="tel:<?= e(preg_replace('/\D/', '', $phone2)) ?>"><?= e($phone2) ?></a><?php endif; ?>
        <?php if ($email): ?><a class="fcontact" href="mailto:<?= e($email) ?>"><?= e($email) ?></a><?php endif; ?>
        <?php if ($whatsapp): ?><a class="fcontact" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">WhatsApp, reply within 3 hours</a><?php endif; ?>
        <?php if ($googleUrl): ?><a class="fcontact" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See our Google Reviews (<?= e($googleRating) ?>&#9733;)</a><?php endif; ?>
        <?php if ($yt): ?><a class="fcontact" href="<?= e($yt) ?>" target="_blank" rel="noopener">YouTube, <?= e($ytSubs) ?> subscribers</a><?php endif; ?>
      </div>
    </div>

    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> <?= e($siteName) ?>. <?= e($footerText) ?></span>
      <span><?= e($footerNote) ?></span>
    </div>

  </div>
</footer>

<?php if ($whatsapp): ?>
<a class="whatsapp-float" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener" aria-label="Message us on WhatsApp">
  <span class="wa-ring"></span>
  <span class="wa-ring wa-ring-2"></span>
  <span class="wa-icon">
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5.1-1.3A10 10 0 1012 2zm0 18.1a8 8 0 01-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1112 20.1zm4.5-6c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8 1-.2.2-.3.2-.5.1-1.4-.7-2.3-1.3-3.3-2.8-.2-.4.2-.4.6-1.2.1-.2 0-.3 0-.5-.1-.1-.6-1.5-.9-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.9 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.6-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2-.1-.1-.3-.2-.5-.3z"/></svg>
  </span>
  <span class="wa-tooltip">Chat with us</span>
</a>
<?php endif; ?>
<script src="assets/js/site.js" defer></script>
</body>
</html>
