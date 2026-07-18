<?php
require_once __DIR__ . '/includes/header.php';

$db = getDb();
$whatsapp = getSetting('whatsapp_number');

$subjects = $db->query('SELECT * FROM note_subjects WHERE is_active = 1 ORDER BY sort_order, name')->fetchAll();

$samples = $db->query(
    "SELECT ns.*, sub.slug AS subject_slug, sub.name AS subject_name, sub.accent_color
     FROM note_samples ns
     JOIN note_subjects sub ON sub.id = ns.subject_id
     WHERE ns.status = 'published' AND sub.is_active = 1
     ORDER BY ns.class_level, sub.sort_order, ns.sort_order, ns.id"
)->fetchAll();

$samplesByClassSubject = [];
foreach ($samples as $s) {
    $samplesByClassSubject[$s['class_level']][$s['subject_slug']][] = $s;
}

$classes = [9, 10, 11, 12];
$examLabels = [9 => 'SSC-I', 10 => 'SSC-II', 11 => 'HSSC-I', 12 => 'HSSC-II'];
// Card accent bar follows resources/notes.html exactly: one fixed color per
// class (not per subject) -- blue/orange/purple/navy for 9/10/11/12.
$classAccent = [9 => '#3D68B0', 10 => '#E56A19', 11 => '#5B2BA6', 12 => '#1E2A66'];

// Default view is one concrete, populated class+subject pair (Class 9 +
// English, falling back to the first active subject) rather than "All" on
// both filters -- most class/subject combos won't have samples uploaded
// yet, so an All+All default would open on a wall of near-empty sections.
$defaultClass = 9;
$defaultSubjectSlug = null;
foreach ($subjects as $s) {
    if ($s['slug'] === 'english') {
        $defaultSubjectSlug = 'english';
        break;
    }
}
if ($defaultSubjectSlug === null && $subjects) {
    $defaultSubjectSlug = $subjects[0]['slug'];
}

function waNotesLink(string $whatsapp, string $subjectName, int $classLevel, string $examLabel): string
{
    $text = "Assalam o Alaikum! I am a Class $classLevel ($examLabel) student. I need notes for $subjectName. Please guide me.";
    return 'https://wa.me/' . rawurlencode($whatsapp) . '?text=' . rawurlencode($text);
}
?>

<div class="phero phero-dark">
  <div class="wrap reveal">
    <div class="kick">Free Resources</div>
    <h1>Notes that <span class="hl">open doors.</span></h1>
    <p class="sub">Free sample notes for FBISE English, Urdu, Islamiat and Tarjuma-tul-Quran, Classes 9 to 12. No login required, pick a class and subject below. Full-length notes, model papers and MCQ banks are one WhatsApp message away.</p>
  </div>
</div>

<?php if ($subjects): ?>

<div class="nfbar"><div class="nfin">
  <span class="nfl">Class</span>
  <button type="button" class="nft" data-cls="all">All</button>
  <?php foreach ($classes as $c): ?>
    <button type="button" class="nft<?= $c === $defaultClass ? ' on' : '' ?>" data-cls="<?= $c ?>">Class <?= $c ?></button>
  <?php endforeach; ?>
</div></div>
<div class="nfbar-2"><div class="nfin">
  <span class="nfl">Subject</span>
  <button type="button" class="nft-sub" data-subj="all">All</button>
  <?php foreach ($subjects as $s): ?>
    <button type="button" class="nft-sub<?= $s['slug'] === $defaultSubjectSlug ? ' on' : '' ?>" data-subj="<?= e($s['slug']) ?>"><?= e($s['name']) ?></button>
  <?php endforeach; ?>
</div></div>

<section style="padding-top:0">
  <div class="wrap">
    <?php foreach ($classes as $c): ?>
      <?php foreach ($subjects as $s): ?>
        <?php $rows = $samplesByClassSubject[$c][$s['slug']] ?? []; ?>
        <div class="clblock reveal" data-cls="<?= $c ?>" data-subj="<?= e($s['slug']) ?>">
          <div class="clblock-head">
            <h2>Class <?= $c ?> &middot; <?= e($s['name']) ?></h2>
            <span class="clbadge">FBISE &middot; <?= e($examLabels[$c]) ?></span>
          </div>
          <p class="clblock-sub">
            <?php if ($rows): ?>
              Free sample <?= e(mb_strtolower($s['name'])) ?> notes for Class <?= $c ?>, exactly as taught in live class. The rest of the syllabus is sent on request.
            <?php else: ?>
              No free samples posted yet for Class <?= $c ?> <?= e($s['name']) ?>, request full notes directly on WhatsApp below.
            <?php endif; ?>
          </p>
          <div class="nrule"></div>
          <div class="ngrid">
            <?php foreach ($rows as $sample): ?>
              <?php
                $previewLabel = $s['name'] . ' Class ' . $c . ($sample['chapter_label'] ? ', ' . $sample['chapter_label'] : '') . ', ' . $sample['title'];
                $typeLabel = $sample['content_type'] === 'prose' ? 'Prose' : ($sample['content_type'] === 'poetry' ? 'Poetry' : 'Notes');
              ?>
              <article class="ncard3" style="--acc:<?= e($classAccent[$c]) ?>">
                <span class="n3free">Free</span>
                <div class="n3top">
                  <?php if ($sample['chapter_label']): ?><span class="n3ch"><?= e($sample['chapter_label']) ?></span><?php endif; ?>
                  <span class="n3type<?= $sample['content_type'] === 'poetry' ? ' poetry' : '' ?>"><?= e($typeLabel) ?></span>
                </div>
                <h3><?= e($sample['title']) ?></h3>
                <?php if ($sample['description']): ?><p><?= e($sample['description']) ?></p><?php endif; ?>
                <div class="n3meta"><span>PDF</span><span>Class <?= $c ?> &middot; <?= e($s['name']) ?></span></div>
                <button type="button" class="b3 b3-pv" data-pdf="<?= e($sample['file_path']) ?>" data-title="<?= e($previewLabel) ?>" aria-label="Preview <?= e($previewLabel) ?>">
                  <?= icon('eye') ?> Preview
                </button>
              </article>
            <?php endforeach; ?>
            <article class="reqcard">
              <div class="reqic"><?= icon('document') ?></div>
              <h3>Need <?= $rows ? 'the rest of' : 'full' ?> Class <?= $c ?> <?= e($s['name']) ?>?</h3>
              <p><?= $rows ? 'The sample' . (count($rows) > 1 ? 's' : '') . ' above ' . (count($rows) > 1 ? 'are' : 'is') . ' free. ' : '' ?>Full-length notes, model papers and MCQ banks for Class <?= $c ?> <?= e($s['name']) ?> are sent on request, we reply within 3 hours.</p>
              <a class="b3 b3-rq" href="<?= e(waNotesLink($whatsapp, $s['name'], $c, $examLabels[$c])) ?>" target="_blank" rel="noopener">Request Access</a>
            </article>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
    <p class="nempty" id="nempty" style="display:none">No notes match that filter yet.</p>
  </div>
</section>

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

<?php else: ?>
<section>
  <div class="wrap">
    <p>Subjects are being set up, please check back soon. In the meantime, message us on WhatsApp for notes.</p>
    <p><a class="btn btn-o" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">Message on WhatsApp</a></p>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/cta-banner.php'; ?>
<script src="assets/js/notes.js" defer></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
