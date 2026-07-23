<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/hero.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/**
 * A subtle staggered-reveal delay for the Nth item (0-indexed) in a card
 * grid, read by the .reveal IntersectionObserver in assets/js/site.js.
 * Capped so long grids don't end up with a sluggish tail.
 */
function revealDelay(int $index, float $step = 0.06, float $max = 0.3): string
{
    $delay = min($index * $step, $max);
    return $delay > 0 ? ' data-delay="' . $delay . '"' : '';
}

/**
 * Appends -2, -3, etc. to $baseSlug until it no longer collides with an
 * existing blog_posts row (other than $excludeId, so re-saving a post under
 * its own unchanged title doesn't shift its slug).
 */
function uniqueBlogSlug(string $baseSlug, int $excludeId = 0): string
{
    $db = getDb();
    $slug = $baseSlug;
    $suffix = 2;
    while (true) {
        $stmt = $db->prepare('SELECT id FROM blog_posts WHERE slug = ? AND id != ?');
        $stmt->execute([$slug, $excludeId]);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $suffix;
        $suffix++;
    }
}

/**
 * Appends -2, -3, etc. to $baseSlug until it no longer collides with an
 * existing note_subjects row (other than $excludeId), mirroring
 * uniqueBlogSlug() for the notes-library subject nav.
 */
function uniqueSubjectSlug(string $baseSlug, int $excludeId = 0): string
{
    $db = getDb();
    $slug = $baseSlug;
    $suffix = 2;
    while (true) {
        $stmt = $db->prepare('SELECT id FROM note_subjects WHERE slug = ? AND id != ?');
        $stmt->execute([$slug, $excludeId]);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $suffix;
        $suffix++;
    }
}

function getSetting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $stmt = getDb()->query('SELECT setting_key, setting_value FROM site_settings');
        foreach ($stmt->fetchAll() as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

/**
 * Builds a wa.me link with a pre-filled message, so "message us on
 * WhatsApp" buttons open with useful context instead of a blank chat.
 */
function waLink(string $number, ?string $message = null): string
{
    $message = $message ?? 'Assalam-o-alaikum! I have a question about EnglishKeys Academy.';
    return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
}

function getContentBlock(string $pageSlug, string $blockKey): array
{
    $stmt = getDb()->prepare('SELECT content, image_path FROM content_blocks WHERE page_slug = ? AND block_key = ?');
    $stmt->execute([$pageSlug, $blockKey]);
    $row = $stmt->fetch();
    return $row ?: ['content' => '', 'image_path' => null];
}

/**
 * Validates and moves an uploaded image into assets/uploads/{subdir}.
 * Returns the relative path to store in the DB, or null if no file was
 * uploaded. Throws RuntimeException on validation failure.
 */
function handleImageUpload(string $fieldName, string $subdir): ?string
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > UPLOAD_MAX_BYTES) {
        throw new RuntimeException('Image is too large. Max size is ' . (UPLOAD_MAX_BYTES / 1024 / 1024) . 'MB.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED_EXT, true)) {
        throw new RuntimeException('Unsupported file type. Allowed: ' . implode(', ', UPLOAD_ALLOWED_EXT));
    }

    $mime = mime_content_type($file['tmp_name']);
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowedMimes, true)) {
        throw new RuntimeException('Uploaded file is not a valid image.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destDir = __DIR__ . '/../assets/uploads/' . $subdir;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $destPath = $destDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new RuntimeException('Failed to save uploaded file.');
    }

    return 'assets/uploads/' . $subdir . '/' . $filename;
}

/**
 * Deletes a previously uploaded image from disk given its DB-stored
 * relative path (e.g. "assets/uploads/gallery/xxx.jpg"). Safe no-op if
 * the path is empty or the file no longer exists.
 */
function deleteUploadedImage(?string $path): void
{
    if (!$path) {
        return;
    }
    $fullPath = __DIR__ . '/../' . $path;
    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}

/**
 * Validates and moves an uploaded PDF into assets/uploads/{subdir}. Same
 * shape as handleImageUpload() but for note sample files (PDF-only, larger
 * size cap). Returns the relative path to store in the DB, or null if no
 * file was uploaded. Throws RuntimeException on validation failure.
 */
function handlePdfUpload(string $fieldName, string $subdir): ?string
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > UPLOAD_MAX_PDF_BYTES) {
        throw new RuntimeException('PDF is too large. Max size is ' . (UPLOAD_MAX_PDF_BYTES / 1024 / 1024) . 'MB.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED_PDF_EXT, true)) {
        throw new RuntimeException('Unsupported file type. Only PDF files are allowed.');
    }

    $mime = mime_content_type($file['tmp_name']);
    if ($mime !== 'application/pdf') {
        throw new RuntimeException('Uploaded file is not a valid PDF.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destDir = __DIR__ . '/../assets/uploads/' . $subdir;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $destPath = $destDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new RuntimeException('Failed to save uploaded file.');
    }

    return 'assets/uploads/' . $subdir . '/' . $filename;
}

/**
 * Whitelist-sanitizes rich blog content HTML (typed or pasted into the
 * TinyMCE editor) with HTMLPurifier. Only structural/semantic tags are kept;
 * inline styles and classes are stripped so every post renders consistently
 * through the site's own .article-body CSS rather than carrying over
 * whatever fonts/colors the source of a paste happened to apply.
 *
 * `dir` (on block tags) and `class="ex"` (on `p`) are the two exceptions:
 * the site's Urdu posts rely on dir="rtl" for right-to-left rendering, and
 * "worked example" callouts rely on class="ex" — both used throughout the
 * seeded posts in sql/schema.sql, so stripping them would silently break
 * that formatting the next time such a post is edited and re-saved.
 */
function sanitizeBlogHtml(?string $html): string
{
    if ($html === null || trim($html) === '') {
        return '';
    }

    $cacheDir = sys_get_temp_dir() . '/htmlpurifier-cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0775, true);
    }

    $config = HTMLPurifier_Config::createDefault();
    $config->set('Cache.SerializerPath', $cacheDir);
    // div[class]/table[class] are needed for the .atable-wrap/.atable pair
    // (assets/css/style.css) that makes a pasted/authored table scroll
    // horizontally on narrow screens instead of breaking the page layout --
    // without them here, that wrapper was silently stripped on every save,
    // so a responsive table looked fine in the editor but reverted to a
    // plain unscrollable one the moment it was published.
    $config->set('HTML.Allowed', 'p[dir|class],br,strong,b,em,i,u,h2[dir],h3[dir],h4[dir],blockquote,ul[dir],ol[dir],li,a[href|target],img[src|alt|width|height],div[class],table[class],thead,tbody,tr,th,td,hr');
    $config->set('Attr.AllowedClasses', ['ex', 'atable-wrap', 'atable']);
    $config->set('HTML.TargetBlank', true);
    $config->set('AutoFormat.RemoveEmpty', true);

    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}

/**
 * Estimated reading time in minutes (~200 words/min). Splits on whitespace
 * rather than str_word_count(), which only recognises Latin-script letters
 * and would undercount Urdu/RTL posts to a handful of "words".
 */
function blogReadingMinutes(?string $htmlContent): int
{
    $text  = trim(strip_tags((string)$htmlContent));
    $words = $text === '' ? [] : preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    return max(1, (int)ceil(count($words) / 200));
}

/**
 * Sends a plain-text notification email. On Hostinger this uses PHP's
 * built-in mail() via the domain's mail server. Locally (XAMPP without an
 * SMTP relay configured) this will typically return false silently -
 * the DB write is the source of truth either way.
 */
function notifyAdmin(string $subject, string $body): bool
{
    $headers = 'From: ' . MAIL_FROM . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
    return @mail(MAIL_TO, $subject, $body, $headers);
}

function redirectWithMessage(string $location, string $message, string $type = 'success'): void
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header('Location: ' . $location);
    exit;
}

function getFlashMessage(): ?array
{
    if (empty($_SESSION['flash_message'])) {
        return null;
    }
    $flash = ['message' => $_SESSION['flash_message'], 'type' => $_SESSION['flash_type'] ?? 'success'];
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    return $flash;
}

/**
 * The About page's Founder and Co-Founder highlights, resolved via the
 * dedicated site_settings pointers ('about_founder_teacher_id' /
 * 'about_cofounder_teacher_id', editable in Admin -> Our Team) so the
 * page never depends on fragile Role Title text matching or Sort Order.
 * Falls back to Role Title containing "CEO" / "Co-Founder" if a pointer
 * isn't set or points to a missing/inactive row. Distinct from
 * site_settings('founders_vision_teacher_id'), which independently picks
 * whose quote shows in the Home page's "Founders' Vision" section.
 * Returns ['founder' => ?array, 'cofounder' => ?array].
 */
function getFounderAndCofounder(PDO $db): array
{
    $fetchById = function (int $id) use ($db): ?array {
        if ($id <= 0) return null;
        $stmt = $db->prepare('SELECT * FROM teachers WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    };

    $founder = $fetchById((int) getSetting('about_founder_teacher_id'));
    if (!$founder) {
        $founder = $db->query("SELECT * FROM teachers WHERE is_active = 1 AND role_title LIKE '%CEO%' ORDER BY sort_order, id LIMIT 1")->fetch() ?: null;
    }

    $cofounder = $fetchById((int) getSetting('about_cofounder_teacher_id'));
    if (!$cofounder) {
        $cofounder = $db->query("SELECT * FROM teachers WHERE is_active = 1 AND role_title LIKE '%Co-Founder%' ORDER BY sort_order, id LIMIT 1")->fetch() ?: null;
    }
    if ($founder && $cofounder && $founder['id'] === $cofounder['id']) {
        $cofounder = null;
    }

    return ['founder' => $founder, 'cofounder' => $cofounder];
}

/**
 * The "Proven Track Record" achiever cards, single source of truth for
 * Home ("Proven Track Record"), Testimonials ("Alumnus Corner"), Alumni
 * (top band) and About ("The Hat-Trick"). Pair with renderTrackRecordCard()
 * below. No $limit (the default) fetches every active record, so the grid
 * grows automatically as records are added, wrapping into new rows via
 * CSS grid, no template change ever needed.
 */
function getTrackRecords(?int $limit = null): array
{
    $sql = 'SELECT * FROM track_records WHERE is_active = 1 ORDER BY sort_order, id';
    if ($limit !== null) {
        $sql .= ' LIMIT ' . (int) $limit;
    }
    return getDb()->query($sql)->fetchAll();
}

/**
 * Renders one track-record achiever card (the .tcard markup). $class and
 * $delayAttr are passed in per call site so each page keeps its existing
 * reveal-animation behaviour unchanged.
 */
function renderTrackRecordCard(array $r, string $class = 'tcard', string $delayAttr = ''): string
{
    if (empty($r['image'])) {
        return '<div class="' . e($class) . '"' . $delayAttr . '>'
            . '<span class="tpos">' . e($r['position_badge']) . '</span>'
            . '<div class="tyr">' . e($r['year']) . '</div>'
            . '<b>' . e($r['student_name']) . '</b>'
            . '<span>' . e($r['achievement_title']) . '</span>'
            . '</div>';
    }

    return '<div class="' . e($class) . ' tcard--has-image"' . $delayAttr . '>'
        . '<img class="tcard-photo" src="' . e($r['image']) . '" alt="' . e($r['student_name']) . '" loading="lazy">'
        . '<div class="tcard-info">'
        . '<span class="tpos">' . e($r['position_badge']) . '</span>'
        . '<div class="tyr">' . e($r['year']) . '</div>'
        . '<b>' . e($r['student_name']) . '</b>'
        . '<span>' . e($r['achievement_title']) . '</span>'
        . '</div>'
        . '</div>';
}

/**
 * The stat cards shown in the dark band below the Home hero (e.g. "210K+ /
 * Learners in our community"). Single source of truth: Admin -> Homepage
 * Stats. Pair with renderHomeStatsBand() below so any page that needs this
 * exact band (Home, About, ...) shares one query and one markup/CSS path -
 * add/edit/delete/hide/reorder in the admin panel updates every page.
 */
function getHomeStats(): array
{
    return getDb()->query('SELECT * FROM home_stats WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();
}

/**
 * Echoes the full stats band (including its .band/.wrap wrapper), or
 * nothing at all if there are no active stats. Call as <?php renderHomeStatsBand(); ?>
 */
function renderHomeStatsBand(): void
{
    $stats = getHomeStats();
    if (!$stats) {
        return;
    }
    echo '<div class="band"><div class="wrap bg-auto reveal">';
    foreach ($stats as $stat) {
        echo '<div class="bs"><b>' . e($stat['value']) . '</b><span>' . e($stat['label']) . '</span></div>';
    }
    echo '</div></div>';
}

/**
 * Live values the offline chat widget's small hardcoded safety-net answers
 * (assets/js/chatbot.js) reference via {{token}}, shipped to the browser
 * as window.EKA_INFO (see includes/footer.php). This only needs to cover
 * what that fixed, minimal KB actually asks for — the real answering
 * (buildChatFacts(), below) reads the DB directly and needs no token map.
 */
function buildChatTokens(): array
{
    $tokens = [
        'waLink' => waLink(getSetting('whatsapp_number')),
        'whatsapp' => getSetting('phone'),
        'phone2' => getSetting('phone_2'),
        'email' => getSetting('email'),
        'bankLine' => getSetting('bank_name') . ' — Title: ' . getSetting('bank_title') . ', IBAN ' . getSetting('bank_iban'),
        'easypaisaLine' => getSetting('easypaisa_number') ? (getSetting('easypaisa_name') . ', ' . getSetting('easypaisa_number')) : '',
    ];

    $summerCourse = getDb()->query("SELECT price, schedule_info FROM courses WHERE slug = 'summer-intensive-2026' LIMIT 1")->fetch();
    $tokens['summerPrice'] = $summerCourse['price'] ?? '';
    $sched = [];
    foreach (explode('|', (string)($summerCourse['schedule_info'] ?? '')) as $part) {
        [$k, $v] = array_pad(explode(':', $part, 2), 2, '');
        $sched[trim($k)] = trim($v);
    }
    $scheduleStr = implode(', ', array_filter([$sched['Schedule'] ?? '', $sched['Time'] ?? '']));
    $range = (!empty($sched['Starts']) && !empty($sched['Ends'])) ? ($sched['Starts'] . ' to ' . $sched['Ends']) : '';
    $tokens['summerDates'] = trim($scheduleStr . ($range ? ' (' . $range . ')' : ''));

    return $tokens;
}

/**
 * Assembles the AI chat assistant's knowledge block from live DB data
 * (site_settings, teachers, courses, alumni) instead of a hardcoded string,
 * so editing content in the admin panel (fees, contact info, teachers,
 * results) keeps the chatbot's answers in sync without a code change.
 */
function buildChatFacts(): string
{
    $db = getDb();

    $about = "ABOUT\n"
        . '- Online academy coaching FBISE (Federal Board) students. Tagline: "' . getSetting('tagline') . '."' . "\n"
        . '- Teaching since ' . getSetting('stat_since') . '; co-founded in its current form on ' . getSetting('founded_date') . ".\n"
        . '- 100% online, live classes on Zoom, ' . getSetting('address') . ".\n"
        . '- Community of ' . getSetting('stat_learners') . "+ learners.\n"
        . '- ' . getSetting('stat_youtube_subs') . " subscribers on YouTube.\n"
        . '- Rated ' . getSetting('google_rating') . ' stars from ' . getSetting('google_review_count') . " Google reviews.";

    $teachers = $db->query("SELECT name, role_title, credentials FROM teachers WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
    $founderLines = [];
    foreach ($teachers as $t) {
        $creds = str_replace("\n", ', ', (string)$t['credentials']);
        $founderLines[] = '- ' . $t['name'] . ' — ' . $t['role_title'] . '. ' . $creds . '.';
    }
    $founders = "FOUNDERS (husband and wife)\n" . implode("\n", $founderLines);

    $subjects = $db->query("SELECT title FROM courses WHERE category = 'subject' AND is_active = 1 ORDER BY sort_order")->fetchAll();
    $programmes = $db->query("SELECT title, price, duration, eligibility, mode, schedule_info FROM courses WHERE category IN ('programme', 'featured') AND is_active = 1 ORDER BY sort_order")->fetchAll();
    $progLines = [];
    foreach ($programmes as $p) {
        $bits = array_filter([$p['duration'], $p['eligibility'], $p['mode'], $p['price'] ? 'Fee: ' . $p['price'] : null]);
        $schedule = $p['schedule_info'] ? ' (' . str_replace('|', ', ', $p['schedule_info']) . ')' : '';
        $progLines[] = '- ' . $p['title'] . ': ' . implode(', ', $bits) . $schedule;
    }
    $subjectsCourses = "SUBJECTS & COURSES (Classes 9–12, FBISE)\n"
        . '- Four core subjects: ' . implode(', ', array_column($subjects, 'title')) . ".\n"
        . "- Programmes:\n" . implode("\n", $progLines);

    $alumni = $db->query("SELECT name, achievement, batch_info FROM alumni WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
    $resultLines = [];
    foreach ($alumni as $a) {
        $resultLines[] = '- ' . $a['batch_info'] . ': ' . $a['name'] . ' — ' . $a['achievement'] . '.';
    }
    $results = "RESULTS (verifiable Federal Board results)\n" . implode("\n", $resultLines);

    $notes = "NOTES\n- Free notes for every visitor on the Notes page (no login). Premium notes and model papers unlock with an active subscription; some notes are secured, view-only PDFs.";

    $feesLines = ['- Bank: ' . getSetting('bank_name') . ', Title "' . getSetting('bank_title') . '", IBAN ' . getSetting('bank_iban') . '.'];
    if (getSetting('easypaisa_number')) {
        $feesLines[] = '- EasyPaisa: ' . getSetting('easypaisa_name') . ', ' . getSetting('easypaisa_number') . '.';
    }
    if (getSetting('jazzcash_number')) {
        $feesLines[] = '- JazzCash: ' . getSetting('jazzcash_name') . ', ' . getSetting('jazzcash_number') . '.';
    }
    $fees = "FEES & PAYMENT\n" . implode("\n", $feesLines) . "\n- Ask on WhatsApp for fees of any programme not listed above.";

    $whatsapp = getSetting('whatsapp_number');
    $contact = "CONTACT\n"
        . '- WhatsApp (fastest, reply within 3 hours): +' . $whatsapp . ' → https://wa.me/' . $whatsapp . "\n"
        . '- Phone: ' . getSetting('phone') . ', ' . getSetting('phone_2') . '. Email: ' . getSetting('email') . ".\n"
        . "- Pages: /courses /notes /blog /testimonials /alumni /about /contact\n"
        . '- Enroll via the form at /contact.php or on WhatsApp.';

    return implode("\n\n", [$about, $founders, $subjectsCourses, $results, $notes, $fees, $contact]);
}

/**
 * Renders one testimonial card. $cardStyle comes from the testimonial's
 * category (testimonial_categories.card_style) and picks the markup
 * variant: 'standard' (stars only), 'marks' (orange marks badge from
 * $t['course']), 'tag' (navy subject/course badge from $t['course']),
 * 'parent' (left-border pull-quote card, no badge/stars).
 */
function renderTestimonialCard(array $t, string $cardStyle = 'standard', int $index = 0): string
{
    $delayAttr = revealDelay($index);

    if ($cardStyle === 'parent') {
        return '<div class="pquote reveal"' . $delayAttr . '>'
            . '<p>&ldquo;' . e($t['quote']) . '&rdquo;</p>'
            . '<b>' . e($t['name']) . '</b><br><span>' . e($t['source_label']) . '</span>'
            . '</div>';
    }

    $badge = '';
    if ($cardStyle === 'marks' && !empty($t['course'])) {
        $badge = '<span class="otag">' . e($t['course']) . '</span>';
    } elseif ($cardStyle === 'tag' && !empty($t['course'])) {
        $badge = '<span class="ntag">' . e($t['course']) . '</span>';
    }

    $stars = $cardStyle === 'standard' ? '<div class="stars">' . starRow((int)($t['rating'] ?: 5)) . '</div>' : '';

    return '<div class="rcard reveal"' . $delayAttr . '>'
        . $stars . $badge
        . '<p>&ldquo;' . e($t['quote']) . '&rdquo;</p>'
        . '<div><b>' . e($t['name']) . '</b><br><span>' . e($t['source_label']) . '</span></div>'
        . '</div>';
}

// ---------------------------------------------------------------------------
// Stock avatars: when no real photo is available for a person (track-record
// achiever, alumni story, team member), the admin can pick a male/female
// avatar instead of uploading a file. The chosen avatar is stored in the
// same photo/image column as a normal upload, so every renderer just works.
// ---------------------------------------------------------------------------

const STOCK_AVATARS = [
    'male'   => 'assets/uploads/avatars/avatar-male.png',
    'female' => 'assets/uploads/avatars/avatar-female.png',
];

/**
 * Resolves the photo for a save action from the admin's "Photo" choice:
 * 'upload' (default) validates + stores the uploaded file, 'male'/'female'
 * return the matching stock-avatar path, 'none' clears the photo.
 * Returns the path to store, or null meaning "keep whatever is there".
 */
function resolvePhotoChoice(string $choiceField, string $uploadField, string $subdir): ?string
{
    $choice = $_POST[$choiceField] ?? 'upload';
    if (isset(STOCK_AVATARS[$choice])) {
        return STOCK_AVATARS[$choice];
    }
    if ($choice === 'none') {
        return '';
    }
    return handleImageUpload($uploadField, $subdir);
}

/**
 * The shared admin form control for the photo-or-avatar pattern. Renders a
 * radio group (keep/upload, male avatar, female avatar, none) plus the file
 * input and a preview of the current photo.
 */
function renderPhotoChoiceField(string $choiceField, string $uploadField, ?string $currentPath, string $label = 'Photo'): string
{
    $current = '';
    if ($currentPath) {
        $current = '<div class="photo-current"><img src="../' . e($currentPath) . '" alt="Current photo"></div>';
    }
    $isMale = $currentPath === STOCK_AVATARS['male'];
    $isFemale = $currentPath === STOCK_AVATARS['female'];
    return '<fieldset class="photo-choice"><legend>' . e($label) . '</legend>'
        . $current
        . '<label class="checkbox-label"><input type="radio" name="' . e($choiceField) . '" value="upload" checked> '
        . ($currentPath && !$isMale && !$isFemale ? 'Keep current photo / upload a new one below' : 'Upload a photo (below)') . '</label>'
        . '<label class="checkbox-label"><input type="radio" name="' . e($choiceField) . '" value="male"' . ($isMale ? ' checked' : '') . '> '
        . 'Use male avatar <img class="avatar-option" src="../' . e(STOCK_AVATARS['male']) . '" alt="Male avatar"></label>'
        . '<label class="checkbox-label"><input type="radio" name="' . e($choiceField) . '" value="female"' . ($isFemale ? ' checked' : '') . '> '
        . 'Use female avatar <img class="avatar-option" src="../' . e(STOCK_AVATARS['female']) . '" alt="Female avatar"></label>'
        . '<label class="checkbox-label"><input type="radio" name="' . e($choiceField) . '" value="none"> No photo</label>'
        . '<input type="file" name="' . e($uploadField) . '" accept=".jpg,.jpeg,.png,.webp">'
        . '</fieldset>';
}

