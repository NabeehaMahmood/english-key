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
/* ================================================================
   MERGED FROM COURSES/ALUMNI BUILD (talha): alumni story wall,
   share-your-story panel, FAQ accordion, jump-nav scroll spy and
   the refreshable enroll/alumni captcha.
   ================================================================ */

/* ------------------------------------------------------------------
   Alumni Stories: "Read More" / "Show Less" expand toggle for the
   3-line-clamped story preview. A story only gets the button if its text
   actually overflows the 3-line clamp (scrollHeight > clientHeight) -
   short stories that already fit never show "Read More". Finds nothing
   and does nothing on any page without a .story-card.
   ------------------------------------------------------------------ */
(function () {
  document.querySelectorAll('.story-card').forEach(function (card) {
    var text = card.querySelector('.story-text');
    var btn = card.querySelector('.story-toggle');
    if (!text || !btn) return;
    if (text.scrollHeight - text.clientHeight <= 1) {
      btn.remove();
      return;
    }
    btn.addEventListener('click', function () {
      var expanded = card.classList.toggle('expanded');
      btn.textContent = expanded ? 'Show Less' : 'Read More';
    });
  });
})();

/* ------------------------------------------------------------------
   Alumni "Share Your Story": on a successful submission the server
   redirects back with a session flash message rendered by header.php as
   a plain .flash.flash-success banner (shared site-wide with
   contact/enroll). Only on the alumni page - identified by #sharePanel,
   which nothing else on the site has - that same banner is upgraded into
   a premium auto-dismissing toast, without touching the shared component
   used elsewhere. Finds nothing and does nothing on any other page.
   ------------------------------------------------------------------ */
(function () {
  var sharePanel = document.getElementById('sharePanel');
  var flash = document.querySelector('.flash.flash-success');
  if (!sharePanel || !flash) return;

  var message = flash.textContent;
  flash.innerHTML = '';
  var text = document.createElement('span');
  text.textContent = message;
  flash.appendChild(text);
  flash.classList.add('alumni-toast');

  setTimeout(function () {
    flash.classList.add('alumni-toast-hide');
    setTimeout(function () { flash.remove(); }, 400);
  }, 4500);
})();

/* ------------------------------------------------------------------
   Alumni "Share Your Story": the form stays collapsed behind a CTA
   button until clicked, then slides open (reusing slideToggle) and
   scrolls into view. Server can pre-render the panel open (class
   "open" + aria-expanded="true") after a failed submission so
   validation errors are visible without an extra click. Finds
   nothing and does nothing on any page without #shareToggle.
   ------------------------------------------------------------------ */
(function () {
  var btn = document.getElementById('shareToggle');
  var panel = document.getElementById('sharePanel');
  if (!btn || !panel) return;

  function setOpen(open) {
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    slideToggle(panel, open);
    if (open) {
      setTimeout(function () {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 80);
    }
  }

  btn.addEventListener('click', function () {
    setOpen(btn.getAttribute('aria-expanded') !== 'true');
  });

  if (btn.getAttribute('aria-expanded') === 'true') {
    panel.classList.add('open');
    panel.style.maxHeight = panel.scrollHeight + 'px';
  }

  window.addEventListener('resize', function () {
    if (panel.classList.contains('open')) panel.style.maxHeight = panel.scrollHeight + 'px';
  });
})();

/* ------------------------------------------------------------------
   Alumni story form: live character counter for the story textarea.
   Finds nothing and does nothing on any page without #alStory.
   ------------------------------------------------------------------ */
(function () {
  var story = document.getElementById('alStory');
  var counter = document.getElementById('alCounterNum');
  if (!story || !counter) return;
  function update() { counter.textContent = story.value.length; }
  story.addEventListener('input', update);
  update();
})();

/* ------------------------------------------------------------------
   Courses page FAQ accordion: one panel open at a time, reuses the
   shared slideToggle() helper defined above. Finds nothing and does
   nothing on any page without .faq-list.
   ------------------------------------------------------------------ */
(function () {
  var buttons = document.querySelectorAll('.faq-q');
  if (!buttons.length) return;

  buttons.forEach(function (btn) {
    var panel = document.getElementById(btn.getAttribute('aria-controls'));

    btn.addEventListener('click', function () {
      var isOpen = btn.getAttribute('aria-expanded') === 'true';

      buttons.forEach(function (other) {
        if (other === btn || other.getAttribute('aria-expanded') !== 'true') return;
        other.setAttribute('aria-expanded', 'false');
        slideToggle(document.getElementById(other.getAttribute('aria-controls')), false);
      });

      btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      slideToggle(panel, !isOpen);
    });
  });

  window.addEventListener('resize', function () {
    document.querySelectorAll('.faq-a.open').forEach(function (panel) {
      panel.style.maxHeight = panel.scrollHeight + 'px';
    });
  });
})();

/* ------------------------------------------------------------------
   "On this page" jump nav: scroll-spy. Highlights whichever section
   is currently under the sticky header + sticky jumpnav as the user
   scrolls, using IntersectionObserver rather than a scroll listener.
   Clicking a link still does a normal anchor jump (CSS scroll-behavior:
   smooth + scroll-padding-top already handle the sticky offset there).
   Finds nothing and does nothing on any page without a .jumpnav.
   ------------------------------------------------------------------ */
(function () {
  var nav = document.querySelector('.jumpnav');
  if (!nav || !('IntersectionObserver' in window)) return;

  var links = Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]'));
  var sections = links
    .map(function (link) { return document.getElementById(link.getAttribute('href').slice(1)); })
    .filter(Boolean);
  if (!sections.length) return;

  var current = null;
  var visible = Object.create(null);

  function setActive(id) {
    if (id === current) return;
    current = id;
    links.forEach(function (link) {
      var isActive = link.getAttribute('href') === '#' + id;
      link.classList.toggle('active', isActive);
      if (isActive) link.setAttribute('aria-current', 'page');
      else link.removeAttribute('aria-current');
    });
    // Tablet/mobile: the bar scrolls horizontally instead of wrapping, so
    // keep the active pill in view. Scroll the nav's own scrollLeft directly
    // (not scrollIntoView) - the nav has no vertical overflow of its own, so
    // scrollIntoView's "nearest" would resolve against the window and fight
    // the page's own smooth-scroll to the section.
    if (nav.scrollWidth > nav.clientWidth) {
      var activeLink = nav.querySelector('a.active');
      if (activeLink) {
        var target = activeLink.offsetLeft - (nav.clientWidth - activeLink.offsetWidth) / 2;
        nav.scrollTo({ left: Math.max(0, target), behavior: 'smooth' });
      }
    }
  }

  function pickActive() {
    // Two adjacent sections can briefly both sit inside the thin "active
    // band" as one scrolls out and the next scrolls in - prefer the later
    // one in document order so the nav switches the moment the new
    // section arrives, instead of lagging on the one that's leaving.
    for (var i = sections.length - 1; i >= 0; i--) {
      if (visible[sections[i].id]) { setActive(sections[i].id); return; }
    }
  }

  var observer = null;
  function build() {
    if (observer) observer.disconnect();
    visible = Object.create(null);
    var header = document.querySelector('header.site');
    var offset = (header ? header.offsetHeight : 0) + nav.offsetHeight;
    observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        visible[entry.target.id] = entry.isIntersecting;
      });
      pickActive();
    }, { rootMargin: '-' + (offset + 1) + 'px 0px -65% 0px', threshold: 0 });
    sections.forEach(function (s) { observer.observe(s); });
  }

  build();
  setActive(sections[0].id);

  // Reaching the bottom of the page: the last section may be short
  // enough that its band never lands under the sticky bars, so force
  // the last link active once the user can't scroll any further.
  window.addEventListener('scroll', function () {
    var atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2;
    if (atBottom) setActive(sections[sections.length - 1].id);
  }, { passive: true });

  var resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(build, 200);
  });
})();

/* ---------- Alumni story form: refresh the arithmetic captcha without a
   page reload, fetching a fresh question from captcha-refresh.php ---------- */
(function () {
  var btn = document.getElementById('captchaRefresh');
  var question = document.getElementById('captchaQuestion');
  var input = document.getElementById('captcha_answer');
  if (!btn || !question || !input) return;

  btn.addEventListener('click', function () {
    if (btn.classList.contains('is-loading')) return;
    btn.classList.add('is-loading');

    fetch('captcha-refresh.php', { credentials: 'same-origin' })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        question.textContent = data.question + ' = ?';
        input.value = '';
        input.focus();
        btn.classList.add('is-spinning');
        setTimeout(function () { btn.classList.remove('is-spinning'); }, 350);
      })
      .finally(function () {
        btn.classList.remove('is-loading');
      });
  });
})();

