<?php
/**
 * Regenerates the Alumni story form's arithmetic captcha without a full page
 * reload. Stores the new expected answer in the session (same helper used
 * on the initial page render) and returns the question text as JSON.
 */
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

echo json_encode(['question' => enrollCaptchaQuestion()]);
