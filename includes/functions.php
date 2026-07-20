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
    return '<div class="' . e($class) . '"' . $delayAttr . '>'
        . '<span class="tpos">' . e($r['position_badge']) . '</span>'
        . '<div class="tyr">' . e($r['year']) . '</div>'
        . '<b>' . e($r['student_name']) . '</b>'
        . '<span>' . e($r['achievement_title']) . '</span>'
        . '</div>';
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
