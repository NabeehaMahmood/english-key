(function () {
  var burger = document.getElementById('burger');
  var mm = document.getElementById('mm');
  if (burger && mm) {
    burger.addEventListener('click', function () {
      mm.classList.toggle('open');
    });
    mm.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') mm.classList.remove('open');
    });
  }

  // Elements can opt into a staggered fade via data-delay="0.12" (seconds) -
  // same attribute the approved HTML references use. Applied as a CSS
  // transition-delay right before the reveal fires, so unstaggered .reveal
  // elements (the vast majority) are completely unaffected.
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var delay = entry.target.getAttribute('data-delay');
          if (delay) entry.target.style.transitionDelay = delay + 's';
          entry.target.classList.add('in');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealEls.forEach(function (el) { observer.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in'); });
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

/* ------------------------------------------------------------------
   Courses page: programme category accordion (.cata-hdr) and per-card
   "View details" toggle (.pmore). Click-driven (not hover-only) so it
   works on touch. Both use real <button> + aria-expanded/aria-controls;
   these blocks find nothing on other pages and quietly do nothing.
   ------------------------------------------------------------------ */
(function () {
  var headers = document.querySelectorAll('.cata-hdr');
  if (!headers.length) return;

  function setOpen(btn, panel, container, open) {
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    container.classList.toggle('open', open);
    panel.style.maxHeight = open ? (panel.scrollHeight + 'px') : '0px';
  }

  for (var i = 0; i < headers.length; i++) {
    (function (btn) {
      var panel = document.getElementById(btn.getAttribute('aria-controls'));
      var cata = btn.closest('.cata');
      if (!panel || !cata) return;
      btn.addEventListener('click', function () {
        setOpen(btn, panel, cata, btn.getAttribute('aria-expanded') !== 'true');
      });
    })(headers[i]);
  }

  window.addEventListener('resize', function () {
    var open = document.querySelectorAll('.cata.open .cata-bdy');
    for (var i = 0; i < open.length; i++) {
      open[i].style.maxHeight = open[i].scrollHeight + 'px';
    }
  });
})();

(function () {
  var toggles = document.querySelectorAll('.pmore');
  if (!toggles.length) return;

  function setOpen(btn, panel, card, open) {
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    var label = btn.querySelector('.pmore-label');
    if (label) label.textContent = open ? 'Hide details' : 'View details';
    card.classList.toggle('open', open);
    panel.style.maxHeight = open ? (panel.scrollHeight + 'px') : '0px';
  }

  for (var i = 0; i < toggles.length; i++) {
    (function (btn) {
      var panel = document.getElementById(btn.getAttribute('aria-controls'));
      var card = btn.closest('.pcard');
      if (!panel || !card) return;
      btn.addEventListener('click', function () {
        setOpen(btn, panel, card, btn.getAttribute('aria-expanded') !== 'true');
      });
    })(toggles[i]);
  }

  window.addEventListener('resize', function () {
    var open = document.querySelectorAll('.pcard.open .pdetail');
    for (var i = 0; i < open.length; i++) {
      open[i].style.maxHeight = open[i].scrollHeight + 'px';
    }
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