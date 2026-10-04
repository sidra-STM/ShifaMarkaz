const params = new URLSearchParams(location.search);
let doctorId = params.get('doctor');
let token = params.get('token');
let startAhead = null;
const box = document.getElementById('box');
let doctor = null;

// "My Queue" menu link has no parameters, so use the saved ticket
try {
  const saved = JSON.parse(localStorage.getItem('shifaTicket') || 'null');
  if (!doctorId || !token) {
    if (saved) {
      doctorId = saved.doctor;
      token = saved.token;
      startAhead = saved.startAhead;
    }
  } else if (saved && saved.doctor === doctorId && String(saved.token) === String(token)) {
    startAhead = saved.startAhead;
  }
} catch (e) {
}

const messages = {
  waiting: 'Please wait. This page updates by itself.',
  next: "You're next! Please get ready.",
  serving: "It's your turn. Please go in.",
  passed: 'Your token has passed. Please ask the clinic staff.'
};

async function update() {
  try {
    const s = await getJSON(`api/queue-status.php?doctor=${encodeURIComponent(doctorId)}&token=${encodeURIComponent(token)}`);
    const pct = s.status === 'serving'
      ? 100
      : Number.isFinite(startAhead) && startAhead > 0
        ? Math.max(0, Math.min(100, Math.round(((startAhead - s.ahead) / startAhead) * 100)))
        : 0;
    box.innerHTML = `
      <div class="card">
        <h3>${doctor ? esc(doctor.name) : ''}</h3>
        <p>${doctor ? esc(doctor.clinic) : ''}</p>
      </div>
      <div class="stats">
        <div class="stat big"><span>YOUR TOKEN</span><strong>#${s.yourToken}</strong></div>
        <div class="stat big"><span>NOW SERVING</span><strong>#${s.current}</strong></div>
        <div class="stat"><span>PATIENTS AHEAD</span><strong>${s.ahead}</strong></div>
        <div class="stat"><span>EST. WAIT</span><strong>${s.estimatedMinutes} min</strong></div>
      </div>
      <div class="progress"><div style="width:${pct}%"></div></div>
      <div class="banner ${esc(s.status)}">${esc(messages[s.status])}</div>`;
  } catch (e) {
    box.innerHTML = `<p class="empty">${esc(e.message)} <a href="doctors.php">Find a doctor</a></p>`;
  }
}

async function init() {
  if (!doctorId || !token) {
    box.innerHTML = '<p class="empty">You have no token yet. <a href="doctors.php">Find a doctor</a></p>';
    return;
  }
  try {
    const doctors = await getJSON('api/doctors.php');
    doctor = doctors.find(d => d.id === doctorId) || null;
  } catch (e) {}
  update();
  setInterval(update, 3000);
}
init();