/* Contact page form: no server submission any more. Both buttons read
   whatever the visitor has typed and hand off to Gmail / WhatsApp
   directly, pre-filled with those details. */
(function () {
  'use strict';

  var form = document.getElementById('contact-form');
  if (!form) return;

  var GREETING = 'Assalam-o-alaikum! I have a question about EnglishKeys Academy.';

  function val(id) {
    var el = document.getElementById(id);
    return el ? el.value.trim() : '';
  }

  function buildMessage() {
    var name = val('c-name'), phone = val('c-phone'), subject = val('c-subject'), message = val('c-message');
    if (!name && !subject && !message && !phone) return GREETING;

    var lines = ['Assalam-o-alaikum, my name is ' + (name || '(not given)') + '.'];
    if (subject) lines.push('Subject: ' + subject);
    if (message) lines.push(message);
    if (phone) lines.push('My phone/WhatsApp: ' + phone);
    return lines.join('\n');
  }

  // Enter key inside a text field submits the nearest form by default;
  // there's nothing to submit to any more, so stop that.
  form.addEventListener('submit', function (e) { e.preventDefault(); });

  var gmailBtn = document.getElementById('c-send-gmail');
  if (gmailBtn) {
    gmailBtn.addEventListener('click', function () {
      var to = gmailBtn.getAttribute('data-to') || '';
      var subject = val('c-subject') || 'Website enquiry';
      var url = 'https://mail.google.com/mail/?view=cm&fs=1'
        + '&to=' + encodeURIComponent(to)
        + '&su=' + encodeURIComponent(subject)
        + '&body=' + encodeURIComponent(buildMessage());
      window.open(url, '_blank', 'noopener');
    });
  }

  var waBtn = document.getElementById('c-send-wa');
  if (waBtn) {
    waBtn.addEventListener('click', function () {
      var number = waBtn.getAttribute('data-wa') || '';
      var url = 'https://wa.me/' + number + '?text=' + encodeURIComponent(buildMessage());
      window.open(url, '_blank', 'noopener');
    });
  }
})();
