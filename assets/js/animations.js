/* ═══════════════════════════════════════════════════════════
   NeuroScanBalance – High-End Animations · EDEL-EDITION
   Parallax · Reveals · Spotlight · Count-up · Scroll-Hint
   Additiv: main.js bleibt unberührt.
   ═══════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ── 1) Scroll-Progress (Goldfaden) ── */
  var bar = document.createElement('div');
  bar.id = 'scroll-progress';
  document.body.appendChild(bar);

  /* ── 2) Scroll-Hint im Hero ── */
  var hero = document.querySelector('.hero');
  var hint = null;
  if (hero && !reduced) {
    hint = document.createElement('div');
    hint.className = 'scroll-hint';
    hint.setAttribute('aria-hidden', 'true');
    hero.appendChild(hint);
  }

  /* ── 3) Auto-Tagging: Reveal-Klassen verteilen ── */
  function tag(sel, cls, stagger) {
    var els = document.querySelectorAll(sel);
    for (var i = 0; i < els.length; i++) {
      els[i].classList.add('rv');
      if (cls) els[i].classList.add(cls);
      if (stagger) els[i].style.setProperty('--rv-delay', (i % 6) * 0.13 + 's');
    }
  }

  tag('h2.section-title');
  tag('.section-label:not(.hero .section-label)');
  tag('.about-img', 'rv-left');
  tag('.about-text', 'rv-right');
  tag('.fw-group', 'rv-scale', true);
  tag('.step', null, true);
  tag('.stat-item', null, true);
  tag('.reports', 'rv-scale');
  tag('.quote-band-text', 'rv-blur');
  tag('.intensiv-text', 'rv-left');
  tag('.intensiv-dates', 'rv-right');
  tag('.faq-grid details', null, true);
  tag('.cal-wrap', 'rv-scale');
  tag('.maps-info', 'rv-left');
  tag('.maps-frame', 'rv-right');
  tag('.maps-anfahrt li', null, true);
  tag('.cta-banner-inner');
  tag('.footer-grid');

  /* ── 4) Count-up: Zahlen in der Stats-Bar zählen hoch ── */
  function setupCountUps() {
    var nodes = document.querySelectorAll('.stat-text strong');
    nodes.forEach(function (node) {
      var m = node.textContent.match(/\d+/);
      if (!m) return;
      var target = parseInt(m[0], 10);
      var pre = node.textContent.slice(0, m.index);
      var post = node.textContent.slice(m.index + m[0].length);
      node.innerHTML = '';
      node.appendChild(document.createTextNode(pre));
      var span = document.createElement('span');
      span.className = 'count-up';
      span.textContent = reduced ? String(target) : '0';
      node.appendChild(span);
      node.appendChild(document.createTextNode(post));
      if (reduced) return;
      node._count = { span: span, target: target, done: false };
    });
  }
  setupCountUps();

  function runCountUp(node) {
    var c = node._count;
    if (!c || c.done) return;
    c.done = true;
    var start = null, dur = 1400;
    function step(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      // ease-out-expo
      var e = p === 1 ? 1 : 1 - Math.pow(2, -10 * p);
      c.span.textContent = String(Math.round(e * c.target));
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ── 5) IntersectionObserver: Reveals + Count-up triggern ── */
  if (!reduced && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('rv-in');
          var strong = e.target.querySelector && e.target.querySelector('.stat-text strong');
          if (strong && strong._count) runCountUp(strong);
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    document.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
  } else {
    document.querySelectorAll('.rv').forEach(function (el) { el.classList.add('rv-in'); });
  }

  /* ── 6) Cursor-Spotlight auf Karten (nur Desktop) ── */
  if (!reduced && window.matchMedia('(hover:hover)').matches) {
    var spots = document.querySelectorAll('.fw-group,.reports,.maps-info,.intensiv-dates');
    spots.forEach(function (el) {
      el.addEventListener('mousemove', function (ev) {
        var r = el.getBoundingClientRect();
        el.style.setProperty('--mx', (ev.clientX - r.left) + 'px');
        el.style.setProperty('--my', (ev.clientY - r.top) + 'px');
      }, { passive: true });
    });
  }

  /* ── 7) Parallax + Progress + Nav + Hint (ein rAF-Loop) ── */
  var heroSlider = document.querySelector('.hero-slider');
  var blobR = document.querySelector('.forwhom-blob-r');
  var blobL = document.querySelector('.forwhom-blob-l');
  var nav = document.querySelector('nav');
  var ticking = false;

  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(function () {
      var y = window.scrollY || 0;
      var doc = document.documentElement;
      var max = doc.scrollHeight - doc.clientHeight;

      bar.style.width = (max > 0 ? (y / max) * 100 : 0) + '%';
      if (nav) nav.classList.toggle('nav-scrolled', y > 40);
      if (hint) hint.classList.toggle('is-hidden', y > 80);

      if (!reduced) {
        if (heroSlider && hero && y < hero.offsetHeight + 200) {
          heroSlider.style.transform = 'translateY(' + y * 0.35 + 'px)';
        }
        if (blobR) {
          var rr = blobR.getBoundingClientRect();
          blobR.style.transform = 'translateY(' + (-(window.innerHeight - rr.top) * 0.06) + 'px)';
        }
        if (blobL) {
          var rl = blobL.getBoundingClientRect();
          blobL.style.transform = 'translateY(' + ((window.innerHeight - rl.top) * 0.09) + 'px)';
        }
      }
      ticking = false;
    });
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();
