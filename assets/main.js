// Wittwer Informatik: Projekt-Modal und Burger-Menü.
// Ausgelagert, damit die CSP ohne 'unsafe-inline' bei script-src auskommt.
'use strict';

// Scrollen sperren, solange Modal oder Menü offen ist
function syncScrollLock() {
  const offen = document.querySelector('.modal-overlay.open, .nav-links.open');
  document.body.classList.toggle('no-scroll', Boolean(offen));
}

// Projekt-Modal (existiert nur auf der Startseite)
const modalOverlay = document.getElementById('modal-overlay');
const projektDaten = document.getElementById('projekte-data');

if (modalOverlay && projektDaten) {
  const PROJEKTE = JSON.parse(projektDaten.textContent);
  const modalClose = document.getElementById('modal-close');
  let letzterFokus = null;

  const openModal = (slug) => {
    const p = PROJEKTE[slug];
    if (!p) return;

    // Bilder
    const hauptbild = document.getElementById('modal-img-main');
    hauptbild.src = p.bilder[0] || '';
    hauptbild.alt = p.name;

    // Thumbnails
    const thumbs = document.getElementById('modal-thumbs');
    thumbs.innerHTML = '';
    if (p.bilder.length > 1) {
      p.bilder.forEach((src, i) => {
        const img = document.createElement('img');
        img.src = src; img.alt = p.name + ' Bild ' + (i + 1);
        img.className = 'modal-thumb' + (i === 0 ? ' active' : '');
        img.addEventListener('click', () => {
          hauptbild.src = src;
          thumbs.querySelectorAll('.modal-thumb').forEach(t => t.classList.remove('active'));
          img.classList.add('active');
        });
        thumbs.appendChild(img);
      });
    }

    document.getElementById('modal-title').textContent = p.name;
    document.getElementById('modal-beschreibung').textContent = p.beschreibung;

    const ul = document.getElementById('modal-features');
    ul.innerHTML = '';
    p.features.forEach(f => {
      const li = document.createElement('li');
      li.textContent = f;
      ul.appendChild(li);
    });

    const stack = document.getElementById('modal-stack');
    stack.innerHTML = '';
    p.stack.forEach(s => {
      const span = document.createElement('span');
      span.textContent = s;
      stack.appendChild(span);
    });

    const link = document.getElementById('modal-link');
    link.hidden = !p.url;
    if (p.url) {
      link.href = p.url;
      link.textContent = p.url_label;
    }

    letzterFokus = document.activeElement;
    modalOverlay.classList.add('open');
    syncScrollLock();
    modalClose.focus();
  };

  const closeModal = () => {
    if (!modalOverlay.classList.contains('open')) return;
    modalOverlay.classList.remove('open');
    syncScrollLock();
    if (letzterFokus) letzterFokus.focus();
  };

  document.querySelectorAll('.proj[data-slug]').forEach(card => {
    card.addEventListener('click', () => openModal(card.dataset.slug));
    card.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openModal(card.dataset.slug);
      }
    });
  });

  modalClose.addEventListener('click', closeModal);
  modalOverlay.addEventListener('click', e => {
    if (e.target === modalOverlay) closeModal();
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });
}

// Burger-Menü (Mobile, Slide-in von rechts)
const navBurger = document.getElementById('nav-burger');
const navLinks = document.getElementById('nav-links');
const navBackdrop = document.getElementById('nav-backdrop');

function setMenu(open) {
  navBurger.classList.toggle('open', open);
  navLinks.classList.toggle('open', open);
  navBackdrop.classList.toggle('open', open);
  navBurger.setAttribute('aria-expanded', open ? 'true' : 'false');
  navBurger.setAttribute('aria-label', open ? 'Menü schliessen' : 'Menü öffnen');
  syncScrollLock();
}

navBurger.addEventListener('click', () => setMenu(!navLinks.classList.contains('open')));
navBackdrop.addEventListener('click', () => setMenu(false));
navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
document.addEventListener('keydown', e => { if (e.key === 'Escape') setMenu(false); });
window.addEventListener('resize', () => {
  if (window.innerWidth > 720 && navLinks.classList.contains('open')) setMenu(false);
});
