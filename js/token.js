const doctorId = new URLSearchParams(location.search).get('doctor');
const box = document.getElementById('box');

async function init() {
  try {
    const doctors = await getJSON('api/doctors.php');
    const d = doctors.find(x => x.id === doctorId);
    if (!d) {
      box.innerHTML = '<p class="empty">Doctor not found. <a href="doctors.php">Back to doctors</a></p>';
      return;
    }
    const q = await getJSON('api/queue-summary.php?doctor=' + encodeURIComponent(doctorId));

    box.innerHTML = `
      <div class="card">
        <h2>${esc(d.name)}</h2>
        <p class="specialty">${esc(d.specialty)}</p>
        <p>${esc(d.clinic)}</p>
        <p>${esc(d.days)} | ${esc(d.hours)}</p>
      </div>
      <div class="stats">
        <div class="stat"><span>NOW SERVING</span><strong>#${q.current}</strong></div>
        <div class="stat"><span>WAITING</span><strong>${q.waiting}</strong></div>
        <div class="stat"><span>EST. WAIT</span><strong>${q.waiting * 10} min</strong></div>
      </div>
      <form id="tokenForm" class="card form">
        <label for="name">Your name</label>
        <input id="name" required maxlength="60" placeholder="Full name">
        <label for="phone">Phone number</label>
        <input id="phone" required maxlength="20" placeholder="03XX-XXXXXXX">
        <button class="btn" type="submit">Get my token</button>
        <p id="err" class="error"></p>
      </form>`;

    document.getElementById('tokenForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitButton = e.currentTarget.querySelector('button[type="submit"]');
      submitButton.disabled = true;
      try {
        const r = await postJSON('api/take-token.php', {
          doctor: doctorId,
          name: document.getElementById('name').value,
          phone: document.getElementById('phone').value
        });
        localStorage.setItem('shifaTicket', JSON.stringify({
          doctor: doctorId,
          token: r.yourToken,
          startAhead: r.ahead
        }));
        location.href = `queue.php?doctor=${encodeURIComponent(doctorId)}&token=${r.yourToken}`;
      } catch (err) {
        document.getElementById('err').textContent = err.message;
        submitButton.disabled = false;
      }
    });
  } catch (err) {
    box.innerHTML = '<p class="empty">Could not load. Is Apache running?</p>';
  }
}
init();