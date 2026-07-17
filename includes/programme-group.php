<?php
/**
 * Renders one collapsible programme-group accordion panel. Included from
 * courses.php with $g (group row), $items (its programme rows), $accent,
 * and $gi already in scope - not meant to be requested directly.
 */
?>
<div class="pgroup" style="--c:<?= $accent ?>">
  <button type="button" class="pghead" data-target="pgpanel-<?= (int)$g['id'] ?>" aria-expanded="false">
    <span class="pgicon"><?= icon($g['icon_key'] ?: 'folder', '') ?></span>
    <span class="pgbody">
      <?php if (!empty($g['date_range'])): ?><span class="pgspan"><?= e($g['date_range']) ?></span><?php endif; ?>
      <h3><?= e($g['name']) ?></h3>
      <?php if (!empty($g['description'])): ?><p><?= e($g['description']) ?></p><?php endif; ?>
    </span>
    <span class="pgcount"><b><?= count($items) ?></b><span>Programme<?= count($items) === 1 ? '' : 's' ?></span></span>
    <span class="pgchev"><?= icon('chevron-sm', '') ?></span>
  </button>
  <div class="pgpanel" id="pgpanel-<?= (int)$g['id'] ?>">
    <div class="pgrid">
      <?php foreach ($items as $p):
        $pmeta = array_filter([
            $p['eligibility'] ?: ($p['level'] ?? ''),
            $p['duration'] ?? '',
            $p['price'] ?? '',
            $p['seats_info'] ?? '',
        ], static fn($v) => $v !== '' && $v !== null);
      ?>
        <div class="pcard" style="--c:<?= $accent ?>">
          <?php if ($p['tag_line']): ?><span class="ntag"><?= e($p['tag_line']) ?></span><?php endif; ?>
          <h4 class="ptitle"><?= e($p['title']) ?></h4>
          <?php if ($p['description']): ?><p class="pdesc"><?= e($p['description']) ?></p><?php endif; ?>
          <?php if ($p['highlights']): ?>
            <ul class="pfeat"><?php foreach (explode("\n", $p['highlights']) as $h): if (trim($h) === '') continue; ?><li><?= e(trim($h)) ?></li><?php endforeach; ?></ul>
          <?php endif; ?>
          <?php if ($pmeta): ?>
            <div class="meta"><?php foreach ($pmeta as $val): ?><span><?= e($val) ?></span><?php endforeach; ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
