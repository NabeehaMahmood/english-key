// Tab system for admin section navigation.
// Markup contract:
//   <button class="admin-tab" data-tab-group="G" data-tab-target="id">Label</button>
//   <div class="admin-tabpanel" id="id" data-tab-group="G" data-tab-id="id" [hidden]>...</div>
// Groups can nest (a panel of group "main" may contain its own tab bar for
// group "why-cards"); activation walks up the DOM so a deep-link like
// #why-cards-all reveals every ancestor panel, not just the innermost one.
document.addEventListener('DOMContentLoaded', function () {
  function activateTab(group, target, updateHash) {
    document.querySelectorAll('[data-tab-group="' + group + '"][data-tab-target]').forEach(function (btn) {
      var isActive = btn.getAttribute('data-tab-target') === target;
      btn.classList.toggle('active', isActive);
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
    document.querySelectorAll('[data-tab-group="' + group + '"][data-tab-id]').forEach(function (panel) {
      panel.hidden = panel.getAttribute('data-tab-id') !== target;
    });
    if (updateHash !== false && history.replaceState) {
      history.replaceState(null, '', '#' + target);
    }
  }

  function activateForHash(hash, scroll) {
    var id = (hash || '').replace('#', '');
    if (!id) return false;
    var el = document.getElementById(id);
    if (!el) return false;

    var found = false;
    var current = el;
    while (current) {
      if (current.dataset && current.dataset.tabGroup && current.dataset.tabId) {
        activateTab(current.dataset.tabGroup, current.dataset.tabId, false);
        found = true;
      }
      current = current.parentElement;
    }
    if (found && scroll) {
      el.scrollIntoView({ block: 'start' });
    }
    return found;
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-tab-target]');
    if (!btn) return;
    activateTab(btn.getAttribute('data-tab-group'), btn.getAttribute('data-tab-target'));
  });

  window.addEventListener('hashchange', function () {
    activateForHash(location.hash, true);
  });

  activateForHash(location.hash, true);
});
