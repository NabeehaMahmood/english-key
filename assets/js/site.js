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

  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
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
