<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';

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
