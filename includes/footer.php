<?php
/**
 * Site-wide footer (Maaz's design, used on every page). All text, contact
 * details and social links are admin-editable: Admin -> Footer and
 * Admin -> Contact Info. The Programs column lists live active programmes
 * from the courses table, so it never goes stale.
 */
$phone = getSetting('phone');
$phone2 = getSetting('phone_2');
$email = getSetting('email');
$whatsapp = getSetting('whatsapp_number');
$footerText = getSetting('footer_text');
$footerNote = getSetting('footer_note');
$footerDescription = getSetting('footer_description', 'Online coaching in English, Urdu, Islamiat & Tarjuma-tul-Quran for FBISE students, Classes 9-12, taught live across Pakistan.');
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
<footer>
  <div class="wrap">
    <div class="fg">
      <div class="fb">
        <?php if ($logoPath): ?><img class="flogo" src="<?= e($assetBase . $logoPath) ?>" alt="<?= e($siteName) ?>"><?php endif; ?>
        <b><?= e(getSetting('tagline', 'Where Words Build Futures')) ?>.</b>
        <p><?= e($footerDescription) ?></p>
        <div class="fsoc">
          <?php if ($fb): ?><a href="<?= e($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook" data-brand="facebook"><svg viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.6 9.95v-7.04H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.77-3.9 1.1 0 2.24.2 2.24.2v2.46H15.1c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.91h-2.34v7.04A10 10 0 0 0 22 12z"/></svg></a><?php endif; ?>
          <?php if ($ig): ?><a href="<?= e($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram" data-brand="instagram"><svg viewBox="0 0 24 24"><path d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9a5.5 5.5 0 0 1-5.5 5.5h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2zm0 2A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9a3.5 3.5 0 0 0 3.5-3.5v-9A3.5 3.5 0 0 0 16.5 4h-9zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm5.3-3.2a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4z"/></svg></a><?php endif; ?>
          <?php if ($yt): ?><a href="<?= e($yt) ?>" target="_blank" rel="noopener" aria-label="YouTube<?= $ytSubs ? ', ' . e($ytSubs) . ' subscribers' : '' ?>" data-brand="youtube"><svg viewBox="0 0 24 24"><path d="M23.2 7.3s-.22-1.58-.9-2.27c-.87-.91-1.84-.92-2.28-.97C16.83 3.83 12 3.83 12 3.83h-.01s-4.82 0-8.01.23c-.45.05-1.42.06-2.28.97-.68.69-.9 2.27-.9 2.27S.57 9.16.57 11.02v1.74c0 1.86.23 3.72.23 3.72s.22 1.58.9 2.27c.87.91 2.01.88 2.52.98 1.83.17 7.78.23 7.78.23s4.83-.01 8.02-.24c.44-.05 1.41-.06 2.28-.97.68-.69.9-2.27.9-2.27s.23-1.86.23-3.72v-1.74c0-1.86-.23-3.72-.23-3.72zM9.65 15.14V8.6l6.16 3.28-6.16 3.26z"/></svg></a><?php endif; ?>
          <?php if ($li): ?><a href="<?= e($li) ?>" target="_blank" rel="noopener" aria-label="LinkedIn" data-brand="linkedin"><svg viewBox="0 0 24 24"><path d="M4.98 3.5a2.49 2.49 0 1 1-.02 4.98 2.49 2.49 0 0 1 .02-4.98zM3 9.25h4V21H3zM9.25 9.25H13v1.61h.05c.52-.99 1.8-2.03 3.71-2.03 3.97 0 4.7 2.61 4.7 6.01V21h-3.96v-5.46c0-1.3-.02-2.98-1.81-2.98-1.82 0-2.1 1.42-2.1 2.88V21H9.25z"/></svg></a><?php endif; ?>
        </div>
      </div>
      <div>
        <h4>Explore</h4>
        <div class="fl">
          <a href="index.php">Home</a>
          <a href="courses.php">Courses</a>
          <a href="notes.php">Notes</a>
          <a href="blog.php">Blog</a>
          <a href="testimonials.php">Testimonials</a>
          <a href="alumni.php">Alumni</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
        </div>
      </div>
      <div>
        <h4>Programs</h4>
        <div class="fl">
          <?php foreach ($footerProgrammes as $p): ?><a href="courses.php#programmes"><?= e($p['title']) ?></a><?php endforeach; ?>
        </div>
      </div>
      <div>
        <h4>Get in Touch</h4>
        <div class="fl">
          <?php if ($phone): ?><a href="tel:<?= e(preg_replace('/\D/', '', $phone)) ?>"><?= e($phone) ?></a><?php endif; ?>
          <?php if ($phone2): ?><a href="tel:<?= e(preg_replace('/\D/', '', $phone2)) ?>"><?= e($phone2) ?></a><?php endif; ?>
          <?php if ($email): ?><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a><?php endif; ?>
          <?php if ($whatsapp): ?><a href="<?= e(waLink($whatsapp)) ?>" target="_blank" rel="noopener">WhatsApp, reply within 3 hours</a><?php endif; ?>
          <?php if ($googleUrl): ?><a href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See our Google Reviews (<?= e($googleRating) ?> <?= icon('star', 'icon star-icon') ?>)</a><?php endif; ?>
          <?php if ($yt): ?><a href="<?= e($yt) ?>" target="_blank" rel="noopener">YouTube, <?= e($ytSubs) ?> subscribers</a><?php endif; ?>
        </div>
      </div>
    </div>
    <div class="fbase">
      <span>&copy; <?= date('Y') ?> <?= e($siteName) ?>. <?= e($footerText) ?></span>
      <span><?= e($footerNote) ?></span>
    </div>
  </div>
</footer>
<?php if ($whatsapp): ?>
<a class="wa" href="<?= e(waLink($whatsapp)) ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
  <svg viewBox="0 0 32 32"><path d="M16 3C9.4 3 4 8.4 4 15c0 2.6.8 5 2.3 7L4 29l7.2-2.2c1.9 1 4 1.6 6.1 1.6h.7c6.6 0 12-5.4 12-12S22.6 3 16 3zm6.6 17c-.3.8-1.6 1.5-2.3 1.6-.6.1-1.3.2-2.1-.1-.5-.2-1.1-.4-1.9-.7-3.4-1.5-5.6-4.9-5.8-5.1-.2-.2-1.4-1.8-1.4-3.5s.9-2.5 1.2-2.8c.3-.3.7-.4.9-.4h.6c.2 0 .5-.1.7.5.3.7.9 2.3 1 2.5.1.2.1.4 0 .6-.1.2-.2.4-.4.6-.2.2-.4.5-.5.6-.2.2-.4.4-.2.7.2.4.9 1.5 2 2.4 1.3 1.2 2.5 1.6 2.8 1.7.4.2.6.1.8-.1.2-.2.9-1 1.1-1.4.2-.4.5-.3.8-.2.3.1 2 1 2.4 1.1.4.2.6.3.7.4.1.3.1.9-.2 1.6z"/></svg>
</a>
<?php endif; ?>
<?php
// Live values for the chat widget's built-in safety-net answers ({{token}}
// placeholders in assets/js/chatbot.js). The real AI answering (chat.php ->
// buildChatFacts()) reads the database directly and needs no feed here.
$chatInfo = buildChatTokens();
?>
<script>window.EKA_INFO = <?= json_encode($chatInfo, JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="<?= e($assetVer('assets/js/chatbot.js')) ?>" defer></script>
<script src="<?= e($assetVer('assets/js/site.js')) ?>" defer></script>
</body>
</html>
