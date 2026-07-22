/* ---------------------------------------------------------------
   Shared expand/collapse helper (accurate scrollHeight based, no
   jump) - used by featured cards, the programme accordion, and the
   payment method selector. Elements that don't exist on a given
   page simply never call this, so it's a safe no-op site-wide.
   --------------------------------------------------------------- */
function slideToggle(panel, open) {
  if (!panel) return;
  if (open) {
    panel.classList.add('open');
    panel.style.maxHeight = panel.scrollHeight + 'px';
  } else {
    panel.style.maxHeight = panel.scrollHeight + 'px';
    requestAnimationFrame(function () {
      panel.style.maxHeight = '0px';
      panel.classList.remove('open');
    });
  }
}

/* ---------------------------------------------------------------
   Animated count-up numbers (e.g. review rating, review count).
   Only touches elements with class="count-num" data-target="X" -
   finds nothing and does nothing on pages that don't use it.
   --------------------------------------------------------------- */
function animateCount(el) {
  if (el.dataset.done) return;
  el.dataset.done = '1';
  var target = parseFloat(el.dataset.target);
  var decimals = el.dataset.decimals ? parseInt(el.dataset.decimals, 10) : 0;
  var suffix = el.dataset.suffix || '';
  var duration = 1300;
  var start = null;
  function step(ts) {
    if (!start) start = ts;
    var p = Math.min((ts - start) / duration, 1);
    var eased = 1 - Math.pow(1 - p, 3);
    var val = target * eased;
    el.textContent = (decimals ? val.toFixed(decimals) : Math.round(val)) + suffix;
    if (p < 1) requestAnimationFrame(step);
    else el.textContent = (decimals ? target.toFixed(decimals) : target) + suffix;
  }
  requestAnimationFrame(step);
}

(function () {
  var burger = document.getElementById('burger');
  var mm = document.getElementById('mm');
  if (burger && mm) {
    burger.addEventListener('click', function () {
      var open = mm.classList.toggle('open');
      burger.classList.toggle('open', open);
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    mm.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') { mm.classList.remove('open'); burger.classList.remove('open'); burger.setAttribute('aria-expanded', 'false'); }
    });
  }

  // Animated count-up for elements like the Courses page rating badge
  // (<span class="count-num" data-target="4.8" data-decimals="1">). Runs
  // once per element, the moment its .reveal ancestor becomes visible.
  function animateCount(el) {
    if (el.dataset.done) return;
    el.dataset.done = '1';
    var target = parseFloat(el.dataset.target);
    if (isNaN(target)) return;
    var decimals = el.dataset.decimals ? parseInt(el.dataset.decimals, 10) : 0;
    var duration = 1200;
    var start = null;
    function step(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      var val = target * eased;
      el.textContent = decimals ? val.toFixed(decimals) : Math.round(val);
      if (p < 1) requestAnimationFrame(step);
      else el.textContent = decimals ? target.toFixed(decimals) : target;
    }
    requestAnimationFrame(step);
  }

  // Elements can opt into a staggered fade via data-delay="0.12" (seconds) -
  // same attribute the approved HTML references use. Applied as a CSS
  // transition-delay right before the reveal fires, so unstaggered .reveal
  // elements (the vast majority) are completely unaffected.
  var revealEls = document.querySelectorAll('.reveal');
  function activateReveal(el) {
    // "in" drives the site's original fade+translateY reveal (still used by
    // pages not yet on the client_courses_final.html system); "visible" is
    // the class that design's own reveal CSS looks for. Adding both lets
    // old and new components share one observer without conflicting.
    el.classList.add('in', 'visible');
    if (el.dataset.delay) el.style.transitionDelay = el.dataset.delay + 's';
    if (el.dataset.anim && !el.classList.contains(el.dataset.anim)) el.classList.add(el.dataset.anim);
    el.querySelectorAll('.count-num').forEach(animateCount);
  }
  if ('IntersectionObserver' in window && revealEls.length) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var delay = entry.target.getAttribute('data-delay');
          if (delay) entry.target.style.transitionDelay = delay + 's';
          entry.target.classList.add('in');
          entry.target.querySelectorAll('.count-num').forEach(animateCount);
          activateReveal(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealEls.forEach(function (el) { observer.observe(el); });
  } else {
    revealEls.forEach(activateReveal);
  }
})();

/* ------------------------------------------------------------------
   Featured-course popup (Task A4).
   The markup only exists on the homepage and only when an active
   featured course is in the database, so on every other page these
   blocks find nothing and quietly do nothing.
   ------------------------------------------------------------------ */
(function () {
  var pop = document.getElementById('fcPop');
  if (!pop) return;

  function alreadyShown() {
    try { return sessionStorage.getItem('ek_featured_popup') === '1'; }
    catch (e) { return false; }
  }
  function markShown() {
    try { sessionStorage.setItem('ek_featured_popup', '1'); } catch (e) {}
  }
  function openPopup() {
    pop.hidden = false;
    pop.setAttribute('aria-hidden', 'false');
    requestAnimationFrame(function () { pop.classList.add('show'); });
  }
  function closePopup() {
    pop.classList.remove('show');
    pop.setAttribute('aria-hidden', 'true');
    setTimeout(function () { pop.hidden = true; }, 350);
  }

  // Show once per browser session, about a second after load.
  if (!alreadyShown()) {
    setTimeout(function () { openPopup(); markShown(); }, 1000);
  }

  // Close: X button, dark overlay, Escape key.
  var closers = pop.querySelectorAll('[data-fc-close]');
  for (var i = 0; i < closers.length; i++) {
    closers[i].addEventListener('click', closePopup);
  }
  document.addEventListener('keydown', function (e) {
    if ((e.key === 'Escape' || e.keyCode === 27) && !pop.hidden) closePopup();
  });
})();

/* ---------- Hero: mouse parallax on floating shapes and badges ---------- */
(function () {
  var hero = document.querySelector('.hero');
  if (!hero || !hero.querySelector('.floaties')) return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var items = hero.querySelectorAll('.fl, .hb');
  hero.addEventListener('mousemove', function (e) {
    var r = hero.getBoundingClientRect();
    var x = (e.clientX - r.left) / r.width - 0.5;
    var y = (e.clientY - r.top) / r.height - 0.5;
    for (var i = 0; i < items.length; i++) {
      var d = parseFloat(items[i].getAttribute('data-depth')) || (8 + i * 4);
      items[i].style.transform = 'translate(' + (x * d) + 'px,' + (y * d) + 'px)';
    }
  });
  hero.addEventListener('mouseleave', function () {
    for (var i = 0; i < items.length; i++) items[i].style.transform = '';
  });
})();

/* ---------- Cards: subtle 3D tilt on hover ---------- */
(function () {
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var cards = document.querySelectorAll('.scard, .rcard, .g3 .card');
  for (var i = 0; i < cards.length; i++) {
    (function (c) {
      c.addEventListener('mousemove', function (e) {
        var r = c.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width - 0.5;
        var y = (e.clientY - r.top) / r.height - 0.5;
        c.style.transform = 'perspective(700px) rotateX(' + (-y * 4) + 'deg) rotateY(' + (x * 4) + 'deg) translateY(-4px)';
      });
      c.addEventListener('mouseleave', function () { c.style.transform = ''; });
    })(cards[i]);
  }
})();

/* ------------------------------------------------------------------
   Featured courses (client_courses_final.html): hovering a card
   reveals its details instantly; only one card's details stay open
   at a time. Click still works for touch/keyboard users. Finds
   nothing and does nothing on any other page.
   ------------------------------------------------------------------ */
(function () {
  var cards = Array.prototype.slice.call(document.querySelectorAll('.fcard'));
  var entries = cards.map(function (card) {
    var btn = card.querySelector('.fdetails-toggle');
    var panel = btn ? document.getElementById(btn.getAttribute('data-target')) : null;
    return { card: card, btn: btn, panel: panel, closeTimer: null };
  }).filter(function (entry) { return entry.btn && entry.panel; });

  function openEntry(entry) {
    entries.forEach(function (other) {
      if (other === entry) return;
      clearTimeout(other.closeTimer);
      other.btn.setAttribute('aria-expanded', 'false');
      slideToggle(other.panel, false);
    });
    entry.btn.setAttribute('aria-expanded', 'true');
    slideToggle(entry.panel, true);
  }
  function closeEntry(entry) {
    entry.btn.setAttribute('aria-expanded', 'false');
    slideToggle(entry.panel, false);
  }

  entries.forEach(function (entry) {
    entry.card.addEventListener('mouseenter', function () {
      clearTimeout(entry.closeTimer);
      openEntry(entry);
    });
    entry.card.addEventListener('mouseleave', function () {
      entry.closeTimer = setTimeout(function () { closeEntry(entry); }, 180);
    });
    entry.btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = entry.btn.getAttribute('aria-expanded') === 'true';
      if (isOpen) closeEntry(entry); else openEntry(entry);
    });
  });

  // keep an open panel the right height if the text reflows
  window.addEventListener('resize', function () {
    var open = document.querySelectorAll('.fdetails.open');
    for (var i = 0; i < open.length; i++) {
      open[i].style.maxHeight = open[i].scrollHeight + 'px';
    }
  });
})();

/* ------------------------------------------------------------------
   Programme groups (client_courses_final.html): hover to auto-expand,
   click kept as a fallback for touch/keyboard. First group opens on
   load. Finds nothing and does nothing on any other page.
   ------------------------------------------------------------------ */
(function () {
  var groups = document.querySelectorAll('.pgroup');
  groups.forEach(function (group, i) {
    var btn = group.querySelector('.pghead');
    var panel = document.getElementById(btn.getAttribute('data-target'));
    var closeTimer = null;

    function open() {
      clearTimeout(closeTimer);
      if (btn.getAttribute('aria-expanded') === 'true') return;
      btn.setAttribute('aria-expanded', 'true');
      slideToggle(panel, true);
    }
    function close() {
      if (btn.getAttribute('aria-expanded') !== 'true') return;
      btn.setAttribute('aria-expanded', 'false');
      slideToggle(panel, false);
    }

    group.addEventListener('mouseenter', function () { clearTimeout(closeTimer); open(); });
    group.addEventListener('mouseleave', function () { closeTimer = setTimeout(close, 220); });
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      if (btn.getAttribute('aria-expanded') === 'true') close(); else open();
    });

    if (i === 0) open();
  });

  window.addEventListener('resize', function () {
    document.querySelectorAll('.pgpanel.open').forEach(function (panel) {
      panel.style.maxHeight = panel.scrollHeight + 'px';
    });
  });
})();

/* ------------------------------------------------------------------
   Featured-course popup (Task A4).
   The markup only exists on the homepage and only when an active
   featured course is in the database, so on every other page these
   blocks find nothing and quietly do nothing.
   ------------------------------------------------------------------ */
(function () {
  var pop = document.getElementById('fcPop');
  if (!pop) return;

  function alreadyShown() {
    try { return sessionStorage.getItem('ek_featured_popup') === '1'; }
    catch (e) { return false; }
  }
  function markShown() {
    try { sessionStorage.setItem('ek_featured_popup', '1'); } catch (e) {}
  }
  function openPopup() {
    pop.hidden = false;
    pop.setAttribute('aria-hidden', 'false');
    requestAnimationFrame(function () { pop.classList.add('show'); });
  }
  function closePopup() {
    pop.classList.remove('show');
    pop.setAttribute('aria-hidden', 'true');
    setTimeout(function () { pop.hidden = true; }, 350);
  }

  // Show once per browser session, about a second after load.
  if (!alreadyShown()) {
    setTimeout(function () { openPopup(); markShown(); }, 1000);
  }

  // Close: X button, dark overlay, Escape key.
  var closers = pop.querySelectorAll('[data-fc-close]');
  for (var i = 0; i < closers.length; i++) {
    closers[i].addEventListener('click', closePopup);
  }
  document.addEventListener('keydown', function (e) {
    if ((e.key === 'Escape' || e.keyCode === 27) && !pop.hidden) closePopup();
  });
})();

/* ------------------------------------------------------------------
   Courses page: Featured-card "View details" (.fcard / .fdetails-toggle).
   Hovering a card reveals its details instantly; only one card's details
   stay open at a time. Click still works for touch/keyboard users. Finds
   nothing on other pages and quietly does nothing there.
   ------------------------------------------------------------------ */
(function () {
  var cards = document.querySelectorAll('.fcard');
  if (!cards.length) return;

  function setOpen(btn, panel, open) {
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    panel.classList.toggle('open', open);
    panel.style.maxHeight = open ? (panel.scrollHeight + 'px') : '0px';
  }

  var entries = [];
  cards.forEach(function (card) {
    var btn = card.querySelector('.fdetails-toggle');
    var panel = btn ? document.getElementById(btn.getAttribute('aria-controls')) : null;
    if (!btn || !panel) return;
    entries.push({ btn: btn, panel: panel, closeTimer: null });
  });

  entries.forEach(function (entry) {
    function open() {
      entries.forEach(function (other) {
        if (other === entry) return;
        clearTimeout(other.closeTimer);
        setOpen(other.btn, other.panel, false);
      });
      setOpen(entry.btn, entry.panel, true);
    }
    function close() { setOpen(entry.btn, entry.panel, false); }

    var card = entry.btn.closest('.fcard');
    card.addEventListener('mouseenter', function () { clearTimeout(entry.closeTimer); open(); });
    card.addEventListener('mouseleave', function () { entry.closeTimer = setTimeout(close, 180); });
    entry.btn.addEventListener('click', function (e) {
      e.stopPropagation();
      if (entry.btn.getAttribute('aria-expanded') === 'true') close(); else open();
    });
  });

  window.addEventListener('resize', function () {
    document.querySelectorAll('.fdetails.open').forEach(function (panel) {
      panel.style.maxHeight = panel.scrollHeight + 'px';
    });
  });
})();

/* ------------------------------------------------------------------
   Courses page: programme group accordion (.pghead / .pgpanel). Hovering
   a group expands it; click is the accessible fallback for touch/
   keyboard. The first group starts open. Finds nothing on other pages.
   ------------------------------------------------------------------ */
(function () {
  var groups = document.querySelectorAll('.pgroup');
  if (!groups.length) return;

  function setOpen(btn, panel, open) {
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    panel.style.maxHeight = open ? (panel.scrollHeight + 'px') : '0px';
  }

  groups.forEach(function (group, i) {
    var btn = group.querySelector('.pghead');
    var panel = btn ? document.getElementById(btn.getAttribute('aria-controls')) : null;
    if (!btn || !panel) return;
    var closeTimer = null;

    function open() {
      clearTimeout(closeTimer);
      if (btn.getAttribute('aria-expanded') === 'true') return;
      setOpen(btn, panel, true);
    }
    function close() {
      if (btn.getAttribute('aria-expanded') !== 'true') return;
      setOpen(btn, panel, false);
    }

    group.addEventListener('mouseenter', function () { clearTimeout(closeTimer); open(); });
    group.addEventListener('mouseleave', function () { closeTimer = setTimeout(close, 220); });
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      if (btn.getAttribute('aria-expanded') === 'true') close(); else open();
    });

    if (i === 0) open();
  });

  window.addEventListener('resize', function () {
    document.querySelectorAll('.pghead[aria-expanded="true"]').forEach(function (btn) {
      var panel = document.getElementById(btn.getAttribute('aria-controls'));
      if (panel) panel.style.maxHeight = panel.scrollHeight + 'px';
    });
  });
})();

/* ------------------------------------------------------------------
   Testimonials page: category filter tabs (.pfilter / .pf-btn / .tpanel).
   Finds nothing on other pages and quietly does nothing there.
   ------------------------------------------------------------------ */
(function () {
  var bar = document.querySelector('.pfilter');
  if (!bar) return;
  var btns = bar.querySelectorAll('.pf-btn');
  var panels = document.querySelectorAll('.tpanel');

  bar.addEventListener('click', function (e) {
    var btn = e.target.closest('.pf-btn');
    if (!btn) return;
    for (var i = 0; i < btns.length; i++) btns[i].classList.remove('is-on');
    btn.classList.add('is-on');
    var filter = btn.getAttribute('data-filter');
    for (var j = 0; j < panels.length; j++) {
      panels[j].classList.toggle('is-on', panels[j].getAttribute('data-tab') === filter);
    }
    bar.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
})();