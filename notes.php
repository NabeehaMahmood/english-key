<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();
$whatsapp = getSetting('whatsapp_number');

// note_classes is every primary-nav tab on the page, admin-editable in one
// place -- there's no separate "group" concept, just classes. Whether a
// class shows a subject sub-nav (Class 9-12) or lists its own samples
// directly (MDCAT, Summer Camp, Others, ...) is derived below from whether
// it actually has rows in note_class_subjects, not a stored flag. One
// shared sort_order scale positions them all in the nav.
$allClasses = $db->query('SELECT * FROM note_classes WHERE is_active = 1 ORDER BY sort_order, class_level')->fetchAll();

$classes = array_map(fn($cl) => (int)$cl['class_level'], $allClasses);
$classLabels = [];
$examLabels = [];
$classAccent = [];
foreach ($allClasses as $cl) {
    $cLevel = (int)$cl['class_level'];
    $classLabels[$cLevel] = $cl['label'];
    $examLabels[$cLevel] = $cl['exam_label'];
    $classAccent[$cLevel] = $cl['accent_color'];
}

// Which subjects apply to which has-subjects class is admin-editable
// (Notes > Class Subjects), e.g. Islamiat for 9-11 vs Pakistan Studies for
// 10-12, rather than one global subject list shared by every class.
$classSubjectRows = $db->query(
    "SELECT ncs.class_level, s.* FROM note_class_subjects ncs
     JOIN note_subjects s ON s.id = ncs.subject_id
     WHERE s.is_active = 1
     ORDER BY ncs.class_level, s.sort_order, s.name"
)->fetchAll();

$classSubjects = [];   // class_level => [subject rows]
$subjectClasses = [];  // slug => [class levels using it]
$subjectsBySlug = [];  // slug => subject row (for the union subject bar)
foreach ($classSubjectRows as $row) {
    $cls = (int)$row['class_level'];
    $classSubjects[$cls][] = $row;
    $subjectClasses[$row['slug']][] = $cls;
    if (!isset($subjectsBySlug[$row['slug']])) {
        $subjectsBySlug[$row['slug']] = $row;
    }
}

$samples = $db->query(
    "SELECT ns.*, sub.slug AS subject_slug, sub.name AS subject_name
     FROM note_samples ns
     LEFT JOIN note_subjects sub ON sub.id = ns.subject_id
     WHERE ns.status = 'published' AND (sub.id IS NULL OR sub.is_active = 1)
     ORDER BY ns.class_level, sub.sort_order, ns.sort_order, ns.id"
)->fetchAll();

$samplesByClassSubject = []; // class_level => subject_slug => [sample rows] (has-subjects classes)
$samplesByClass = [];        // class_level => [sample rows] (flat classes)
foreach ($samples as $s) {
    if ($s['subject_slug'] !== null) {
        $samplesByClassSubject[$s['class_level']][$s['subject_slug']][] = $s;
    } else {
        $samplesByClass[$s['class_level']][] = $s;
    }
}

// One shared render model for every block on the page, whether it's a real
// Class x Subject pairing or a flat class -- same card grid, same "see
// more"/Unlock Complete Notes treatment, same filter data-attributes.
$blocks = [];

foreach ($allClasses as $cl) {
    $c = (int)$cl['class_level'];
    $classLabel = $cl['label'];

    if (!empty($classSubjects[$c])) {
        foreach ($classSubjects[$c] ?? [] as $s) {
            $rows = $samplesByClassSubject[$c][$s['slug']] ?? [];
            $items = [];
            foreach ($rows as $sample) {
                $items[] = [
                    'title' => $sample['title'],
                    'chapter_label' => $sample['chapter_label'],
                    'type_label' => $sample['content_type'] === 'prose' ? 'Prose' : ($sample['content_type'] === 'poetry' ? 'Poetry' : 'Notes'),
                    'is_poetry' => $sample['content_type'] === 'poetry',
                    'description' => $sample['description'],
                    'file_path' => $sample['file_path'],
                    'preview_label' => $s['name'] . ' ' . $classLabel . ($sample['chapter_label'] ? ', ' . $sample['chapter_label'] : '') . ', ' . $sample['title'],
                    'meta_label' => $classLabel . ' · ' . $s['name'],
                    'accent' => $cl['accent_color'],
                ];
            }
            $blocks[] = [
                'cls' => (string)$c,
                'subj' => $s['slug'],
                'heading' => $classLabel . ' · ' . $s['name'],
                'badge' => $cl['exam_label'] ? ('FBISE · ' . $cl['exam_label']) : null,
                'intro' => $rows
                    ? ('Free sample ' . mb_strtolower($s['name']) . ' notes for ' . $classLabel . ', exactly as taught in live class. The complete syllabus is included free for enrolled students.')
                    : ('No free samples posted yet for ' . $classLabel . ' ' . $s['name'] . ' -- the full notes still exist and are included free once you enroll.'),
                'items' => $items,
                'reqSubject' => $classLabel . ' ' . $s['name'],
                'ctaLabel' => 'Enroll & Get Complete Notes',
                'ctaLink' => 'courses.php',
                'iconKey' => $cl['icon_key'],
            ];
        }
    } else {
        $rows = $samplesByClass[$c] ?? [];
        $items = [];
        foreach ($rows as $sample) {
            $items[] = [
                'title' => $sample['title'],
                'chapter_label' => $sample['chapter_label'],
                'type_label' => $sample['content_type'] === 'prose' ? 'Prose' : ($sample['content_type'] === 'poetry' ? 'Poetry' : 'Notes'),
                'is_poetry' => $sample['content_type'] === 'poetry',
                'description' => $sample['description'],
                'file_path' => $sample['file_path'],
                'preview_label' => $classLabel . ($sample['chapter_label'] ? ', ' . $sample['chapter_label'] : '') . ', ' . $sample['title'],
                'meta_label' => $classLabel,
                'accent' => $cl['accent_color'],
            ];
        }
        $blocks[] = [
            'cls' => (string)$c,
            'subj' => 'all',
            'heading' => $classLabel,
            'badge' => null,
            'intro' => $rows ? (string)$cl['description'] : 'No free samples posted yet in this class -- the full set still exists and is included free once you enroll.',
            'items' => $items,
            'reqSubject' => $classLabel,
            'ctaLabel' => $cl['cta_label'],
            'ctaLink' => $cl['cta_link'],
            'iconKey' => $cl['icon_key'],
        ];
    }
}

// Default view is one concrete, populated class+subject pair (first class
// + English, falling back to the first subject assigned to it) rather than
// "All" on both filters -- most class/subject combos won't have samples
// uploaded yet, so an All+All default would open on a wall of near-empty
// sections.
$defaultClass = $classes ? (string)$classes[0] : null;
$defaultSubjectSlug = null;
foreach ($classSubjects[$classes[0] ?? 0] ?? [] as $s) {
    if ($s['slug'] === 'english') {
        $defaultSubjectSlug = 'english';
        break;
    }
}
if ($defaultSubjectSlug === null && !empty($classSubjects[$classes[0] ?? 0])) {
    $defaultSubjectSlug = $classSubjects[$classes[0]][0]['slug'];
}

// Cards beyond this count start hidden behind a "See more" toggle so the
// Unlock Complete Notes card stays visible without scrolling past a wall
// of samples first -- a subject with 18 uploaded PDFs shouldn't bury it.
$visibleLimit = 6;
?>

<div class="phero phero-navy">
<?php renderPageHero('notes'); ?>
<div class="phero phero-dark">
  <div class="wrap reveal">
    <div class="kick">Free Resources</div>
    <h1>Notes that <span class="hl">open doors.</span></h1>
    <p class="sub">Free sample notes for FBISE English, Urdu and more, Classes 9 to 12. No login required, pick a class and subject below. Full-length notes, model papers and MCQ banks are included free for enrolled students.</p>
  </div>
</div>

<?php if ($blocks): ?>

<div class="nfbar"><div class="nfin">
  <span class="nfl">Class</span>
  <button type="button" class="nft" data-cls="all">All</button>
  <?php foreach ($allClasses as $cl): $clsKey = (string)(int)$cl['class_level']; ?>
    <button type="button" class="nft<?= $clsKey === $defaultClass ? ' on' : '' ?>" data-cls="<?= e($clsKey) ?>"><?= e($cl['label']) ?></button>
  <?php endforeach; ?>
</div></div>
<div class="nfbar-2"><div class="nfin">
  <span class="nfl">Subject</span>
  <button type="button" class="nft-sub" data-subj="all">All</button>
  <?php foreach ($subjectsBySlug as $slug => $s): ?>
    <button type="button" class="nft-sub<?= $slug === $defaultSubjectSlug ? ' on' : '' ?>" data-subj="<?= e($slug) ?>" data-classes="<?= e(implode(',', $subjectClasses[$slug])) ?>"><?= e($s['name']) ?></button>
  <?php endforeach; ?>
</div></div>

<section style="padding-top:0">
  <div class="wrap">
    <?php foreach ($blocks as $bi => $block): ?>
      <?php $total = count($block['items']); $hasMore = $total > $visibleLimit; ?>
      <div class="clblock reveal" data-cls="<?= e($block['cls']) ?>" data-subj="<?= e($block['subj']) ?>">
        <div class="clblock-head">
          <h2><?= e($block['heading']) ?></h2>
          <?php if ($block['badge']): ?><span class="clbadge"><?= e($block['badge']) ?></span><?php endif; ?>
        </div>
        <?php if ($block['intro']): ?><p class="clblock-sub"><?= e($block['intro']) ?></p><?php endif; ?>
        <div class="nrule"></div>
        <div class="ngrid" id="ngrid-<?= $bi ?>">
          <?php foreach ($block['items'] as $i => $item): ?>
            <article class="ncard3<?= $i >= $visibleLimit ? ' ncard3-extra' : '' ?>" style="--acc:<?= e($item['accent']) ?>">
              <span class="n3free">Free</span>
              <div class="n3top">
                <?php if ($item['chapter_label']): ?><span class="n3ch"><?= e($item['chapter_label']) ?></span><?php endif; ?>
                <span class="n3type<?= $item['is_poetry'] ? ' poetry' : '' ?>"><?= e($item['type_label']) ?></span>
              </div>
              <h3><?= e($item['title']) ?></h3>
              <?php if ($item['description']): ?><p><?= e($item['description']) ?></p><?php endif; ?>
              <div class="n3meta"><span>PDF</span><span><?= e($item['meta_label']) ?></span></div>
              <button type="button" class="b3 b3-pv" data-pdf="<?= e($item['file_path']) ?>" data-title="<?= e($item['preview_label']) ?>" aria-label="Preview <?= e($item['preview_label']) ?>">
                <?= icon('eye') ?> Preview
              </button>
            </article>
          <?php endforeach; ?>
          <?php if ($hasMore): ?>
            <button type="button" class="nmore-toggle" data-target="ngrid-<?= $bi ?>" data-more-label="See all <?= $total ?> free samples" data-less-label="Show fewer samples" aria-expanded="false">
              <span class="nmore-label">See all <?= $total ?> free samples</span> <?= icon('chevron-down', 'nmore-ic') ?>
            </button>
          <?php endif; ?>
          <article class="reqcard">
            <div class="reqic"><?= icon($block['iconKey'] ?: 'document') ?></div>
            <h3>Unlock Complete Notes</h3>
            <p>The notes above are free sample notes to help you experience the quality of our study material.</p>
            <p>Complete chapter-wise notes, grammar capsules, MCQ Banks, Revision Sheets, Worksheets, and much more for <?= e($block['reqSubject']) ?>, along with MCQ Banks, Model Papers and other premium learning resources, are provided free of cost exclusively to students enrolled in any EnglishKeys Academy course, including our Bootcamps, Marathons, Crash Courses, FLPs and other regular programs.</p>
            <p>Simply enroll in any eligible course to receive these resources as part of your course package at no additional cost.</p>
            <a class="b3 b3-rq" href="<?= e($block['ctaLink']) ?>"><?= e($block['ctaLabel']) ?></a>
          </article>
        </div>
      </div>
    <?php endforeach; ?>
    <p class="nempty" id="nempty" style="display:none">No notes match that filter yet.</p>
  </div>
</section>

<?php else: ?>
<section>
  <div class="wrap">
    <p>Subjects are being set up, please check back soon. In the meantime, message us on WhatsApp for notes.</p>
    <p><a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Message on WhatsApp</a></p>
  </div>
</section>
<?php endif; ?>

<div class="pdfmodal" id="pdfModal" aria-hidden="true">
  <div class="pdfmodal-backdrop" data-close></div>
  <div class="pdfmodal-panel" role="dialog" aria-modal="true" aria-label="Note preview">
    <div class="pdfmodal-bar">
      <button type="button" class="pdfmodal-close" data-close aria-label="Close preview">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
      <span class="pdfmodal-title" id="pdfModalTitle">Preview</span>
      <div class="pdfmodal-controls">
        <button type="button" class="pdfmodal-menu-btn" aria-haspopup="true" aria-expanded="false" aria-label="More options">
          <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
        </button>
        <div class="pdfmodal-menu-list" hidden>
          <a href="#" target="_blank" rel="noopener" class="pdfmodal-newtab">Open in new tab</a>
        </div>
      </div>
    </div>
    <iframe id="pdfModalFrame" class="pdfmodal-frame" title="Note PDF preview" src=""></iframe>
  </div>
</div>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
<script src="assets/js/notes.js" defer></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
