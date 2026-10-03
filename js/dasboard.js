const select = document.getElementById('doctorSelect');
const msg = document.getElementById('msg');
let doctorId = '';

async function refresh() {
  if (!doctorId) return;
  try {
    const d = await getJSON('api/dashboard-data.php?doctor=' + encodeURIComponent(doctorId));
    document.getElementById('nowServing').textContent = '#' + d.current;
    document.getElementById('servingName').textContent = d.serving ? d.serving.name : '';
    document.getElementById('waitingCount').textContent = d.waiting.length;
    document.getElementById('servedCount').textContent = d.servedToday;
    document.getElementById('rows').innerHTML = d.waiting.length === 0
      ? '<tr><td colspan="4">No patients waiting.</td></tr>'
      : d.waiting.map(t => `<tr><td>#${t.token}</td><td>${esc(t.name)}</td><td>${esc(t.phone)}</td><td>${esc(t.time)}</td></tr>`).join('');
  } catch (e) {
    msg.textContent = e.message;
  }
}

async function init() {
  const doctors = await getJSON('api/doctors.php');
  select.innerHTML = doctors.map(d => `<option value="${esc(d.id)}">${esc(d.name)} (${esc(d.specialty)})</option>`).join('');
  const saved = localStorage.getItem('shifaDashDoctor');
  if (saved && doctors.some(d => d.id === saved)) select.value = saved;
  doctorId = select.value;
  refresh();
  setInterval(refresh, 3000);
}

select.addEventListener('change', () => {
  doctorId = select.value;
  localStorage.setItem('shifaDashDoctor', doctorId);
  msg.textContent = '';
  refresh();
});

document.getElementById('callNext').addEventListener('click', async () => {
  msg.textContent = '';
  try {
    const r = await postJSON('api/call-next.php', { doctor: doctorId });
    if (!r.ok) msg.textContent = r.message;
    refresh();
  } catch (e) {
    msg.textContent = e.message;
  }
});

document.getElementById('resetBtn').addEventListener('click', async () => {
  if (!confirm('Reset the demo queue for this doctor?')) return;
  await postJSON('api/reset.php', { doctor: doctorId });
  refresh();
});

init();