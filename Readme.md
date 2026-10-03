# ShifaMarkaz

**AI-powered healthcare navigation and digital queue system for Chitral.**

> Right doctor. Right time. No long waiting.

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

**Symptoms -> AI suggests specialty -> Chitral doctors -> Digital token -> Live queue**

### 1. Doctor directory
Browse doctors available in Chitral with their specialty, qualification, clinic or hospital, visiting days and hours, phone number and teleconsultation availability. Search by name or clinic, or filter by specialty. Each doctor card shows a live "Now serving" token.

### 2. AI Health Navigator
The user describes their problem in plain words (English, Urdu or Roman Urdu). The AI asks short follow-up questions and then suggests **which type of doctor to see**. The matching doctors from our Chitral directory are shown immediately, with a **Get Token** button.

### 3. Digital queue token
The patient picks a doctor, enters a name and phone number, and receives a token. The queue screen shows:

- the patient's token,
- the token now being served,
- how many patients are ahead,
- an estimated wait time (about 10 minutes per patient ahead),
- a live status: waiting, you're next, your turn, or token passed.

The screen refreshes by itself every 3 seconds.

### 4. Clinic dashboard
Clinic staff choose their doctor, see the waiting patients, and press **Call Next Patient**. The current token changes, and every patient's queue screen updates within seconds.

---

## How the AI is used safely

| The AI does | The AI does NOT |
|---|---|
| Understand symptoms in simple language | Diagnose diseases |
| Ask short follow-up questions | Name medicines or doses |
| Pick **one specialty from a fixed list** | Choose or invent doctors |

Safety design:

- **Emergency check runs first, in our own code.** Red-flag words such as chest pain or difficulty breathing show an emergency message (go to the nearest hospital or call Rescue 1122) *before* the AI is ever called.
- **Doctors come from our own directory.** The AI only returns a specialty. Our code matches doctors from `doctors.json`, so the AI cannot make up a doctor.
- **The specialty must be on the allowed list.** Anything else defaults to General Physician.
- **Backup rules.** If the AI service is unavailable (no internet, quota reached or an error), the navigator automatically switches to simple keyword rules, so the flow keeps working.
- **The API key stays on the server** in `api/config.php`. It is never sent to the browser and is excluded from Git.

---

## Technology

- **Frontend:** HTML, CSS, JavaScript (no frameworks, no build step)
- **Backend:** PHP
- **Data:** JSON files (`doctors.json`, `queues.json`)
- **AI:** Google Gemini API, called from PHP
- **Local server:** XAMPP (Apache)

Data flow for the AI:
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
├── js/                  Page scripts (doctors, navigator, token, queue, dashboard)
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
│   └── config.example.php   Template for the private config
└── data/
    ├── doctors.json         Demo doctors
    └── queues.json          Queue state (updated by PHP)
```

---

## How to run it

### Requirements
- Windows, macOS or Linux
- [XAMPP](https://www.apachefriends.org/) (only **Apache** is needed, not MySQL)
- A free Gemini API key from [Google AI Studio](https://aistudio.google.com/) (optional, see below)

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

**No API key?** The app still works. The navigator uses its built-in backup rules instead of the AI.

### Demo tips

- Reset all queues before each demo: open `http://localhost/shifa/api/reset.php`
- To show the patient screen on a phone, connect the phone to the same Wi-Fi and open `http://YOUR-LAPTOP-IP/shifa/` (you may need to allow Apache through the Windows Firewall).
- Use the **same doctor** for the token and the dashboard, so the patient screen visibly changes.

### Suggested demo flow

1. Open the navigator and describe a symptom, such as *"My child has fever and cough"*.
2. Answer the follow-up question and see the suggested specialty and Chitral doctors.
3. Type *"chest pain"* to show the emergency warning.
4. Choose a doctor, enter a name and phone number, and get a token.
5. On the clinic dashboard, press **Call Next Patient** and watch the patient's queue screen update.

---

## Limitations (prototype)

We want to be clear about what this prototype is and is not:

- **Demo data only.** Doctors, clinics and phone numbers are samples, not real people.
- **JSON files instead of a database.** Writes are protected with file locking, but a real system needs a proper database.
- **No login.** The clinic dashboard is open for demonstration. A real system needs staff authentication.
- **Simple wait estimate.** Wait time is patients ahead x 10 minutes, not a learned estimate.
- **Local-prototype settings.** The Gemini call disables SSL certificate verification so it works on a default Windows XAMPP install. This must be removed in any real deployment.
- **Not medical advice.** The AI suggests a type of doctor only. It is not a diagnosis tool and does not replace a doctor.

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