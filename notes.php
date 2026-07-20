<?php
require_once __DIR__ . '/includes/header.php';

$notes = getDb()->query('SELECT * FROM notes WHERE is_active = 1 ORDER BY sort_order')->fetchAll();
?>

<?php renderPageHero('notes'); ?>

<section>
  <div class="wrap">
    <div class="g3">
      <?php foreach ($notes as $n): ?>
        <div class="ncard2 reveal">
          <?= icon('document', 'icon fic') ?>
          <?php if ($n['subject_tag']): ?><span class="ntag"><?= e($n['subject_tag']) ?></span><?php endif; ?>
          <h3><?= e($n['title']) ?></h3>
          <p><?= e($n['description']) ?></p>
          <?php if ($n['link']): ?><a class="vlink" href="<?= e($n['link']) ?>" target="_blank" rel="noopener">Request access →</a><?php endif; ?>
        </div>
      <?php endforeach; ?>
      <?php if (!$notes): ?><p>Notes are being added, please check back soon.</p><?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
