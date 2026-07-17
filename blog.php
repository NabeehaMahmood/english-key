<?php
require_once __DIR__ . '/includes/header.php';

$posts = getDb()->query('SELECT * FROM blog_posts WHERE is_active = 1 ORDER BY published_at DESC')->fetchAll();
?>

<div class="phero">
  <div class="wrap reveal">
    <div class="kick">Blog</div>
    <h1>Exam tips, study routines &amp; <span class="hl">board updates.</span></h1>
    <p class="sub">Short, practical articles on exam technique and grammar, written to help FBISE students score higher. New pieces published through the term.</p>
  </div>
</div>

<section>
  <div class="wrap">
    <div class="g3">
      <?php foreach ($posts as $post): ?>
        <div class="bcard2 reveal">
          <div class="bhead"><?php if ($post['category']): ?><span class="otag"><?= e($post['category']) ?></span><?php endif; ?></div>
          <div class="bbody">
            <?php if ($post['image']): ?><img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" style="width:100%;border-radius:8px;margin-bottom:12px"><?php endif; ?>
            <h3><?= e($post['title']) ?></h3>
            <p><?= e(mb_strimwidth((string)$post['content'], 0, 140, '...')) ?></p>
            <span class="meta"><?= e(date('M j, Y', strtotime($post['published_at']))) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (!$posts): ?><p>New articles are on the way, check back soon.</p><?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
