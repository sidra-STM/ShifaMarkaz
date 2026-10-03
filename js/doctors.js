let allDoctors = [];
let queues = {};
let activeSpecialty = 'All';

const grid = document.getElementById('grid');
const chips = document.getElementById('chips');
const search = document.getElementById('search');

function esc(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function renderChips() {
  const specialties = ['All', ...new Set(allDoctors.map(d => d.specialty))];
  chips.innerHTML = specialties.map(s =>
    `<button class="chip ${s === activeSpecialty ? 'active' : ''}" data-s="${esc(s)}">${esc(s)}</button>`
  ).join('');
  chips.querySelectorAll('.chip').forEach(btn => {
    btn.addEventListener('click', () => {
      activeSpecialty = btn.dataset.s;
      renderChips();
      renderDoctors();
    });
  });
}

function renderDoctors() {
  const term = search.value.toLowerCase();
  const list = allDoctors.filter(d => {
    const matchSpecialty = activeSpecialty === 'All' || d.specialty === activeSpecialty;
    const text = (d.name + d.specialty + d.clinic + d.qualification).toLowerCase();
    return matchSpecialty && text.includes(term);
  });

  if (list.length === 0) {
    grid.innerHTML = '<p class="empty">No doctors found.</p>';
    return;
  }

  grid.innerHTML = list.map(d => {
    const q = queues[d.id];
    const live = q
      ? `<div class="now"><span class="live"></span>Now serving #${q.current} &middot; ${q.waiting} waiting</div>`
      : '';
    return `
    <div class="card">
      <div class="avatar">${esc(d.name.replace('Dr. ', '').charAt(0))}</div>
      <h3>${esc(d.name)}</h3>
      <p class="specialty">${esc(d.specialty)}</p>
      <p>${esc(d.qualification)}</p>
      <p>${esc(d.clinic)}</p>
      <p>${esc(d.days)} | ${esc(d.hours)}</p>
      <p>${esc(d.phone)}</p>
      ${d.teleconsultation ? '<span class="badge">Teleconsultation</span>' : ''}
      ${live}
      <a class="btn" href="token.php?doctor=${encodeURIComponent(d.id)}">Get Token</a>
    </div>`;
  }).join('');
}

async function loadQueues() {
  try {
    const res = await fetch('api/queue-all.php');
    queues = await res.json();
    renderDoctors();
  } catch (e) {}
}

async function loadDoctors() {
  try {
    const res = await fetch('api/doctors.php');
    allDoctors = await res.json();
    const fromUrl = new URLSearchParams(window.location.search).get('specialty');
    if (fromUrl) activeSpecialty = fromUrl;
    renderChips();
    renderDoctors();
    loadQueues();
    setInterval(loadQueues, 5000);
  } catch (err) {
    grid.innerHTML = '<p class="empty">Could not load doctors. Is Apache running?</p>';
  }
}

search.addEventListener('input', renderDoctors);
loadDoctors();