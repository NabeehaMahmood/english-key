<?php
/* ============ EnglishKeys Academy — AI Assistant backend ============
   Small proxy that lets the website chatbot answer real questions about
   EnglishKeys Academy using an AI model, WITHOUT exposing your API key
   in the browser. The key lives only here, on the server.

   ---- PICK A FREE PROVIDER ----
   Set EKA_PROVIDER below and get a FREE key:

   'gemini'  (recommended)  Google Gemini — free tier, no credit card.
             Key: https://aistudio.google.com/app/apikey
   'groq'                    Groq (Llama models) — free, very fast.
             Key: https://console.groq.com/keys
   'openai'                  OpenAI-compatible (also works with OpenRouter,
             Together, etc.) — set EKA_BASE_URL + EKA_MODEL to match.
   'anthropic'               Anthropic Claude (paid).

   ---- ADD YOUR KEY (never paste it in chat or commit it) ----
   Best: hPanel -> Advanced -> add an environment variable EKA_AI_KEY.
   Or:   paste it into EKA_AI_KEY_FALLBACK below (then keep this file out
         of git so it never reaches GitHub).

   If no key is set, or the free tier is rate-limited/unreachable, the
   widget quietly falls back to its built-in FAQ answers — the site
   never breaks. The knowledge the AI draws on (fees, contact info,
   teachers, results...) comes live from the same database the admin
   panel edits — see buildChatFacts() in includes/functions.php.
=================================================================== */

require_once __DIR__ . '/includes/functions.php';

const EKA_PROVIDER = 'gemini';                  // 'gemini' | 'groq' | 'openai' | 'anthropic'
const EKA_AI_KEY_FALLBACK = '';                 // paste key here, or use the EKA_AI_KEY env var

/* Model per provider. Free-tier friendly defaults; override if you like. */
const EKA_MODELS = [
  'gemini'    => 'gemini-2.0-flash',
  'groq'      => 'llama-3.3-70b-versatile',
  'openai'    => 'gpt-4o-mini',
  'anthropic' => 'claude-haiku-4-5-20251001',
];
/* For 'openai'-style providers only: the API base URL.
   OpenAI: https://api.openai.com/v1  ·  OpenRouter: https://openrouter.ai/api/v1
   Together: https://api.together.xyz/v1 */
const EKA_BASE_URL = 'https://api.openai.com/v1';

const EKA_MAX_TURNS = 10;                        // how much conversation history to keep

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

/* mbstring polyfill for hosts without the extension */
if (!function_exists('mb_strlen')) { function mb_strlen($s){ return strlen($s); } }
if (!function_exists('mb_substr')) { function mb_substr($s,$a,$b=null){ return $b===null?substr($s,$a):substr($s,$a,$b); } }

/* --- read key from environment first, then the constant --- */
$KEY = getenv('EKA_AI_KEY') ?: EKA_AI_KEY_FALLBACK;

function out($arr){ echo json_encode($arr); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); out(['ok'=>false,'error'=>'POST only']); }
if ($KEY === '') { out(['ok'=>false,'fallback'=>true]); }   // tell the widget to use offline FAQ

/* --- light per-IP rate limit: 20 messages / 5 minutes --- */
$rk = sys_get_temp_dir().'/eka_ai_'.substr(sha1(($_SERVER['REMOTE_ADDR'] ?? '').'eka-ai'),0,16);
$hits = @json_decode(@file_get_contents($rk), true) ?: [];
$now = time();
$hits = array_values(array_filter($hits, function($t) use ($now){ return $now - $t < 300; }));
if (count($hits) >= 20) { out(['ok'=>false,'error'=>'Too many messages, please slow down a little.']); }
$hits[] = $now; @file_put_contents($rk, json_encode($hits), LOCK_EX);

/* --- read the conversation from the browser --- */
$in = json_decode(file_get_contents('php://input'), true);
$history = (isset($in['messages']) && is_array($in['messages'])) ? $in['messages'] : [];

/* keep only the last N, sanitise, cap length */
$msgs = [];
foreach (array_slice($history, -EKA_MAX_TURNS) as $m) {
  $role = ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
  $text = trim((string)($m['content'] ?? ''));
  if ($text === '') continue;
  if (mb_strlen($text) > 1500) $text = mb_substr($text, 0, 1500);
  $msgs[] = ['role'=>$role, 'content'=>$text];
}
if (!$msgs) { out(['ok'=>false,'error'=>'Say something and I\'ll help.']); }
/* the API requires the first message to be from the user */
while ($msgs && $msgs[0]['role'] !== 'user') array_shift($msgs);
if (!$msgs) { out(['ok'=>false,'error'=>'Say something and I\'ll help.']); }

/* ===================== EKA KNOWLEDGE (live from the DB) ===================== */
$FACTS = buildChatFacts();

$SYSTEM = "You are the EKA AI Assistant, the friendly assistant for EnglishKeys Academy (an online FBISE coaching academy in Pakistan).\n\n".
"Answer questions about EnglishKeys Academy — its courses, fees, timings, teachers, results, notes, enrolment, payment, and anything a prospective student or parent might reasonably ask — using the FACTS below and sensible, helpful explanation around them.\n\n".
"Rules:\n".
"- Stay on the topic of EnglishKeys Academy and studying with it. If asked something unrelated (general trivia, other companies, homework answers, etc.), politely steer back: you're here to help with EnglishKeys Academy.\n".
"- Use the FACTS as your source of truth. You may explain, summarise, and reason around them, but never invent specific facts (prices, dates, names, numbers) that aren't given. If a specific detail isn't in the FACTS, say you're not certain and point them to WhatsApp where the team replies within 3 hours.\n".
"- Be warm, concise and encouraging. A short greeting like Assalam-o-alaikum is welcome. Keep replies to a few sentences unless more detail is genuinely needed.\n".
"- When it helps, mention the relevant page (e.g. the Courses page, /enroll) or suggest WhatsApp for a quick reply.\n".
"- Never claim to be a human; you're the academy's AI assistant.\n\n".
"FACTS:\n".$FACTS;

/* ===================== call the model ===================== */
if (!function_exists('curl_init')) { out(['ok'=>false,'fallback'=>true]); }   /* host lacks cURL -> offline FAQ */

$provider = EKA_PROVIDER;
$model    = EKA_MODELS[$provider] ?? EKA_MODELS['gemini'];

/* Build the request differently per provider, then normalise the reply. */
if ($provider === 'gemini') {
  /* Gemini: system goes in system_instruction; roles are 'user'/'model' */
  $contents = [];
  foreach ($msgs as $m) {
    $contents[] = ['role' => ($m['role'] === 'assistant' ? 'model' : 'user'),
                   'parts' => [['text' => $m['content']]]];
  }
  $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.urlencode($KEY);
  $payload = [
    'system_instruction' => ['parts' => [['text' => $SYSTEM]]],
    'contents' => $contents,
    'generationConfig' => ['maxOutputTokens' => 500, 'temperature' => 0.5],
  ];
  $headers = ['Content-Type: application/json'];

} elseif ($provider === 'anthropic') {
  $url = 'https://api.anthropic.com/v1/messages';
  $payload = ['model'=>$model, 'max_tokens'=>500, 'system'=>$SYSTEM, 'messages'=>$msgs];
  $headers = ['Content-Type: application/json', 'x-api-key: '.$KEY, 'anthropic-version: 2023-06-01'];

} else {
  /* OpenAI-compatible: groq, openai, openrouter, together... system as first message */
  $base = ($provider === 'groq') ? 'https://api.groq.com/openai/v1' : EKA_BASE_URL;
  $url = rtrim($base, '/').'/chat/completions';
  $oaMsgs = array_merge([['role'=>'system','content'=>$SYSTEM]], $msgs);
  $payload = ['model'=>$model, 'max_tokens'=>500, 'temperature'=>0.5, 'messages'=>$oaMsgs];
  $headers = ['Content-Type: application/json', 'Authorization: Bearer '.$KEY];
}

$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTPHEADER => $headers,
  CURLOPT_POSTFIELDS => json_encode($payload),
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$cerr = curl_error($ch);
curl_close($ch);

if ($resp === false) { out(['ok'=>false,'error'=>'The assistant is unreachable right now. Please try WhatsApp: +'.getSetting('whatsapp_number').'.', 'detail'=>$cerr]); }

$data = json_decode($resp, true);
if ($code !== 200 || !is_array($data)) {
  out(['ok'=>false, 'fallback'=>true, 'status'=>$code]);   /* any API error -> offline FAQ */
}

/* Extract the reply text regardless of provider shape. */
$text = '';
if ($provider === 'gemini') {
  $parts = $data['candidates'][0]['content']['parts'] ?? [];
  foreach ($parts as $p) { if (isset($p['text'])) $text .= $p['text']; }
} elseif ($provider === 'anthropic') {
  foreach (($data['content'] ?? []) as $block) { if (($block['type'] ?? '') === 'text') $text .= $block['text']; }
} else {
  $text = $data['choices'][0]['message']['content'] ?? '';
}
$text = trim((string)$text);
if ($text === '') { out(['ok'=>false,'fallback'=>true]); }

out(['ok'=>true, 'reply'=>$text]);
