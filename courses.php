<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();

$featured = $db->query("SELECT * FROM courses WHERE category = 'featured' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$subjects = $db->query("SELECT * FROM courses WHERE category = 'subject' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$programmes = $db->query("SELECT * FROM courses WHERE category = 'programme' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$testimonials = $db->query('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order LIMIT 6')->fetchAll();

$whatsapp = getSetting('whatsapp_number');
$featuredIntro = getContentBlock('courses', 'featured_intro');
$googleUrl = getSetting('google_reviews_url');
$googleRating = getSetting('google_rating');
$googleCount = getSetting('google_review_count');

/**
 * One meta-row builder reused by the Featured cards - each passes the
 * icon/value pairs it needs, in the order the reference layout uses.
 */
function courseMeta(array $fields): string
{
    $out = '';
    foreach ($fields as [$ic, $val]) {
        if ($val !== '' && $val !== null) {
            $out .= '<span>' . icon($ic) . ' ' . e($val) . '</span>';
        }
    }
    return $out ? '<div class="meta">' . $out . '</div>' : '';
}

/**
 * Featured course detail grid. schedule_info holds "Label:Value" pairs
 * separated by "|" (admin/courses.php help text explains the convention);
 * a plain free-text value with no ":" still renders as one cell, so older
 * data never breaks. Only the value is shown (reference detgrid cells are
 * unlabeled bold text), the label is kept for a11y/title only.
 */
function scheduleCells(string $raw): array
{
    $cells = [];
    foreach (explode('|', $raw) as $part) {
        $part = trim($part);
        if ($part === '') continue;
        if (strpos($part, ':') !== false) {
            [$label, $value] = explode(':', $part, 2);
            $cells[] = ['label' => trim($label), 'value' => trim($value)];
        } else {
            $cells[] = ['label' => 'Schedule', 'value' => $part];
        }
    }
    return $cells;
}

// Programme accordion: bucket programmes by their admin-assigned group
// (courses.programme_group_id -> programme_groups, managed under
// Admin -> Programme Groups). Ungrouped rows fall into a generic "Other
// Programmes" bucket rather than being hidden. Accent colours cycle
// through a fixed palette matched to the approved design.
$byGroup = [];
foreach ($programmes as $p) {
    $byGroup[(int)($p['programme_group_id'] ?? 0)][] = $p;
}
$groupIds = array_filter(array_keys($byGroup));
$groups = [];
if ($groupIds) {
    $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
    $stmt = $db->prepare("SELECT * FROM programme_groups WHERE id IN ($placeholders) AND is_active = 1 ORDER BY sort_order, name");
    $stmt->execute($groupIds);
    $groups = $stmt->fetchAll();
}
$groupPalette = ['#EA6C1F', '#1E2A66', '#7A3FD0', '#1B7FB4'];
?>
<main class="page-courses">
<?php renderPageHero('courses'); ?>

<nav class="jumpnav wrap reveal" aria-label="Section navigation">
  <span class="jumpnav-label"><?= icon('list', 'icon') ?> On this page</span>
  <?php if ($featured): ?><a href="#featured"><span>Featured Courses</span></a><?php endif; ?>
  <?php if ($subjects): ?><a href="#subjects"><span>Core Subjects</span></a><?php endif; ?>
  <?php if ($programmes): ?><a href="#programmes"><span>Programmes</span></a><?php endif; ?>
  <?php if ($testimonials): ?><a href="#reviews"><span>Reviews</span></a><?php endif; ?>
</nav>

<?php if ($featured):
  [$fiTitle, $fiDesc] = array_pad(explode('|', (string)($featuredIntro['content'] ?? ''), 2), 2, '');
?>
<section id="featured">
  <div class="wrap">
    <?php if (trim($fiTitle) !== ''): ?>
    <div class="reveal">
      <div class="kick">Featured, Enrolling Now</div>
      <h2 class="t" style="max-width:32ch"><?= e(trim($fiTitle)) ?></h2>
      <?php if (trim($fiDesc) !== ''): ?><p class="sub"><?= e(trim($fiDesc)) ?></p><?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="g2 reveal" style="margin-top:30px">
      <?php foreach ($featured as $fi => $f):
        $scheduleCells = scheduleCells((string)($f['schedule_info'] ?? ''));
        $detailId = 'fdetails-' . (int)$f['id'];
      ?>
      <div class="card fcard">
        <span class="fbadge">Enrolling Now</span>
        <h3 class="ptitle"><?= e($f['title']) ?><?php if ($f['tag_line']): ?>, <span class="hl"><?= e($f['tag_line']) ?></span><?php endif; ?></h3>
        <?php if ($f['description']): ?><p class="pdesc"><?= e($f['description']) ?></p><?php endif; ?>

        <?= courseMeta([
          ['calendar', $f['duration']],
          ['person', $f['level'] ?: $f['eligibility']],
          ['book', $f['mode']],
          ['card', $f['price']],
          ['ticket', $f['seats_info']],
        ]) ?>

        <?php if ($scheduleCells || $f['highlights']): ?>
        <button type="button" class="btn btn-l fdetails-toggle" aria-controls="<?= e($detailId) ?>" aria-expanded="false">
          View Details <span class="chev">&#9660;</span>
        </button>

        <div class="fdetails" id="<?= e($detailId) ?>">
          <?php if ($scheduleCells): ?>
          <div class="detgrid">
            <?php foreach (array_slice($scheduleCells, 0, 3) as $cell): ?>
              <div class="det"><b><?= e($cell['value']) ?></b></div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <?php if ($f['highlights']): ?>
            <h4 style="font-size:15px;margin:16px 0 6px">Course highlights</h4>
            <?php foreach (explode("\n", $f['highlights']) as $h): if (trim($h) === '') continue; ?>
              <div class="check"><?= e(trim($h)) ?></div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px">
          <a class="btn btn-o" href="<?= e(waLink($whatsapp)) ?>" target="_blank" rel="noopener">Enroll on WhatsApp</a>
          <a class="btn btn-l" href="contact.php">Ask a Question</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($subjects): ?>
<section class="soft" id="subjects">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Core Subjects</div>
      <h2 class="t">Four subjects, mapped to your class.</h2>
    </div>
    <div class="g2" style="margin-top:30px">
      <?php foreach ($subjects as $i => $s): ?>
        <div class="card scard reveal"<?= revealDelay($i) ?> style="--c:<?= e($s['accent_color']) ?>">
          <div class="num" style="color:<?= e($s['accent_color']) ?>">0<?= (int)$s['sort_order'] ?>, <?= e($s['level']) ?></div>
          <h3><?= e($s['title']) ?></h3>
          <p><?= e($s['description']) ?></p>
          <?php if ($s['tag_line']): ?>
            <div class="tags"><?php foreach (explode(' - ', $s['tag_line']) as $tag): ?><span class="tag"><?= e(trim($tag)) ?></span><?php endforeach; ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($programmes): ?>
<section id="programmes">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Seasonal &amp; Intensive</div>
      <h2 class="t">Programmes for every stage of the year.</h2>
    </div>
    <div class="pgroups reveal" style="margin-top:30px">
      <?php $gi = 0;
      foreach ($groups as $g):
        if (empty($byGroup[$g['id']])) continue;
        $items = $byGroup[$g['id']];
        $accent = $groupPalette[$gi % count($groupPalette)];
        $gi++;
      ?>
        <?php require __DIR__ . '/includes/programme-group.php'; ?>
      <?php endforeach; ?>

      <?php if (!empty($byGroup[0])):
        $g = ['id' => 0, 'name' => 'Other Programmes', 'description' => '', 'date_range' => '', 'icon_key' => 'folder'];
        $items = $byGroup[0];
        $accent = $groupPalette[$gi % count($groupPalette)];
        $gi++;
      ?>
        <?php require __DIR__ . '/includes/programme-group.php'; ?>
      <?php endif; ?>
    </div>
    <div class="notebar reveal" style="margin-top:26px">
      <?= icon('calendar', 'icon notebar-icon') ?>
      <p>Seats are limited each term to protect teaching quality. Message us on WhatsApp with your class and subjects for the current schedule and fees.</p>
      <a class="btn btn-o" href="<?= e(waLink($whatsapp)) ?>" target="_blank" rel="noopener">Ask on WhatsApp</a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($testimonials): ?>
<section class="soft" id="reviews">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Student Reviews</div>
      <h2 class="t">What students say, by subject &amp; course.</h2>
      <?php if ($googleUrl && $googleRating): ?>
      <div class="rating-badge reveal" style="--pct:<?= (float)$googleRating * 20 ?>%">
        <div class="rnum"><span class="count-num" data-target="<?= e($googleRating) ?>" data-decimals="1">0.0</span></div>
        <div class="stars-wrap">
          <div class="stars-base"><?= str_repeat(icon('star'), 5) ?></div>
          <div class="stars-fill"><?= str_repeat(icon('star'), 5) ?></div>
        </div>
        <div class="rating-copy">based on <span class="count-num" data-target="<?= (int)$googleCount ?>">0</span> genuine Google reviews</div>
      </div>
      <?php endif; ?>
    </div>
    <div class="reviews-grid reveal">
      <?php foreach ($testimonials as $t): ?>
        <div class="rcard-premium">
          <div class="rstars"><?= starRow((int)($t['rating'] ?: 5)) ?></div>
          <p class="rtext">&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
          <div class="rfoot">
            <div><b><?= e($t['name']) ?></b><span><?= e($t['source_label']) ?></span></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:36px;display:flex;gap:14px;flex-wrap:wrap" class="reveal">
      <?php if ($googleUrl): ?><a class="btn btn-n ar" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See Reviews on Google&nbsp;</a><?php endif; ?>
      <a class="btn btn-l ar" href="testimonials.php">Read All Reviews&nbsp;</a>
    </p>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
