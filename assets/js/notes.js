(function () {
  var primaryTabs = document.querySelectorAll('.nft[data-cls]');
  var subjectTabs = document.querySelectorAll('.nft-sub[data-subj]');
  var blocks = document.querySelectorAll('.clblock[data-cls]');
  var empty = document.getElementById('nempty');

  function activeValue(tabs) {
    var current = null;
    tabs.forEach(function (t) { if (t.classList.contains('on')) current = t.dataset.cls || t.dataset.subj; });
    return current;
  }

  // A subject tab applies to a class if that class is in its comma-separated
  // data-classes list (see notes.php) -- subjects differ per class (e.g.
  // Islamiat for 9-10 vs Pakistan Studies for 11-12), so the "all" subject
  // tab is always shown, but a specific subject only shows for the classes
  // it's actually assigned to.
  function subjectAppliesToClass(tab, cls) {
    if (tab.dataset.subj === 'all' || cls === 'all') return true;
    var classes = (tab.dataset.classes || '').split(',');
    return classes.indexOf(cls) !== -1;
  }

  function filter(cls, subj) {
    var shown = 0;
    blocks.forEach(function (b) {
      var hit = (cls === 'all' || b.dataset.cls === cls) && (subj === 'all' || b.dataset.subj === subj);
      b.style.display = hit ? '' : 'none';
      if (hit) {
        shown++;
        b.querySelectorAll('.reveal:not(.in)').forEach(function (el) { el.classList.add('in'); });
      }
    });
    if (empty) empty.style.display = shown ? 'none' : 'block';

    primaryTabs.forEach(function (t) { t.classList.toggle('on', t.dataset.cls === cls); });
    subjectTabs.forEach(function (t) {
      var applies = subjectAppliesToClass(t, cls);
      t.hidden = !applies;
      t.classList.toggle('on', applies && t.dataset.subj === subj);
    });
  }

  if (primaryTabs.length && subjectTabs.length) {
    primaryTabs.forEach(function (t) {
      t.addEventListener('click', function () {
        var cls = t.dataset.cls;
        var subj = activeValue(subjectTabs) || 'all';
        // Selected subject doesn't exist for the newly chosen class (e.g.
        // switching from Class 9 to Class 11 while "Islamiat" was active) --
        // fall back to "All" rather than filtering to an impossible pair.
        var stillValid = subjectTabs.length && Array.prototype.some.call(subjectTabs, function (s) {
          return s.dataset.subj === subj && subjectAppliesToClass(s, cls);
        });
        filter(cls, stillValid ? subj : 'all');
        history.replaceState(null, '', location.pathname);
      });
    });
    subjectTabs.forEach(function (t) {
      t.addEventListener('click', function () {
        filter(activeValue(primaryTabs) || 'all', t.dataset.subj);
        history.replaceState(null, '', location.pathname);
      });
    });
    // Initial state is whatever the server marked "on" (see notes.php) --
    // a real, populated class+subject pair by default, not All/All.
    filter(activeValue(primaryTabs) || 'all', activeValue(subjectTabs) || 'all');
  }

  // ---- "See more" sample toggle (keeps Unlock Complete Notes visible
  // without scrolling past a long list first) ----
  document.querySelectorAll('.nmore-toggle[data-target]').forEach(function (btn) {
    var grid = document.getElementById(btn.dataset.target);
    if (!grid) return;
    var extras = grid.querySelectorAll('.ncard3-extra');
    var label = btn.querySelector('.nmore-label');
    btn.addEventListener('click', function () {
      var willShow = !extras[0].classList.contains('show');
      extras.forEach(function (el) { el.classList.toggle('show', willShow); });
      btn.setAttribute('aria-expanded', willShow ? 'true' : 'false');
      if (label) label.textContent = willShow ? btn.dataset.lessLabel : btn.dataset.moreLabel;
    });
  });

  // ---- PDF preview modal (Google Classroom-style chrome) ----
  var modal = document.getElementById('pdfModal');
  if (modal) {
    var frame = document.getElementById('pdfModalFrame');
    var title = document.getElementById('pdfModalTitle');
    var newTabLink = modal.querySelector('.pdfmodal-newtab');
    var menuBtn = modal.querySelector('.pdfmodal-menu-btn');
    var menuList = modal.querySelector('.pdfmodal-menu-list');
    var lastFocused = null;

    function openModal(url, label) {
      lastFocused = document.activeElement;
      frame.src = url;
      title.textContent = label || 'Preview';
      newTabLink.href = url;
      modal.classList.add('open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      modal.classList.remove('open');
      modal.setAttribute('aria-hidden', 'true');
      frame.src = '';
      menuList.hidden = true;
      document.body.style.overflow = '';
      if (lastFocused) lastFocused.focus();
    }

    document.querySelectorAll('.b3-pv[data-pdf]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        openModal(btn.dataset.pdf, btn.dataset.title || btn.getAttribute('aria-label') || 'Preview');
      });
    });

    modal.querySelectorAll('[data-close]').forEach(function (el) {
      el.addEventListener('click', closeModal);
    });

    menuBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var willOpen = menuList.hidden;
      menuList.hidden = !willOpen;
      menuBtn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    });
    document.addEventListener('click', function (e) {
      if (!menuList.hidden && !menuList.contains(e.target) && e.target !== menuBtn) {
        menuList.hidden = true;
        menuBtn.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });
  }
})();
