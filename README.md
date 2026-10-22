# Custom Type Master Pro — PHP & MySQL Web Application

A complete, professional typing practice and analytics web application built with **PHP 8.2+**, **MySQL**, **HTML5**, **CSS3**, **JavaScript (Vanilla)**, **Bootstrap 5**, and **Chart.js**.

Designed to replace desktop-heavy Electron apps with a lightweight, secure, ultra-responsive, and visually stunning web architecture running locally or deployed to any standard cloud or PHP hosting provider.

---

## 🌟 Key Features

### 1. Advanced Real-Time Typing Engine
* **Advanced Real-Time Word Count**: Displays words completed, words remaining, total passage words, and real-time active word indicators.
* **Scientific Scoring**:
  * **Gross WPM**: Standard formula $\frac{\text{Total Characters Typed} / 5}{\text{Elapsed Minutes}}$
  * **Net WPM**: $\text{Gross WPM} - \frac{\text{Uncorrected Errors}}{\text{Elapsed Minutes}}$ (safely handled $\ge 0$)
  * **Accuracy**: $\frac{\text{Correct Keystrokes}}{\text{Total Keystrokes}} \times 100\%$
* **Character-by-Character Feedback**:
  * Correct keystrokes highlighted in customizable green
  * Incorrect keystrokes highlighted in red with background tint
  * Active caret with smooth pulse (Line, Block, or Underline)
* **Zero-Dependency Sound Synthesizer**: Built with HTML5 Web Audio API (mechanical clacks, tactile clicks, error buzz, and finish fanfare) — works 100% offline without external audio files!
* **Practice Durations**: 15s, 30s, 60s, 120s, and 300s timed countdowns or full-passage practice drills.
* **Custom Text Drill**: Paste any custom paragraph, article, or programming snippet to practice on demand.

### 2. Comprehensive Analytics & Dashboard
* **Real-Time Dashboard**: Total tests, Best Net WPM, Average Net WPM, Average Accuracy %, Total Practice Time, and Total Words Typed.
* **Weekly Trajectory Chart**: Dynamic Chart.js dual-axis graph displaying speed progression alongside accuracy trends.
* **Achievement Milestones**: Unlock badges for 40+ WPM (Everyday Typist), 60+ WPM (Professional), 80+ WPM (Grandmaster), and 100+ WPM (Typing God).
* **Deep Statistics Page**:
  * Speed progression across sessions
  * Keystroke breakdown doughnut (Correct vs Errors vs Backspaces)
  * Speed comparisons across difficulty levels
  * Growth velocity metrics (Recent 5 vs Previous 5 sessions)

### 3. Theme & Deep Customization
* **Four Curated Themes**: Dark Obsidian, Light Minimal, Cyberpunk Neon, and Nord Arctic.
* **Customizable Typography**: JetBrains Mono, Fira Code, Roboto Mono, and Inter.
* **Visual Tuning**: Adjustable font size (14–36px), line-height, arena width, caret style, caret color, and character feedback colors.
* Settings automatically persist to MySQL and restore upon every login.

### 4. History & Export Tools
* **Paginated Session History**: Search and filter by passage title or difficulty level.
* **One-Click Exports**: Export complete user test history directly to **CSV** or formatted **JSON**.
* **Individual Session Reports**: View granular test statistics, retype the same passage, or copy a brag summary to the clipboard.

### 5. Role-Based Administration Panel
* **Administrator Overview**: Total registered users, total tests taken, passage count, and global platform WPM.
* **User Management**: View typist profiles, change roles (user $\leftrightarrow$ admin), and delete accounts.
* **Passage Management**: Add, edit, or deactivate passages with automatic word count and character count calculation.

---

## 🗄️ Database Architecture (`type.sql`)

The application database is named `custom_type_master_pro`.

| Table | Description |
|---|---|
| `users` | User credentials, password hashes (`password_hash`), full names, and roles (`admin`/`user`). |
| `user_settings` | Per-user customization: theme, font family, font size, caret styles, colors, sound theme, and defaults. |
| `typing_passages` | Curated passages categorized by Beginner, Intermediate, and Advanced, pre-calculated word and character counts. |
| `typing_results` | Complete historical records: Gross WPM, Net WPM, accuracy %, elapsed seconds, keystroke counts, backspaces, and timestamps. |

The database file is located at the project root: **`type.sql`** (and also in **`database/schema.sql`**).

---

## 🚀 Quick Setup & Installation Guide

### Step 1: Start Apache and MySQL Web Server
1. Launch your web server environment (e.g. Apache & MySQL).
2. Ensure both services are actively running.

### Step 2: Import the Database Schema
1. Open your browser and navigate to: `http://localhost/phpmyadmin/`
2. Click the **Import** tab at the top.
3. Click **Choose File** and select `type.sql` from the project directory:
   ```
   d:\xammp1\htdocs\type mater pro\type.sql
   ```
4. Click **Import** at the bottom.
5. The database `custom_type_master_pro` along with all tables, sample passages, and default accounts will be created immediately.

*(Alternative via Command Line / PowerShell):*
```powershell
& "d:\xammp1\mysql\bin\mysql.exe" -u root -e "SOURCE d:/xammp1/htdocs/type mater pro/type.sql"
```

### Step 3: Access the Application
Open your browser and visit:
```
http://localhost/type%20mater%20pro/
```
*(Or your virtual host / folder name inside `htdocs`)*

---

## 🔑 Default Credentials

The database schema seeds two pre-configured accounts:

| Role | Username | Email | Password |
|---|---|---|---|
| **Administrator** | `admin` | `admin@typemaster.pro` | `Admin@123456` |
| **Demo Typist** | `demouser` | `demo@typemaster.pro` | `Admin@123456` |

You can also click **Create Account** on the landing or login page to register a new user anytime.

---

## 📁 Project Structure

```
type mater pro/
├── index.php                 # Landing page & showcase
├── login.php                 # User login with CSRF & password_verify
├── register.php              # User registration & automatic settings setup
├── logout.php                # Safe session destruction
├── dashboard.php             # Analytics, KPI metrics & Chart.js graph
├── practice.php              # Flagship interactive typing arena & live engine
├── history.php               # Paginated session logs with search & filters
├── results.php               # Granular test breakdown & share report
├── statistics.php            # Deep dive Chart.js graphs & velocity
├── settings.php              # Live theme, font, caret & audio customizer
├── export.php                # CSV & JSON export engine
├── type.sql                  # Complete SQL schema & seed data
├── config/
│   ├── app.php               # Base URL resolution & session bootstrap
│   └── database.php          # Singleton PDO connection & UTF-8 mb4
├── includes/
│   ├── auth.php              # Authentication guards & user functions
│   ├── csrf.php              # CSRF token generator & validator
│   ├── header.php            # Responsive navbar, theme injector & CSS
│   ├── sidebar.php           # Collapsible glassmorphism sidebar
│   ├── footer.php            # Scripts & keyboard shortcut listeners
│   └── functions.php         # Scientific WPM math, formatting & badges
├── admin/
│   ├── index.php             # Admin overview & system statistics
│   ├── users.php             # User role management & account deletion
│   └── passages.php          # Passage creation, editing & word counting
├── api/
│   ├── save-result.php       # AJAX endpoint for saving completed test sessions
│   ├── get-passage.php       # AJAX endpoint for random/specific passage loading
│   └── save-settings.php     # AJAX endpoint for asynchronous preferences
├── assets/
│   ├── css/
│   │   └── style.css         # Modern glassmorphism CSS & multi-theme variables
│   └── js/
│       ├── app.js            # Theme switcher, toasts & responsive sidebar
│       └── typing-engine.js  # Core typing engine with real-time word count & audio
└── database/
    └── schema.sql            # Mirror copy of database schema
```

---

## 🔒 Security Best Practices Implemented
* **PDO Prepared Statements**: 100% of user-supplied database interactions use parameterized queries.
* **Cross-Site Request Forgery (CSRF)**: All POST requests and AJAX actions validate cryptographic tokens.
* **Password Security**: Passwords are securely hashed with `password_hash($pass, PASSWORD_BCRYPT)`.
* **Cross-Site Scripting (XSS)**: All user-generated and database-rendered content is escaped via `e()` (`htmlspecialchars`).
* **Session Security**: Session cookies enforce `httponly = true` and `samesite = Lax`.
* **Access Control**: Role-based authentication guards prevent regular users from accessing administrator routes or modifying other users' records.
