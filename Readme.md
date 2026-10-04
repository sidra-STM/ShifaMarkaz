# ShifaMarkaz

**Gemini-powered healthcare navigation and digital queue prototype for Chitral.**

> Explore doctor specialties, take a demo queue token, and follow the queue.

Built by team **The Builders** for the **Chitral AI Challenge 2026**
(HindukushSoft Technologies x Computer Science Department, University of Chitral).
Theme: *Learn. Build. Showcase. Connect.*

> **This is a prototype.** All doctor names, clinics and phone numbers are **demo data**. ShifaMarkaz gives guidance only. It does not diagnose diseases or prescribe medicines.

---

## The problem

In Chitral, many people:

- do not know **which doctor or specialist** to consult for their problem,
- do not know **where and when** a specialist is available,
- travel long distances and then **wait for hours** with no idea when their turn will come,
- cannot tell whether their problem needs **routine care or urgent attention**.

## Our solution

ShifaMarkaz has two connected sides:

**Patient side**
- Describe symptoms and get a suggested specialty (guidance only, not a diagnosis).
- Browse sample Chitral doctors and see when and where they are available.
- Take a digital queue token and follow your turn live from your phone.

**Clinic side**
- Clinic staff open the dashboard and see the waiting patients.
- They press "Call Next Patient" and the current token changes.
- Every patient's queue screen updates within seconds, so patients wait less at the clinic.

**Flow:** Symptoms -> AI suggests specialty -> Chitral doctors -> Digital token -> Live queue -> Clinic calls next patient

### Doctor registration (early module)
Doctors can submit a registration request from the footer link or at `doctor-register.php`. Submissions are saved as pending; nothing is published automatically. A secure admin panel for reviewing and approving registrations is planned for the future.

---

## How the AI is used safely

| The AI does | The AI does NOT |
|---|---|
| Gemini interprets the conversation and selects an allowed specialty when correctly configured and reachable | Provide a verified clinical assessment |
| Decide whether more information is needed | Guarantee medically correct advice |
| Suggest an allowed specialty | Choose doctors outside the sample directory |

Safety design:

- **A limited emergency phrase check runs first.** Recognized phrases such as "chest pain" trigger an emergency message before Gemini is called. This is a prototype keyword check and cannot identify every emergency.
- **Doctor choices come from the sample directory.** The server matches the selected specialty to `doctors.json`; Gemini does not supply doctor records.
- **Specialties are allowlisted.** A response with an unrecognized specialty is rejected and the limited keyword backup is used.
- **Displayed navigator wording is controlled by the app.** Gemini supplies a response type, an allowlisted specialty when appropriate, and a language choice. ShifaMarkaz generates the displayed questions, specialty guidance, and emergency wording; Gemini's free-text message is not shown.
- **Fallback rules.** If Gemini cannot be reached or returns an error, basic keyword rules provide a limited backup so the demo can continue. They do not replace Gemini's intended navigation experience.
- **AI specialty suggestions are not independently clinically verified.** The prototype is not a diagnosis or a substitute for a clinician.
- **The API key stays on the server** in `api/config.php`. It is never sent to the browser and is excluded from Git.

---

## Technology

- **Frontend:** HTML, CSS, JavaScript (no frameworks, no build step)
- **Backend:** PHP
- **Data:** JSON files (`doctors.json`, `queues.json`)
- **AI:** Google Gemini API as the primary navigator; local keyword backup for request failures
- **Local server:** XAMPP (Apache)

Primary AI data flow:
`Browser (JavaScript) -> api/navigator.php -> Gemini API -> api/navigator.php -> Browser`

## Project structure

```text
shifa/
├── index.php            Home page
├── doctors.php          Doctor directory
├── navigator.php        AI Health Navigator (chat)
├── token.php            Get a queue token
├── queue.php            Patient's live queue screen
├── dashboard.php        Clinic dashboard
├── doctor-register.php  Doctor/clinic registration request form
├── includes/            Shared header and footer
├── css/style.css        Styles
├── js/                  Page scripts (doctors, navigator, token, queue, dashboard, registration)
├── api/                 PHP endpoints (JSON in, JSON out)
│   ├── helpers.php          Shared functions and safe file locking
│   ├── doctors.php          Doctor list
│   ├── navigator.php        AI navigator and backup rules
│   ├── take-token.php       Issue a token
│   ├── queue-status.php     Patient's position
│   ├── queue-summary.php    Current token and waiting count
│   ├── queue-all.php        Live badges for the directory
│   ├── dashboard-data.php   Clinic dashboard data
│   ├── call-next.php        Call the next patient
│   ├── reset.php            Reset demo queues
│   ├── register-doctor.php  Save a doctor/clinic listing request
│   └── config.example.php   Template for the private config
└── data/
    ├── doctors.json         Sample doctor listings
    ├── queues.json          Demo queue state (updated by PHP)
    ├── .htaccess            Deny direct browser access to data files
    └── doctor-registration-requests.json  Created when a request is submitted (ignored by Git)
```

---

## How to run it

### Requirements
- Windows, macOS or Linux
- [XAMPP](https://www.apachefriends.org/) (only **Apache** is needed, not MySQL)
- A Gemini API key from [Google AI Studio](https://aistudio.google.com/) for the AI Navigator
- PHP's **cURL** and **mbstring** extensions enabled in XAMPP's `php.ini` (restart Apache after enabling them)

### Steps

1. **Get the code** into XAMPP's web folder. Use the folder name `shifa`:
   ```text
   cd C:\xampp\htdocs
   git clone https://github.com/sidra-STM/ShifaMarkaz.git shifa
   ```
2. **Create your private config.** Copy `api/config.example.php` to `api/config.php` and fill in your values:
   ```php
   <?php
   return [
       'gemini_key'   => 'YOUR_API_KEY',
       'gemini_model' => 'gemini-3.8-flash',
   ];
   ```
   Model names change over time. If you get a "model not found" error, check which models your key can use in Google AI Studio.
3. **Start Apache** in the XAMPP Control Panel.
4. **Open** `http://localhost/shifa/` in your browser.
5. Ensure `data/queues.json` is writable by Apache. The `data` directory must also be writable so doctor registration requests can be created.

**Gemini setup is required for the intended AI Navigator experience.** If Gemini is not configured or a request fails, the app shows limited keyword-based backup guidance instead. Doctor records and queue activity remain demo data.
The navigator labels whether each answer used Gemini or rule-based backup guidance. Before presenting, submit a sample symptom and confirm the Gemini label appears; a backup label means the key, model, network, or available API quota needs attention. Never present a backup response as a Gemini response.

### Demo tips

- Reset all queues before each demo: open `http://localhost/shifa/api/reset.php`
- To show the patient screen on a phone, connect the phone to the same Wi-Fi and open `http://YOUR-LAPTOP-IP/shifa/` (you may need to allow Apache through the Windows Firewall).
- Use the **same doctor** for the token and the dashboard, so the patient screen visibly changes.

### Suggested demo flow

1. Open the navigator and try a sample symptom such as *"My child has fever and cough"*.
2. Answer the follow-up and see Gemini's suggested specialty with matching sample doctors. If the Gemini request fails, the interface identifies when limited backup guidance is being shown.
3. Type *"chest pain"* to demonstrate the limited emergency phrase check.
4. Choose a sample doctor, enter demo details, and join that doctor's queue with a token. This is not a scheduled appointment.
5. On the demo dashboard, press **Call Next Patient** and watch the queue screen update.

---

## Limitations (prototype)

We want to be clear about what this prototype is and is not:

- **Demo data only.** Doctors, clinics, phone numbers, queue activity, and wait estimates are samples, not real clinic information.
- **JSON files instead of a database.** Writes are protected with file locking, but a real system needs a proper database.
- **No login.** The clinic dashboard is open for demonstration. A real system needs staff authentication.
- **Simple wait estimate.** The demo uses patients ahead x 10 minutes; this is not a live clinic estimate.
- **Local-prototype settings.** The Gemini call disables SSL certificate verification so it works on a default Windows XAMPP install. This must be removed in any real deployment.
- **Not medical advice.** Navigator output is prototype guidance, not a diagnosis, triage service, or substitute for a clinician.

## Future work

- Verified real doctor and clinic data, managed by the clinics themselves
- Admin panel to verify and approve doctor registrations
- Real database and secure staff login
- Full Urdu and Khowar interface
- SMS or WhatsApp alerts when a patient's turn is near
- Clinic-specific average consultation times for better wait estimates
- Support for hospitals, labs and pharmacies

## Team Builders

- Sidratul Muntaha
- Wajeeha Nasir
- Amina Bibi

*Chitral AI Challenge 2026 - Learn. Build. Showcase. Connect.*