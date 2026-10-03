const chat = document.getElementById('chat');
const input = document.getElementById('msg');
const sendBtn = document.getElementById('send');
let messages = [];
let finished = false;

function addBubble(cls, html) {
  const div = document.createElement('div');
  div.className = 'bubble ' + cls;
  div.innerHTML = html;
  chat.appendChild(div);
  chat.scrollTop = chat.scrollHeight;
  return div;
}

function setBusy(busy) {
  sendBtn.disabled = busy || finished;
  input.disabled = busy || finished;
}

function showResult(r) {
  const docs = r.doctors.map(d => `
    <div class="mini">
      <div><strong>${esc(d.name)}</strong><br><span>${esc(d.clinic)} | ${esc(d.days)}</span></div>
      <a class="btn" href="token.php?doctor=${encodeURIComponent(d.id)}">Get Token</a>
    </div>`).join('');
  addBubble('bot', `
    <p>${esc(r.message)}</p>
    <p class="specialty">Suggested specialty: ${esc(r.specialty)}</p>
    ${docs}
    <p class="disclaimer">Guidance only. Not a diagnosis.</p>
    <p><a href="navigator.php">Start again</a></p>`);
}

async function send(text) {
  text = text.trim();
  if (!text || finished) return;
  input.value = '';
  addBubble('user', esc(text));
  messages.push({ role: 'user', content: text });
  setBusy(true);
  const typing = addBubble('bot', '<em>Thinking...</em>');

  try {
    const r = await postJSON('api/navigator.php', { messages });
        console.log('NAV RESPONSE:', r);
    typing.remove();
    messages.push({ role: 'assistant', content: r.message });
    if (r.type === 'emergency') {
      finished = true;
      addBubble('bot emergency', `<p><strong>${esc(r.message)}</strong></p><a class="btn" href="tel:1122">Call Rescue 1122</a><p><a href="navigator.php">Start again</a></p>`);
    } else if (r.type === 'result') {
      finished = true;
      showResult(r);
    } else {
      addBubble('bot', esc(r.message));
    }
  } catch (e) {
    typing.remove();
    messages.pop();
    addBubble('bot', 'Sorry, something went wrong. Please try again.');
  }
  setBusy(false);
  if (!finished) input.focus();
}

sendBtn.addEventListener('click', () => send(input.value));
input.addEventListener('keydown', e => { if (e.key === 'Enter') send(input.value); });
document.querySelectorAll('.example').forEach(b => b.addEventListener('click', () => send(b.dataset.text)));

addBubble('bot', 'Hello! Tell me what problem you are having, and I will help you choose the right type of doctor.');