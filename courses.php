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

// The "View/Download Course Outline" buttons are wired to whatever PDF is
// currently active in the admin's Student Course Handout screen - no
// hardcoded link, no site_settings placeholder. The whole panel is skipped
// below when nothing has been uploaded/enabled yet.
$handout = getActiveHandout();

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

<div class="phero phero-navy">
  <div class="wrap reveal" data-anim="scale-in">
    <h1>Built around the FBISE syllabus, <span class="hl">nothing wasted.</span></h1>
    <p class="sub">Complete preparation for Classes 9-12 across four subjects, plus seasonal intensives, bootcamps and crash courses.</p>
  </div>
</div>

<nav class="jumpnav wrap reveal" aria-label="Section navigation">
  <span class="jumpnav-label"><?= icon('list', 'icon') ?> On this page</span>
  <?php if ($featured): ?><a href="#featured"><span>Featured Courses</span></a><?php endif; ?>
  <?php if ($subjects): ?><a href="#subjects"><span>Core Subjects</span></a><?php endif; ?>
  <?php if ($programmes): ?><a href="#programmes"><span>Programmes</span></a><?php endif; ?>
  <a href="#enrol"><span>How to Enrol</span></a>
  <a href="#faqs"><span>FAQs</span></a>
  <?php if ($testimonials): ?><a href="#reviews"><span>Reviews</span></a><?php endif; ?>
</nav>

<?php if ($featured):
  $primaryFeatured = $featured[0];
?>
<section id="featured">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Featured, Enrolling Now</div>
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
<?php endif; ?>

<?php if ($subjects): ?>
<section class="soft" id="subjects">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">Core Subjects</div>
      <h2 class="t">Four subjects, mapped to your class.</h2>
    </div>
    <div class="courses-grid reveal" style="margin-top:30px">
      <?php foreach ($subjects as $s): ?>
        <a href="enroll.php#enrol-form" class="ccard" style="--c:<?= e($s['accent_color']) ?>">
          <div class="ccard-media">
            <?php if (!empty($s['image'])): ?>
              <img src="<?= e($s['image']) ?>" alt="<?= e($s['title']) ?>" loading="lazy">
            <?php else: ?>
              <span class="ccard-media-fallback"><?= icon('book-open', 'icon') ?></span>
            <?php endif; ?>
          </div>
          <div class="ccard-body">
            <div class="ccard-num">0<?= (int)$s['sort_order'] ?>, <?= e($s['level']) ?></div>
            <h3 class="ccard-title"><?= e($s['title']) ?></h3>
            <p class="ccard-desc"><?= e($s['description']) ?></p>
            <?php if ($s['tag_line']): ?>
              <div class="tags"><?php foreach (explode(' - ', $s['tag_line']) as $tag): ?><span class="tag"><?= e(trim($tag)) ?></span><?php endforeach; ?></div>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($handout): ?>
<section class="handout-panel">
  <div class="wrap">
    <div class="handout-card reveal" data-anim="fade-up">
      <div class="handout-icon" aria-hidden="true">
        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 3h6l3 3v10.5A.5.5 0 0 1 14.5 17h-8a.5.5 0 0 1-.5-.5v-13A.5.5 0 0 1 6 3z"/><path d="M12 3v3h3"/><path d="M7.5 10h5M7.5 12.5h5"/>
        </svg>
      </div>
      <div class="handout-body">
        <div class="kick">Course Handout</div>
        <p class="handout-desc"><?= e($handout['description'] ?: 'Download or view the latest detailed course outline, syllabus and study structure prepared by EnglishKeys Academy.') ?></p>
      </div>
      <div class="handout-actions">
        <a class="btn btn-l handout-btn" href="<?= e($handout['file_path']) ?>" target="_blank" rel="noopener">
          <svg class="icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M1.5 10S4.5 4.5 10 4.5 18.5 10 18.5 10 15.5 15.5 10 15.5 1.5 10 1.5 10z"/><circle cx="10" cy="10" r="2.5"/>
          </svg>
          View Course Outline
        </a>
        <a class="btn btn-o handout-btn" href="<?= e($handout['file_path']) ?>" download="<?= e($handout['original_filename']) ?>" rel="noopener">
          <svg class="icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M10 3v9.5M6 9l4 4 4-4"/><path d="M3.5 15.5v1a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-1"/>
          </svg>
          Download Course Outline
        </a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

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
    <div class="pgroups reveal" style="margin-top:30px">
      <?php
        // All programme-group cards share the same navy resting accent and
        // transition to the client orange on hover/interaction - consistent
        // colour behaviour across every card rather than singling one out.
        $accent = 'var(--navy)';
        $gi = 0;
      foreach ($groups as $g):
        if (empty($byGroup[$g['id']])) continue;
        $items = $byGroup[$g['id']];
        $gi++;
      ?>
        <?php require __DIR__ . '/includes/programme-group.php'; ?>
      <?php endforeach; ?>

      <?php if (!empty($byGroup[0])):
        $g = ['id' => 0, 'name' => 'Other Programmes', 'description' => '', 'date_range' => '', 'icon_key' => 'folder'];
        $items = $byGroup[0];
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

<section class="soft" id="enrol">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">How to Enrol</div>
      <h2 class="t">Four simple steps to your seat.</h2>
    </div>
    <?php if ($howTo['content']): ?>
    <div class="stepper reveal" style="margin-top:36px">
      <?php $n = 0; foreach (explode("\n", $howTo['content']) as $line):
        if (trim($line) === '') continue;
        [$title, $desc] = array_pad(explode('|', $line, 2), 2, ''); $n++;
        $title = preg_replace('/^\d+\.\s*/', '', $title); ?>
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

<?php
// Static FAQ content for the Courses page (client-supplied copy - kept
// verbatim, including numbering and the "A:" answer marker).
$faqs = [
    ['q' => 'Q1. How do I register for a course at EnglishKeys Academy?', 'a' => "You can register by calling/texting our registration number (0311-1537563), through advertisement posters shared in our WhatsApp/Facebook/Instagram socials. Get the payment details, pay the prescribed fee, and share the receipt on the academy\u{2019}s number."],
    ['q' => 'Q2. Is there an entry test before joining a Bootcamp or Marathon?', 'a' => "No formal entry test is required for most programs. However, a short assessment may be conducted at the start of each course to help us understand each student's current level and group them accordingly."],
    ['q' => 'Q3. What are the class timings for each program?', 'a' => 'Class timings vary by program and batch and are shared with registered students when a course is announced. Our courses are usually conducted in the evening; please ask our front desk representative for the current schedule.'],
    ['q' => 'Q4. What is the fee for each course, and how can I pay?', 'a' => 'Fee details for each program are shared at the time of registration and may vary by class level and course duration. Fees can be paid online via bank transfer / mobile wallet — receipts are provided for all payments.'],
    ['q' => 'Q5. Are seats really limited? How can I confirm if a seat is available?', 'a' => 'Yes, most Bootcamps and special courses (Summer Camp, MDCAT Prep, Deen Camp, Full-Length Papers) have limited seats to maintain teaching quality. Please call 0311-1537563 to confirm seat availability before making payment.'],
    ['q' => 'Q6. What happens if my child misses a class?', 'a' => 'Make-up notes, and recordings (where applicable) can be arranged for students who miss a class due to genuine reasons. Please inform the academy in advance where possible.'],
    ['q' => 'Q7. Do you provide study material, notes, or past papers?', 'a' => 'Yes, class-specific notes, practice worksheets, and past papers are provided as part of the course material for Bootcamps, Marathons, and Crash Courses.'],
    ['q' => 'Q8. How are students assessed, and how are results communicated to parents?', 'a' => 'Students are assessed through weekly quizzes, class tests, and full-length papers depending on the program. Results and performance feedback are shared with parents periodically through report cards, calls, or WhatsApp updates.'],
    ['q' => 'Q9. What is the policy on fee refunds or transfers between batches?', 'a' => 'Fees once paid are generally non-refundable; however, students may be allowed to transfer to another batch of the same program (subject to seat availability) by informing the administration in advance, before the commencement of course and issuance of resource pack.'],
    ['q' => 'Q10. Are online classes available for students who cannot attend in person?', 'a' => "All our classes are online. We don\u{2019}t offer physical, on-campus classes. Please contact the academy directly to check for the available slots."],
    ['q' => 'Q11. What measures are in place for student safety and discipline?', 'a' => "EnglishKeys Academy maintains a disciplined, respectful learning environment, with attendance monitoring and direct communication with parents in case of any concerns regarding a student's conduct or wellbeing."],
];
?>
<section id="faqs">
  <div class="wrap">
    <div class="reveal">
      <div class="kick">FAQs</div>
      <h2 class="t">Frequently Asked Questions (FAQs)</h2>
    </div>
    <div class="faq-list reveal" style="margin-top:36px">
      <?php foreach ($faqs as $i => $f): $n = $i + 1; ?>
      <div class="faq-item">
        <button type="button" class="faq-q" aria-expanded="false" aria-controls="faq-panel-<?= $n ?>" id="faq-btn-<?= $n ?>">
          <span><?= e($f['q']) ?></span>
          <span class="faq-toggle" aria-hidden="true"></span>
        </button>
        <div class="faq-a" id="faq-panel-<?= $n ?>" role="region" aria-labelledby="faq-btn-<?= $n ?>">
          <div class="faq-a-inner"><p><strong>A:</strong> <?= e($f['a']) ?></p></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($testimonials):
  $ratingPct = $googleRating ? max(0, min(100, round(((float)$googleRating / 5) * 100))) : 0;
?>
<section id="reviews">
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
