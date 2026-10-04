const registrationForm = document.getElementById('registrationForm');
const registrationMessage = document.getElementById('registrationMessage');

registrationForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  const submitButton = registrationForm.querySelector('button[type="submit"]');
  submitButton.disabled = true;
  registrationMessage.textContent = '';

  const form = new FormData(registrationForm);
  try {
    const result = await postJSON('api/register-doctor.php', {
      clinicName: form.get('clinicName'),
      specialty: form.get('specialty'),
      contactName: form.get('contactName'),
      phone: form.get('phone'),
      email: form.get('email')
    });
    registrationMessage.textContent = result.message;
    registrationForm.reset();
  } catch (error) {
    registrationMessage.textContent = error.message;
  } finally {
    submitButton.disabled = false;
  }
});
