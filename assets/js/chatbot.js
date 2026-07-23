/* ============ EnglishKeys Academy — FAQ Assistant ============
   Self-contained chat widget. Tries the AI proxy at chat.php first — that's
   the one that's actually "smart": chat.php -> buildChatFacts() (in
   includes/functions.php) reads courses/teachers/alumni/site_settings
   live on every message, so it automatically knows about anything an
   admin adds through the existing Courses/Teachers/Alumni/Settings admin
   screens, with no separate knowledge base to maintain.

   The KB below is NOT that. It's a small, deliberately dumb keyword-matched
   safety net for when the AI is unreachable/rate-limited/not configured —
   a handful of generic answers (fee, timing, enrollment, payment, contact)
   plus a WhatsApp hand-off for absolutely everything else. It is fixed in
   code on purpose: a rule-based matcher can never really "know" the site's
   content the way the AI does, so there's no admin screen pretending
   otherwise here — editing it means editing this file.
================================================================ */
(function () {
  'use strict';

  /* buildChatTokens() in includes/functions.php computes these once (fee,
     schedule, contact info) and footer.php ships the result as-is; this
     file never re-derives them, so there's one place, not two, that can
     drift from the live DB. The literals below only cover the edge case
     EKA_INFO fails to render at all. */
  var TOKENS = window.EKA_INFO || {
    waLink: 'https://wa.me/923111537563',
    whatsapp: '0311-1537563',
    phone2: '0317-5403540',
    email: 'englishkeysacademy@gmail.com',
    summerPrice: 'Rs. 5,000',
    summerDates: '06–31 July, Mon–Fri, 07:00–09:00 PM PKT',
    bankLine: 'Askari Bank — Title: EnglishKeys Academy, IBAN PK95 ASCM 0001 9702 0000 2790',
    easypaisaLine: 'Muhammad Naeem, 0311-1537563'
  };

  // What this safety net actually covers, said once and reused everywhere
  // that scope needs spelling out (greeting, fallback), so widening or
  // narrowing it later is a one-line change instead of a find-and-replace.
  var TOPICS = 'fees, timings, enrollment, payment and contact';

  function substitute(text) {
    return String(text).replace(/\{\{(\w+)\}\}/g, function (m, key) {
      return Object.prototype.hasOwnProperty.call(TOKENS, key) ? TOKENS[key] : m;
    });
  }

  var KB = [
    { keys: ['salam','salaam','assalam','hello','hi','hey','good morning','good evening'],
      a: 'Wa alaikum assalam! 👋 I’m the EKA Assistant. I can help with ' + TOPICS + ' — for anything else, the team replies within 3 hours on <a href="{{waLink}}" target="_blank" rel="noopener">WhatsApp</a>.' },
    { keys: ['fee','fees','price','cost','charges','kitna','rupees','rs'],
      q: 'What is the fee?',
      a: 'The Summer Intensive 2026 (English Language) is {{summerPrice}} for the full course — 20 live sessions of 2 hours each. For fees of other programmes, message us on <a href="{{waLink}}" target="_blank" rel="noopener">WhatsApp</a> and we’ll reply within 3 hours.' },
    { keys: ['time','timing','timings','schedule','class time','when','hours'],
      q: 'What are the class timings?',
      a: 'Classes are online and run on Pakistan Standard Time. The current Summer Intensive meets {{summerDates}}. For other programme schedules, ask us on <a href="{{waLink}}" target="_blank" rel="noopener">WhatsApp</a>.' },
    { keys: ['enrol','enroll','admission','register','join','sign up','how to start'],
      q: 'How do I enroll?',
      a: 'Two easy ways: fill the <a href="/enroll">enrollment form</a> (takes 2 minutes) or message us directly on <a href="{{waLink}}" target="_blank" rel="noopener">WhatsApp ({{whatsapp}})</a>. We reply within 3 hours.' },
    { keys: ['pay','payment','bank','easypaisa','jazzcash','iban','transfer','account'],
      q: 'How can I pay?',
      a: 'We accept bank transfer ({{bankLine}}), EasyPaisa ({{easypaisaLine}}) and JazzCash. Full payment details are on the <a href="/courses">Courses page</a>.' },
    { keys: ['contact','phone','number','whatsapp','email','reach','call'],
      q: 'How do I contact you?',
      a: 'WhatsApp is fastest: <a href="{{waLink}}" target="_blank" rel="noopener">{{whatsapp}}</a> (reply within 3 hours). Phone: {{whatsapp}} or {{phone2}}. Email: {{email}}. Or use the <a href="/contact">Contact page</a>.' },
    { keys: ['thank','thanks','shukria','jazak'],
      a: 'You’re welcome! If anything else comes up, I’m right here — or reach the team on <a href="{{waLink}}" target="_blank" rel="noopener">WhatsApp</a>. Best of luck with your studies! 📚' }
  ];

  var FALLBACK = 'I can only help with a few basics right now (' + TOPICS + ') — for courses, results, teachers, notes or anything else, the team replies within 3 hours on <a href="{{waLink}}" target="_blank" rel="noopener">WhatsApp</a>.';

  /* ---------- matching: keyword scoring with light fuzziness ---------- */
  function norm(t){ return (' ' + t.toLowerCase().replace(/[^a-z0-9؀-ۿ ]+/g, ' ') + ' ').replace(/\s+/g, ' '); }
  function answer(text){
    var t = norm(text), best = null, bestScore = 0;
    for (var i = 0; i < KB.length; i++) {
      var score = 0;
      for (var k = 0; k < KB[i].keys.length; k++) {
        var key = KB[i].keys[k];
        if (t.indexOf(key.length > 3 ? key : ' ' + key + ' ') !== -1) score += key.length > 4 ? 2 : 1;
      }
      if (score > bestScore) { bestScore = score; best = KB[i]; }
    }
    return substitute(bestScore > 0 ? best.a : FALLBACK);
  }

  /* ---------- UI ---------- */
  var css = ''
  + '.ekb-btn{position:fixed;right:22px;bottom:92px;width:56px;height:56px;border-radius:16px;background:linear-gradient(140deg,#26346F,#1E2A66);display:grid;place-items:center;z-index:98;box-shadow:0 12px 30px rgba(30,42,102,.45);cursor:pointer;border:0;transition:transform .25s}'
  + '.ekb-btn:hover{transform:scale(1.07)}'
  + '.ekb-btn svg{width:26px;height:26px;fill:#fff}'
  + '.ekb-badge{position:absolute;top:-4px;right:-4px;width:14px;height:14px;border-radius:50%;background:#E56A19;border:2px solid #fff}'
  + '.ekb-panel{position:fixed;right:18px;bottom:160px;width:min(370px,calc(100vw - 36px));max-height:min(560px,calc(100vh - 190px));background:#fff;border-radius:18px;box-shadow:0 24px 70px rgba(17,24,56,.35);z-index:99;display:none;flex-direction:column;overflow:hidden;font-family:Inter,system-ui,sans-serif}'
  + '.ekb-panel.open{display:flex}'
  + '.ekb-head{background:linear-gradient(140deg,#26346F,#17204F);color:#fff;padding:16px 18px;display:flex;align-items:center;gap:12px}'
  + '.ekb-head .ekb-dot{width:38px;height:38px;border-radius:12px;background:#E56A19;display:grid;place-items:center;font-weight:800;font-family:Manrope,Inter,sans-serif;font-size:16px;flex:none}'
  + '.ekb-head b{font-family:Manrope,Inter,sans-serif;font-size:15px;display:block;line-height:1.2}'
  + '.ekb-head span{font-size:11.5px;color:#D8E0F6}'
  + '.ekb-x{margin-left:auto;background:rgba(255,255,255,.12);border:0;color:#fff;width:30px;height:30px;border-radius:9px;cursor:pointer;font-size:15px;line-height:1}'
  + '.ekb-x:hover{background:rgba(255,255,255,.25)}'
  + '.ekb-msgs{flex:1;overflow-y:auto;padding:16px;background:#F1F4FA;display:flex;flex-direction:column;gap:10px}'
  + '.ekb-m{max-width:86%;padding:10px 14px;border-radius:14px;font-size:13.8px;line-height:1.55;word-wrap:break-word}'
  + '.ekb-m.bot{background:#fff;border:1px solid #DCE3F2;color:#121938;border-bottom-left-radius:5px;align-self:flex-start}'
  + '.ekb-m.user{background:#1E2A66;color:#fff;border-bottom-right-radius:5px;align-self:flex-end}'
  + '.ekb-m a{color:#3D68B0;font-weight:700;text-decoration:none}'
  + '.ekb-m a:hover{text-decoration:underline}'
  + '.ekb-typing{display:inline-flex;gap:4px;padding:12px 16px}'
  + '.ekb-typing i{width:7px;height:7px;border-radius:50%;background:#5B2BA6;opacity:.4;animation:ekbp 1s infinite}'
  + '.ekb-typing i:nth-child(2){animation-delay:.18s}.ekb-typing i:nth-child(3){animation-delay:.36s}'
  + '@keyframes ekbp{0%,100%{opacity:.35;transform:translateY(0)}50%{opacity:1;transform:translateY(-3px)}}'
  + '.ekb-chips{display:flex;gap:7px;flex-wrap:wrap;padding:2px 0}'
  + '.ekb-chip{background:#fff;border:1px solid #C9D3EC;color:#1E2A66;font-size:12px;font-weight:700;font-family:Manrope,Inter,sans-serif;border-radius:99px;padding:7px 12px;cursor:pointer;transition:all .15s}'
  + '.ekb-chip:hover{background:#1E2A66;color:#fff;border-color:#1E2A66}'
  + '.ekb-in{display:flex;gap:8px;padding:12px;background:#fff;border-top:1px solid #DCE3F2}'
  + '.ekb-in input{flex:1;border:1px solid #C9D3EC;border-radius:11px;padding:11px 13px;font:inherit;font-size:13.8px;color:#121938;background:#FBFCFE}'
  + '.ekb-in input:focus{outline:2px solid #3D68B0;outline-offset:1px}'
  + '.ekb-in button{background:#BF4F0B;border:0;border-radius:11px;width:44px;display:grid;place-items:center;cursor:pointer;transition:background .15s}'
  + '.ekb-in button:hover{background:#E56A19}'
  + '.ekb-in svg{width:18px;height:18px;fill:#fff}'
  + '@media(max-width:480px){.ekb-panel{bottom:150px}}';

  var style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  var root = document.createElement('div');
  root.innerHTML = ''
  + '<button class="ekb-btn" aria-label="Open EKA AI Assistant" title="Questions? Ask me!">'
  + '<svg viewBox="0 0 24 24"><path d="M12 3C6.48 3 2 6.94 2 11.8c0 2.55 1.24 4.85 3.23 6.46-.1.86-.44 2.2-1.42 3.24 0 0 2.35-.16 4.19-1.36.95.27 1.96.42 3 .42 5.52 0 10-3.94 10-8.76S17.52 3 12 3zm-4.5 9.9c-.72 0-1.3-.58-1.3-1.3s.58-1.3 1.3-1.3 1.3.58 1.3 1.3-.58 1.3-1.3 1.3zm4.5 0c-.72 0-1.3-.58-1.3-1.3s.58-1.3 1.3-1.3 1.3.58 1.3 1.3-.58 1.3-1.3 1.3zm4.5 0c-.72 0-1.3-.58-1.3-1.3s.58-1.3 1.3-1.3 1.3.58 1.3 1.3-.58 1.3-1.3 1.3z"/></svg>'
  + '<span class="ekb-badge"></span></button>'
  + '<div class="ekb-panel" role="dialog" aria-label="EKA AI Assistant">'
  + '<div class="ekb-head"><div class="ekb-dot">EK</div><div><b>EKA AI Assistant</b><span>Online • ask me anything about EnglishKeys</span></div><button class="ekb-x" aria-label="Close">✕</button></div>'
  + '<div class="ekb-msgs"></div>'
  + '<form class="ekb-in"><input type="text" maxlength="200" placeholder="Ask about courses, fees, timings…" aria-label="Your question"><button type="submit" aria-label="Send"><svg viewBox="0 0 24 24"><path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2z"/></svg></button></form>'
  + '</div>';
  document.body.appendChild(root);

  var btn = root.querySelector('.ekb-btn'), panel = root.querySelector('.ekb-panel'),
      msgs = root.querySelector('.ekb-msgs'), form = root.querySelector('.ekb-in'),
      input = form.querySelector('input'), badge = root.querySelector('.ekb-badge');

  function scroll(){ msgs.scrollTop = msgs.scrollHeight; }
  function addMsg(html, who){
    var d = document.createElement('div');
    d.className = 'ekb-m ' + who;
    d.innerHTML = html;             /* bot answers come only from the KB above, or from chat.php */
    msgs.appendChild(d); scroll();
    return d;
  }
  function addChips(){
    var wrap = document.createElement('div'); wrap.className = 'ekb-chips';
    var shown = 0;
    for (var i = 0; i < KB.length && shown < 5; i++) {
      if (!KB[i].q) continue;
      (function (entry) {
        var c = document.createElement('button');
        c.type = 'button'; c.className = 'ekb-chip'; c.textContent = entry.q;
        c.onclick = function(){ ask(entry.q); };
        wrap.appendChild(c);
      })(KB[i]); shown++;
    }
    msgs.appendChild(wrap); scroll();
  }
  function typing(){
    var t = document.createElement('div');
    t.className = 'ekb-m bot ekb-typing';
    t.innerHTML = '<i></i><i></i><i></i>';
    msgs.appendChild(t); scroll();
    return t;
  }
  function esc(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
  /* turn a plain-text AI reply into safe HTML: escape, keep links the KB style,
     linkify bare URLs and internal paths, honour line breaks */
  function format(txt){
    var s = esc(txt);
    s = s.replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');
    s = s.replace(/(^|\s)(\/(?:courses|notes|blog|testimonials|alumni|about|contact|enroll))\b/g, '$1<a href="$2">$2</a>');
    s = s.replace(/\n/g, '<br>');
    return s;
  }

  var convo = [];   /* [{role, content}] sent to the AI proxy */
  var aiOn = true;   /* flips to false if the proxy says to fall back */

  function ask(q){
    var text = (q || '').trim();
    if (!text) return;
    addMsg(esc(text), 'user');
    convo.push({ role: 'user', content: text });
    input.value = '';
    var t = typing();

    /* offline / fallback path: instant keyword FAQ answer */
    function faq(){
      setTimeout(function(){
        t.remove();
        var a = answer(text);
        addMsg(a, 'bot');
        convo.push({ role: 'assistant', content: a.replace(/<[^>]+>/g, '') });
      }, 300);
    }
    if (!aiOn) { faq(); return; }

    fetch('chat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ messages: convo })
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d && d.ok && d.reply) {
        t.remove();
        addMsg(format(d.reply), 'bot');
        convo.push({ role: 'assistant', content: d.reply });
      } else if (d && d.fallback) {
        aiOn = false; faq();               /* no key / API issue -> offline FAQ */
      } else {
        t.remove();
        addMsg((d && d.error) ? esc(d.error) : FALLBACK, 'bot');
      }
    })
    .catch(function(){ aiOn = false; faq(); });   /* network error -> offline FAQ */
  }

  var greeted = false;
  function open(){
    panel.classList.add('open'); badge.style.display = 'none'; input.focus();
    if (!greeted) {
      greeted = true;
      var t = typing();
      setTimeout(function(){
        t.remove();
        addMsg('Assalam-o-alaikum! 👋 I’m the EKA AI Assistant. Ask me anything about EnglishKeys Academy — courses, fees, timings, results, notes, enrollment — or tap a question:', 'bot');
        addChips();
      }, 550);
    }
  }
  btn.addEventListener('click', function(){ panel.classList.contains('open') ? panel.classList.remove('open') : open(); });
  root.querySelector('.ekb-x').addEventListener('click', function(){ panel.classList.remove('open'); });
  form.addEventListener('submit', function(e){ e.preventDefault(); ask(input.value); });
})();
