<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();

// B1: the LIMIT 1 is gone - every active featured course is rendered.
$featured = $db->query("SELECT * FROM courses WHERE category = 'featured' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$subjects = $db->query("SELECT * FROM courses WHERE category = 'subject' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$programmes = $db->query("SELECT * FROM courses WHERE category = 'programme' AND is_active = 1 ORDER BY sort_order")->fetchAll();
$testimonials = $db->query('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order LIMIT 6')->fetchAll();

$whatsapp = getSetting('whatsapp_number');
$howTo = getContentBlock('courses', 'how_to_enrol_steps');
$googleUrl = getSetting('google_reviews_url');
$googleRating = getSetting('google_rating');
$googleCount = getSetting('google_review_count');

/**
 * One detail order used everywhere a featured course's meta chips appear -
 * Duration -> Level/Eligibility -> Mode -> Price -> Seats.
 */
function featuredMeta(array $c): string
{
    $rows = [
        ['meta-calendar', $c['duration'] ?? ''],
        ['meta-person',   $c['eligibility'] ?: ($c['level'] ?? '')],
        ['meta-mode',     $c['mode'] ?? ''],
        ['meta-price',    $c['price'] ?? ''],
        ['meta-seats',    $c['seats_info'] ?? ''],
    ];
    $out = '';
    foreach ($rows as [$ic, $val]) {
        if ($val !== '' && $val !== null) {
            $out .= '<span>' . icon($ic, 'icon') . ' ' . e($val) . '</span>';
        }
    }
    return $out ? '<div class="meta">' . $out . '</div>' : '';
}
?>

/**
 * One meta-row builder reused by both the Featured cards and the
 * Programme cards - each passes the icon/value pairs it needs, in the
 * order its own reference layout uses.
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
 * Featured course schedule grid. schedule_info holds "Label:Value" pairs
 * separated by "|" (admin/courses.php help text explains the convention);
 * a plain free-text value with no ":" still renders as one cell, so older
 * data never breaks.
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

/**
 * Featured course curriculum modules. `modules` holds blocks of
 * "Label|Title|bullet one\nbullet two", blocks separated by a line
 * containing only "---". Empty/unset renders nothing.
 */
function parseModules(string $raw): array
{
    $blocks = [];
    $raw = trim($raw);
    if ($raw === '') return $blocks;
    foreach (preg_split('/\r?\n-{3,}\r?\n/', $raw) as $chunk) {
        $lines = explode("\n", trim($chunk));
        if (!$lines || trim($lines[0]) === '') continue;
        [$label, $title, $firstBullet] = array_pad(explode('|', array_shift($lines), 3), 3, '');
        $bullets = array_values(array_filter(array_map('trim', array_merge([$firstBullet], $lines)), fn($b) => $b !== ''));
        if ($title === '') continue;
        $blocks[] = ['label' => trim($label), 'title' => trim($title), 'bullets' => $bullets];
    }
    return $blocks;
}

// Programme accordion: group by programme_group (admin/courses.php field),
// falling back to a single "Programmes" group for rows that don't set one
// so nothing silently disappears. Color/icon per group cycle through a
// fixed 4-style palette matched to the reference, not stored per-course.
$programmeGroups = [];
foreach ($programmes as $p) {
    $group = trim((string)($p['programme_group'] ?? '')) ?: 'Programmes';
    $programmeGroups[$group][] = $p;
}
$groupStyles = [
    ['color' => '#1E2A66', 'rgb' => '30,42,102', 'icon' => 'book-spine'],
    ['color' => '#E56A19', 'rgb' => '229,106,25', 'icon' => 'check-circle'],
    ['color' => '#7A3FD0', 'rgb' => '122,63,208', 'icon' => 'star-badge'],
    ['color' => '#1B7FB4', 'rgb' => '27,127,180', 'icon' => 'lightning'],
];
?>
<main class="page-courses">
<div class="phero">
  <div class="wrap reveal" data-anim="scale-in">
    <h1>Built around the FBISE syllabus, <span class="hl">nothing wasted.</span></h1>
    <p class="sub">Complete preparation for Classes 9-12 across four subjects, plus seasonal intensives, bootcamps and crash courses.</p>
  </div>
</div>

<?php if ($featured): ?>
<?php foreach ($featured as $f):
  $scheduleCells = scheduleCells((string)($f['schedule_info'] ?? ''));
  if (!empty($f['eligibility'])) $scheduleCells[] = ['label' => 'Eligibility', 'value' => $f['eligibility']];
  if (!empty($f['mode'])) $scheduleCells[] = ['label' => 'Mode', 'value' => $f['mode']];
  if (!empty($f['price'])) $scheduleCells[] = ['label' => 'Fee', 'value' => $f['price']];
  $modules = parseModules((string)($f['modules'] ?? ''));
<nav class="jumpnav wrap reveal" aria-label="Section navigation">
  <span class="jumpnav-label"><?= icon('list', 'icon') ?> On this page</span>
  <?php if ($featured): ?><a href="#featured"><span>Featured Courses</span></a><?php endif; ?>
  <?php if ($subjects): ?><a href="#subjects"><span>Core Subjects</span></a><?php endif; ?>
  <?php if ($programmes): ?><a href="#programmes"><span>Programmes</span></a><?php endif; ?>
  <a href="#enrol"><span>How to Enrol</span></a>
</nav>

<?php if ($featured):
  $primaryFeatured = $featured[0];
?>
<section id="featured">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Featured, Enrolling Now</div>
      <h2 class="t"><?= e($f['title']) ?><?php if ($f['tag_line']): ?>, <span class="hl"><?= e($f['tag_line']) ?></span><?php endif; ?></h2>
      <?php if ($f['description']): ?><p class="sub"><?= e($f['description']) ?></p><?php endif; ?>
    </div>
    <div class="g2 reveal fcgrid">
      <div class="card fcard-a">
        <?php if ($scheduleCells): ?>
          <div class="fcgrid-sched">
            <?php foreach ($scheduleCells as $cell): ?>
              <div><small><?= e($cell['label']) ?></small><b><?= e($cell['value']) ?></b></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="fcta">
          <a class="btn btn-o ar" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Enrol on WhatsApp&nbsp;</a>
          <a class="btn btn-l" href="contact.php">Ask a Question</a>
        </div>
        <?php if ($f['seats_info']): ?><p class="fc-note"><?= e($f['seats_info']) ?></p><?php endif; ?>
      </div>
      <div class="card fcard-b">
        <?php if ($f['highlights']): ?>
          <h3>Course highlights</h3>
          <div class="fcheck-grid">
            <?php foreach (explode("\n", $f['highlights']) as $h): if (trim($h) === '') continue; ?>
              <div class="fcheck"><?= icon('check') ?><span><?= e(trim($h)) ?></span></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <?php if ($modules): ?>
          <div class="fmodules">
            <?php foreach ($modules as $i => $m): ?>
              <?php if ($i > 0): ?><div class="fmod-div"></div><?php endif; ?>
              <div class="mod">
                <b class="mh"><?= e($m['label']) ?></b>
                <h3><?= e($m['title']) ?></h3>
                <?php if ($m['bullets']): ?>
                  <ul><?php foreach ($m['bullets'] as $b): ?><li><?= e($b) ?></li><?php endforeach; ?></ul>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <h2 class="t" style="max-width:32ch">
        <?= e($primaryFeatured['title']) ?><?php if ($primaryFeatured['tag_line']): ?>,<br><span class="hl-mark"><?= e($primaryFeatured['tag_line']) ?></span><?php endif; ?>
      </h2>
      <?php if ($primaryFeatured['description']): ?>
        <p style="margin-top:14px;color:var(--muted);font-size:16.5px;max-width:56ch"><?= e($primaryFeatured['description']) ?></p>
      <?php endif; ?>
    </div>
    <div class="g2 reveal" style="margin-top:30px">
      <?php foreach ($featured as $i => $f): ?>
        <div class="card fcard">
          <span class="fbadge">Enrolling Now</span>
          <h3 class="ptitle"><?= e($f['title']) ?><?php if ($f['tag_line']): ?>, <span class="hl"><?= e($f['tag_line']) ?></span><?php endif; ?></h3>
          <?php if ($f['description']): ?><p class="pdesc"><?= e($f['description']) ?></p><?php endif; ?>
          <?= featuredMeta($f) ?>
          <?php if ($f['schedule_info'] || $f['highlights']): ?>
            <button type="button" class="btn btn-l fdetails-toggle" data-target="fdetails-<?= (int)$f['id'] ?>" aria-expanded="false">
              View Details <span class="chev">&#9660;</span>
            </button>
            <div class="fdetails" id="fdetails-<?= (int)$f['id'] ?>">
              <?php if ($f['schedule_info']): ?>
                <div class="detgrid">
                  <?php foreach (explode(' - ', $f['schedule_info']) as $part): ?>
                    <div class="det"><b><?= e(trim($part)) ?></b></div>
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
            <a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Enrol on WhatsApp</a>
            <a class="btn btn-l" href="contact.php">Ask a Question</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>
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
    <div class="g2 reveal" style="margin-top:30px">
      <?php foreach ($subjects as $s): ?>
        <div class="card scard" style="--c:<?= e($s['accent_color']) ?>">
          <div class="num">0<?= (int)$s['sort_order'] ?>, <?= e($s['level']) ?></div>
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

<?php if ($programmeGroups): ?>
<?php if ($programmes):
  // Bucket programmes by their admin-assigned group (courses.programme_group_id
  // -> programme_groups table). Ungrouped rows fall into a generic "Other
  // Programmes" bucket rather than being hidden - nothing hardcoded, nothing lost.
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
  // First group gets the orange accent (soonest/most prominent), the rest navy -
  // matches the approved design's own pattern rather than a full colour rotation.
?>
<section id="programmes">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Seasonal &amp; Intensive</div>
      <h2 class="t">Programmes for every stage of the year.</h2>
    </div>
    <div class="cata-list reveal" style="margin-top:30px">
      <?php $gi = 0; foreach ($programmeGroups as $groupName => $items): $style = $groupStyles[$gi % count($groupStyles)]; $gi++; ?>
        <div class="cata" style="--c:<?= e($style['color']) ?>;--c-rgb:<?= e($style['rgb']) ?>">
          <button type="button" class="cata-hdr" aria-expanded="false" aria-controls="cata-bdy-<?= $gi ?>">
            <span class="cata-lbl"><?= icon($style['icon'], 'cata-ico') ?><?= e($groupName) ?></span>
            <span class="cata-count"><?= count($items) ?> programme<?= count($items) === 1 ? '' : 's' ?></span>
            <?= icon('chevron-down', 'cata-ic') ?>
          </button>
          <div class="cata-bdy" id="cata-bdy-<?= $gi ?>">
            <div class="cata-grid">
              <?php foreach ($items as $p): $detailId = 'pdetail-' . (int)$p['id']; ?>
                <div class="card pcard">
                  <?php if ($p['tag_line']): ?><span class="ptag"><?= icon('calendar', 'gic-sm') ?> <?= e($p['tag_line']) ?></span><?php endif; ?>
                  <h3 class="ptitle"><?= e($p['title']) ?></h3>
                  <?php if ($p['description']): ?><p class="pteaser"><?= e($p['description']) ?></p><?php endif; ?>
                  <?= courseMeta([
                    ['person', $p['level'] ?: $p['eligibility']],
                    ['calendar', $p['duration']],
                    ['card', $p['price']],
                    ['ticket', $p['seats_info']],
                  ]) ?>
                  <?php if ($p['description'] || $p['highlights']): ?>
                    <button type="button" class="pmore" aria-expanded="false" aria-controls="<?= e($detailId) ?>">
                      <span class="pmore-label">View details</span> <?= icon('chevron-down', 'pmore-ic') ?>
                    </button>
                    <div class="pdetail" id="<?= e($detailId) ?>">
                      <?php if ($p['description']): ?><p class="pdesc"><?= e($p['description']) ?></p><?php endif; ?>
                      <?php if ($p['highlights']): ?>
                        <ul class="pfeat"><?php foreach (explode("\n", $p['highlights']) as $h): if (trim($h) === '') continue; ?><li><?= e(trim($h)) ?></li><?php endforeach; ?></ul>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
    <div class="pgroups reveal" style="margin-top:30px">
      <?php $gi = 0; foreach ($groups as $g):
        if (empty($byGroup[$g['id']])) continue;
        $items = $byGroup[$g['id']];
        $accent = $gi === 0 ? 'var(--orange)' : 'var(--navy)';
        $gi++;
      ?>
        <?php require __DIR__ . '/includes/programme-group.php'; ?>
      <?php endforeach; ?>

      <?php if (!empty($byGroup[0])):
        $g = ['id' => 0, 'name' => 'Other Programmes', 'description' => '', 'date_range' => '', 'icon_key' => 'folder'];
        $items = $byGroup[0];
        $accent = $gi === 0 ? 'var(--orange)' : 'var(--navy)';
        $gi++;
      ?>
        <?php require __DIR__ . '/includes/programme-group.php'; ?>
      <?php endif; ?>
    </div>
    <div class="notebar reveal" style="margin-top:26px">
      <?= icon('meta-calendar', 'icon notebar-icon') ?>
      <p>Seats are limited each term to protect teaching quality. Message us on WhatsApp with your class and subjects for the current schedule and fees.</p>
      <a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Ask on WhatsApp</a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($howTo['content']): ?>
<section class="soft" id="enrol">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">How to Enrol</div>
      <h2 class="t">Four simple steps to your seat.</h2>
    </div>
    <div class="g4 reveal" style="margin-top:30px">
    <?php if ($howTo['content']): ?>
    <div class="stepper reveal" style="margin-top:36px">
      <?php $n = 0; foreach (explode("\n", $howTo['content']) as $line):
        if (trim($line) === '') continue;
        [$title, $desc] = array_pad(explode('|', $line, 2), 2, ''); $n++;
        $title = preg_replace('/^\d+\.\s*/', '', $title); ?>
        <div class="mcard">
          <div class="mno">0<?= $n ?></div>
        <div class="step">
          <div class="step-circle">0<?= $n ?></div>
          <h3><?= e(trim($title)) ?></h3>
          <p><?= e(trim($desc)) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($testimonials):
  $ratingPct = $googleRating ? max(0, min(100, round(((float)$googleRating / 5) * 100))) : 0;
?>
<section>
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Student Reviews</div>
      <h2 class="t">What students say, by subject &amp; course.</h2>
      <?php if ($googleRating && $googleCount): ?>
      <div class="rating-badge reveal" style="--pct:<?= (int)$ratingPct ?>%">
        <div class="rnum"><span class="count-num" data-target="<?= e($googleRating) ?>" data-decimals="1">0.0</span></div>
        <div class="stars-wrap">
          <div class="stars-base"><?= str_repeat(icon('star-sm', 'icon'), 5) ?></div>
          <div class="stars-fill"><?= str_repeat(icon('star-sm', 'icon'), 5) ?></div>
        </div>
        <div class="rating-copy">based on <span class="count-num" data-target="<?= (int)$googleCount ?>">0</span> genuine Google reviews</div>
      </div>
      <?php endif; ?>
    </div>
    <div class="g3">
      <?php foreach ($testimonials as $i => $t): ?>
        <div class="rcard reveal"<?= revealDelay($i) ?>>
          <?php if (!empty($t['course'])): ?><span class="ntag"><?= e($t['course']) ?></span><?php endif; ?>
          <div class="stars"><?= starRow((int)($t['rating'] ?: 5)) ?></div>
          <p>&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
          <div><b><?= e($t['name']) ?></b><br><span><?= e($t['source_label']) ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:32px;display:flex;gap:14px;flex-wrap:wrap" class="reveal">
      <?php if ($googleUrl): ?><a class="btn btn-n ar" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See Reviews on Google&nbsp;</a><?php endif; ?>
      <a class="btn btn-l ar" href="testimonials.php">Read All Reviews&nbsp;</a>
    <div class="reviews-grid reveal">
      <?php foreach ($testimonials as $t): ?>
        <div class="rcard-premium">
          <div class="rstars"><?= str_repeat(icon('star-sm', 'icon'), 5) ?></div>
          <p class="rtext"><?= e($t['quote']) ?></p>
          <div class="rfoot"><div><b><?= e($t['name']) ?></b><span><?= e($t['source_label']) ?></span></div></div>
        </div>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:36px;text-align:center;display:flex;gap:14px;justify-content:center;flex-wrap:wrap" class="reveal">
      <?php if ($googleUrl): ?><a class="btn btn-n" href="<?= e($googleUrl) ?>" target="_blank" rel="noopener">See Reviews on Google <span aria-hidden="true">&rarr;</span></a><?php endif; ?>
      <a class="btn btn-l" href="testimonials.php">Read All Reviews <span aria-hidden="true">&rarr;</span></a>
    </p>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
