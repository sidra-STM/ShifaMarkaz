# ShifaMarkaz

**Gemini-powered healthcare navigation and digital queue prototype for Chitral.**

> Explore doctor specialties, take a demo queue token, and follow the queue.

Built by team **Builders** for the **Chitral AI Challenge 2026**
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

ShifaMarkaz connects four features into one flow:

**Symptoms -> Gemini specialty suggestion -> demo doctor directory -> queue token -> live demo queue**

### 1. Doctor directory
Browse sample doctor listings with their specialty, qualification, clinic, sample visiting days and hours, and teleconsultation flag. Search by name or clinic, or filter by specialty. Each doctor card shows the current demo queue. These listings and details are not verified real clinic availability.

### 2. AI Health Navigator
Gemini is the primary navigator: it receives the conversation and suggests **which type of doctor to see**. A valid Gemini API key and supported model must be configured for the intended AI experience. If a Gemini request temporarily fails, basic keyword rules provide a limited backup response. Matching sample doctors are shown with a **Get Token** button. This is a demo guide, not a clinical assessment.

### 3. Digital queue token
The patient picks a sample doctor, enters a name and phone number, and joins that doctor's demo queue. This issues a queue token; it does not book a date or appointment time. The queue screen shows:

- the patient's token,
- the token now being served,
- how many patients are ahead,
- a rough demo wait estimate (10 minutes per patient ahead),
- a live status: waiting, you're next, your turn, or token passed.

The screen refreshes by itself every 3 seconds.

### 4. Clinic dashboard
The demo dashboard lets a user select a sample doctor, see the demo waiting list, and press **Call Next Patient**. The current token changes, and queue screens update on their next poll.

### Doctor and clinic listing requests
Doctors and clinics can submit a listing request from the home page. Requests are saved locally in `data/doctor-registration-requests.json` and are not added to the public directory automatically. The data folder is protected from direct browser access by Apache.

---

## How the AI is used safely

| The AI does | The AI does NOT |
|---|---|
| Gemini interprets the conversation when correctly configured and reachable | Provide a verified clinical assessment |
| Ask short follow-up questions | Guarantee medically correct advice |
| Suggest an allowed specialty | Choose doctors outside the sample directory |

Safety design:

- **A limited emergency phrase check runs first.** Recognized phrases such as "chest pain" trigger an emergency message before Gemini is called. This is a prototype keyword check and cannot identify every emergency.
- **Doctor choices come from the sample directory.** The server matches the selected specialty to `doctors.json`; Gemini does not supply doctor records.
- **Specialties are allowlisted.** An unrecognized Gemini specialty defaults to General Physician.
- **Fallback rules.** If Gemini cannot be reached or returns an error, basic keyword rules provide a limited backup so the demo can continue. They do not replace Gemini's intended navigation experience.
- **AI output is not independently clinically verified.** Gemini is instructed not to diagnose or recommend medicines, but the prototype does not guarantee that every generated sentence follows those instructions.
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

### Steps

1. **Get the code** into XAMPP's web folder. Use the folder name `shifa`:
   ```text
   cd C:\xampp\htdocs
   git clone https://github.com/YOUR-USERNAME/shifamarkaz.git shifa
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

**Gemini setup is required for the intended AI Navigator experience.** If Gemini is not configured or a request fails, the app shows limited keyword-based backup guidance instead. Doctor records and queue activity remain demo data.

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