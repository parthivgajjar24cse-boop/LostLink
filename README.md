# 🎓 LostLink — University Lost & Found System

**LostLink** is a modern, lightweight, and secure web application designed specifically for university campuses to report, locate, and claim lost and found belongings. Built entirely with native web technologies—**PHP**, **MySQL**, **Vanilla JavaScript**, and custom **Vanilla CSS**—it provides a frictionless, fast, and responsive user experience without relying on bulky external frameworks.

---

## 📌 Table of Contents

- [Overview & Mission](#-overview--mission)
- [Technology Stack](#-technology-stack)
- [System Architecture & Workflow](#-system-architecture--workflow)
- [Core Features & Functionality](#-core-features--functionality)
  - [1. User Management & Authentication](#1-user-management--authentication)
  - [2. Lost & Found Reporting System](#2-lost--found-reporting-system)
  - [3. Discovery & Real-Time Filtering](#3-discovery--real-time-filtering)
  - [4. Item Details & Verification](#4-item-details--verification)
  - [5. Internal Messaging & Live Chat](#5-internal-messaging--live-chat)
  - [6. Notifications & Update Feeds](#6-notifications--update-feeds)
  - [7. Modern Surface UI & Dark Mode](#7-modern-surface-ui--dark-mode)
- [Security & Defensive Architecture](#-security--defensive-architecture)
- [Database Schema](#-database-schema)
- [Directory Structure](#-directory-structure)
- [Installation & Local Setup](#-installation--local-setup)
- [User Guide: How to Use LostLink](#-user-guide-how-to-use-lostlink)
- [License](#-license)

---

## 🌟 Overview & Mission

On a busy university campus, students and staff routinely misplace crucial belongings—student IDs, laptops, keys, wallets, and notebooks. Traditional physical lost-and-found desks suffer from limited hours, poor visibility, and slow communication.

**LostLink** solves this problem by providing:
- **Centralized Digital Registry**: A transparent database of lost and found items accessible campus-wide.
- **Unique Identification Codes (IU Numbers)**: Automated tracking codes (`L001`, `F001`, etc.) for every submitted item.
- **Verified Campus Profiles**: Student information (Enrollment Number, Department, Batch, Semester, Phone) attached to reports to deter fraudulent claims.
- **Private In-App Messaging**: Instant 1-on-1 item chat allowing finders and owners to coordinate returns safely without broadcasting personal contact numbers publicly.
- **Instant Response**: Built-in 5-second asynchronous polling for live messaging without requiring full page reloads.

---

## 🛠️ Technology Stack

| Layer | Technology | Description |
| :--- | :--- | :--- |
| **Backend Language** | **PHP 8.x** | Strict typing (`declare(strict_types=1)`), modular architecture, native session handling, and clean code organization. |
| **Database** | **MySQL / MariaDB** | Relational schema with InnoDB engine, `utf8mb4_unicode_ci` charset, foreign key constraints (`ON DELETE CASCADE`, `ON DELETE SET NULL`), and indexing. |
| **Database Interface** | **PDO (PHP Data Objects)** | 100% prepared statements with bound parameters to completely eliminate SQL injection risks. |
| **Frontend Styling** | **Vanilla CSS3** | Custom design system with CSS custom properties (`:root` tokens), YouTube-inspired flat surface layers, zero heavy drop shadows, and responsive media queries. |
| **Frontend Scripting** | **Vanilla JavaScript (ES6+)** | Pure client-side logic: live DOM search filter, theme toggle with `localStorage` persistence, and Fetch API asynchronous polling for chat. |
| **Web Server** | **Apache (XAMPP / WAMP / LAMP)** | Standard web server environment. |

---

## 🔄 System Architecture & Workflow

```
+-----------------------------------------------------------------------------------+
|                                  GUEST VISITOR                                    |
|   - Browse Recent Reports (index.php)                                             |
|   - Instant Search & Filter (data-filter-table)                                   |
|   - View Item Specifications (item.php)                                           |
+----------------------------------------+------------------------------------------+
                                         |
                       +-----------------+-----------------+
                       |                                   |
                       v                                   v
             [ Register Account ]                    [ Log In ]
             (register.php)                          (login.php)
                       |                                   |
                       +-----------------+-----------------+
                                         |
                                         v
+-----------------------------------------------------------------------------------+
|                             AUTHENTICATED STUDENT                                 |
|                                                                                   |
|  [ Dashboard ]          [ Report Item ]         [ Communication ]   [ Account ]   |
|  - Statistics           - Lost (L-series)       - Live Chat (5s)    - Edit Profile|
|  - Quick Actions        - Found (F-series)      - Notifications     - Password    |
|  - Side Navigation      - Image Upload (5MB)    - Read Receipts     - Logout      |
+-----------------------------------------------------------------------------------+
```

### 1. Item Lifecycle
1. **Report Submission**: A student reports a lost or found item with date, time, location, description, category, and an optional photograph.
2. **Auto-Tagging**: The system generates a sequential tracking number (e.g., `L005` for Lost, `F012` for Found).
3. **Public Discovery**: The item immediately appears on the live public feed and directory tables with status set to `OPEN`.
4. **Coordination**: Another user spots the report, opens the item page, and clicks **Start Conversation**.
5. **Private Chat**: The two parties exchange messages within an item-bound chat thread to verify ownership and arrange handover.
6. **Resolution**: Once returned, the status transitions to `RESOLVED`.

---

## 🚀 Core Features & Functionality

### 1. User Management & Authentication
- **University-Specific Registration (`register.php`)**:
  - Collects username, university email, department, semester, batch, enrollment number, and phone number.
  - Enforces unique constraints on email and enrollment numbers.
  - Requires minimum 8-character password with confirmation check.
  - Uses `password_hash()` with `PASSWORD_DEFAULT` (Bcrypt) for one-way secure credential hashing.
- **Dual-Identifier Login (`login.php`)**:
  - Allows students to log in using either their **University Email** or **Enrollment Number**.
  - Invokes `session_regenerate_id(true)` upon successful verification to mitigate session fixation attacks.
- **Account & Security Center (`account.php`)**:
  - Displays user identity (email and enrollment number are locked as permanent identifiers).
  - Allows editing active profile details (department, semester, batch, contact phone).
  - Dedicated password update form requiring verification of the existing password before setting a new one.
- **Secure Logout (`logout.php`)**:
  - Clears all session data (`$_SESSION = []`), destroys the server session, and redirects with a flash notification.

### 2. Lost & Found Reporting System
- **Split Routing (`report_lost.php` & `report_found.php`)**:
  - Clean modular endpoints feeding directly into a unified processor (`report_item.php`).
- **Sequential IU Number Generation**:
  - Automatically queries the database for the highest existing numerical sequence for that category (`type = 'LOST'` or `type = 'FOUND'`) and assigns the next zero-padded identifier (e.g., `L001`, `L002` / `F001`, `F002`).
- **Comprehensive Metadata Capture**:
  - Item name, category (e.g., Electronics, Stationery, Cards/IDs, Clothing), incident date, incident time, location, detailed description, and contact phone.
- **Secure Local Photo Upload**:
  - Allows JPG, PNG, or WEBP images up to 5 MB.
  - Validates file MIME type server-side using PHP's `finfo`.
  - Renames the file to a cryptographically secure 32-character hexadecimal filename to prevent path traversal or execution attacks.
  - Saves images to `uploads/items/`.
- **Automated Reporter Notification**:
  - Automatically logs an entry into the user's notification feed confirming successful publication with the assigned IU number.

### 3. Discovery & Real-Time Filtering
- **Home / Welcome Hub (`index.php`)**:
  - Shows an introductory hero banner for guests and a direct dashboard link for logged-in students.
  - Displays the 12 most recent open reports in a clean data table.
- **Dedicated Section Feeds**:
  - `lost.php`: Complete catalog of active lost item reports.
  - `found.php`: Complete catalog of active found item reports.
- **Instant Client-Side Search (`js/app.js`)**:
  - Integrated via `data-filter-table` attributes.
  - Zero-latency, instant keystroke filtering: rows are dynamically hidden or revealed without page reloading or server requests.
  - Searches across item name, IU number, category, location, phone number, and status.

### 4. Item Details & Verification
- **Item Overview (`item.php`)**:
  - Accessible to both guests and registered members.
  - Distinct badge indicators differentiating `LOST` (accent styling) and `FOUND` status.
  - Full details list: IU number, category, incident date (`DD/MM/YYYY`), time (`HH:MM`), location, formatted description, status, and contact phone.
  - Displays uploaded photograph in an adaptive container, or a placeholder if no image was provided.
  - Dynamic call-to-action button:
    - Guests: Prompted to **Log in to Contact Reporter**.
    - Logged-in non-owners: Direct **Start Conversation** button to open a private message thread.
    - Owners: Action button is suppressed on their own items.

### 5. Internal Messaging & Live Chat
- **Item-Bound Private Conversations (`chat.php`)**:
  - Creates a dedicated communication channel tied to a specific item between the reporter and claimant/finder.
  - Displays a verified snapshot of the reporter's credentials directly atop the chat box (Username, Department, Semester, Batch, Enrollment Number, Phone).
- **Asynchronous Live Polling (`fetch_messages.php` & `js/app.js`)**:
  - JavaScript executes an asynchronous `fetch()` request every 5 seconds to load newly arrived messages.
  - Updates the DOM smoothly and automatically scrolls to the newest message.
  - Automatically marks incoming messages as read (`is_read = 1`).
- **Message Dispatch (`send_message.php`)**:
  - Validates CSRF tokens and message length (up to 2000 characters).
  - Records the message in the database.
  - Creates a real-time notification for the recipient alerting them of the message with the item IU number.

### 6. Notifications & Update Feeds
- **Personalized Notification Inbox (`updates.php`)**:
  - Chronological history of account activity (up to 50 entries): report creation confirmations, incoming messages, and status updates.
  - Timestamped in `DD/MM/YYYY HH:MM` format.
  - Direct clickable links to the relevant items.
  - Automatically updates unread notifications to read on visit.

### 7. Modern Surface UI & Dark Mode
- **YouTube-Style Surface Aesthetics (`css/style.css`)**:
  - Uses layered surfaces (`--surface`, `--surface-secondary`, `--surface-hover`, `--surface-active`) instead of heavy, outdated drop shadows.
  - Clean borders (`--border`, `--border-strong`, `--border-subtle`) for modern, crisp contrast.
  - High-readability typography powered by the Google Font **Inter**.
- **Dark Mode System**:
  - Interactive theme switcher in the site header.
  - Automatically detects the user's OS / browser color scheme preference (`prefers-color-scheme: dark`).
  - Saves theme selection in `localStorage('lostlink-theme')` so user preference persists across pages and sessions.
- **Adaptive Mobile Layout (`css/mobile.css`)**:
  - Responsive breakpoints at `860px` and `600px`.
  - Turns sidebar into horizontal navigation pills on tablets and mobile screens.
  - Converts tables with horizontal scroll wrappers (`.table-wrap`) to maintain table integrity on small screens.
  - Full-width touch-friendly buttons and forms.

---

## 🛡️ Security & Defensive Architecture

LostLink is built with defense-in-depth principles:

1. **SQL Injection Prevention**:
   All database operations use PDO prepared statements with parameterized inputs. No raw SQL concatenation exists anywhere in the codebase.
2. **Cross-Site Scripting (XSS) Mitigation**:
   All dynamic database output rendered in HTML templates passes through the `e()` helper function, which applies `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')`.
3. **Cross-Site Request Forgery (CSRF) Protection**:
   Every state-altering HTTP `POST` request (login, registration, profile update, password change, report submission, and sending messages) requires a unique token generated via `random_bytes(32)`. Tokens are strictly verified using timing-attack-safe `hash_equals()`.
4. **Session Fixation & Hijacking Safeguards**:
   Active sessions invoke `session_regenerate_id(true)` upon successful user authentication.
5. **Secure File Upload Validation**:
   Uploaded images are inspected using PHP's `finfo` MIME type inspection (`image/jpeg`, `image/png`, `image/webp`). Client-supplied filenames and extensions are discarded and replaced with server-generated random hashes. Execution in the upload directory is prevented.
6. **Strict Input Validation**:
   Email validation via `filter_var(..., FILTER_VALIDATE_EMAIL)`, integer casting for ID parameters with `FILTER_VALIDATE_INT`, string length constraints, and required fields checks.

---

## 🗄️ Database Schema

The database consists of 4 relational tables created in `database/lostlink.sql`:

```
  +------------------+         +------------------+
  |      users       |         |      items       |
  +------------------+         +------------------+
  | id (PK)          |<---+    | id (PK)          |<---+
  | username         |    |    | iu_number (UQ)   |    |
  | email (UQ)       |    +---[| user_id (FK)     |    |
  | password         |         | type [LOST/FOUND]|    |
  | department       |         | item_name        |    |
  | semester         |         | category         |    |
  | batch            |         | description      |    |
  | enrollment_no(UQ)|         | location         |    |
  | phone            |         | event_date       |    |
  | created_at       |         | event_time       |    |
  | updated_at       |         | phone            |    |
  +------------------+         | image_path       |    |
           |                   | status           |    |
           |                   | created_at       |    |
           |                   +------------------+    |
           |                            |              |
           |      +---------------------+              |
           |      |                                    |
           v      v                                    |
  +------------------+                                 |
  |     messages     |                                 |
  +------------------+                                 |
  | id (PK)          |                                 |
  | item_id (FK) ----+---------------------------------+
  | sender_id (FK) --+---> users(id)
  | receiver_id (FK)-+---> users(id)
  | message          |
  | is_read          |
  | created_at       |
  +------------------+

  +-------------------+
  |   notifications   |
  +-------------------+
  | id (PK)           |
  | user_id (FK) -----+---> users(id)
  | title             |
  | message           |
  | related_item_id --+---> items(id) [ON DELETE SET NULL]
  | is_read           |
  | created_at        |
  +-------------------+
```

### Table Specifications

#### 1. `users`
- `id`: `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `username`: `VARCHAR(60) NOT NULL`
- `email`: `VARCHAR(150) NOT NULL UNIQUE`
- `password`: `VARCHAR(255) NOT NULL` (Bcrypt Hash)
- `department`: `VARCHAR(100) NOT NULL`
- `semester`: `VARCHAR(20) NOT NULL`
- `batch`: `VARCHAR(20) NOT NULL`
- `enrollment_no`: `VARCHAR(50) NOT NULL UNIQUE`
- `phone`: `VARCHAR(25) NOT NULL`
- `created_at`: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`
- `updated_at`: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`

#### 2. `items`
- `id`: `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `iu_number`: `VARCHAR(20) NOT NULL UNIQUE`
- `user_id`: `INT UNSIGNED NOT NULL` (Foreign Key -> `users.id` ON DELETE CASCADE)
- `type`: `ENUM('LOST', 'FOUND') NOT NULL`
- `item_name`: `VARCHAR(150) NOT NULL`
- `category`: `VARCHAR(100) NOT NULL`
- `description`: `TEXT NOT NULL`
- `location`: `VARCHAR(200) NOT NULL`
- `event_date`: `DATE NOT NULL`
- `event_time`: `TIME NULL`
- `phone`: `VARCHAR(25) NOT NULL`
- `image_path`: `VARCHAR(255) NULL`
- `status`: `ENUM('OPEN', 'RESOLVED') NOT NULL DEFAULT 'OPEN'`
- `created_at`: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`
- `updated_at`: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`

#### 3. `messages`
- `id`: `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `item_id`: `INT UNSIGNED NOT NULL` (Foreign Key -> `items.id` ON DELETE CASCADE)
- `sender_id`: `INT UNSIGNED NOT NULL` (Foreign Key -> `users.id` ON DELETE CASCADE)
- `receiver_id`: `INT UNSIGNED NOT NULL` (Foreign Key -> `users.id` ON DELETE CASCADE)
- `message`: `TEXT NOT NULL`
- `is_read`: `TINYINT(1) NOT NULL DEFAULT 0`
- `created_at`: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`

#### 4. `notifications`
- `id`: `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `user_id`: `INT UNSIGNED NOT NULL` (Foreign Key -> `users.id` ON DELETE CASCADE)
- `title`: `VARCHAR(150) NOT NULL`
- `message`: `VARCHAR(255) NOT NULL`
- `related_item_id`: `INT UNSIGNED NULL` (Foreign Key -> `items.id` ON DELETE SET NULL)
- `is_read`: `TINYINT(1) NOT NULL DEFAULT 0`
- `created_at`: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`

---

## 📂 Directory Structure

```
WT_Project_2026/
├── config/
│   └── database.php           # PDO database connection configuration
├── css/
│   ├── mobile.css             # Responsive media queries (tablet & mobile viewports)
│   └── style.css              # Core design tokens, light/dark themes, and component styles
├── database/
│   └── lostlink.sql           # Complete schema definitions, keys, and indexes
├── includes/
│   ├── auth.php               # Session initialization and auth gatekeeper
│   ├── footer.php             # Global HTML footer and script injection
│   ├── functions.php          # Helper functions (XSS escaping, CSRF, uploads, dates)
│   ├── header.php             # HTML head, global navigation bar, theme toggle, flash alerts
│   ├── item_table.php         # Reusable tabular item listing component
│   └── sidebar.php            # Authenticated user sidebar navigation component
├── js/
│   └── app.js                 # Theme toggler, instant search filter, chat polling engine
├── uploads/
│   └── items/                 # Secure storage directory for uploaded item photos
├── account.php                # User profile viewing, editing, and password change
├── chat.php                   # Item-specific private chat interface
├── dashboard.php              # Authenticated user dashboard and quick actions
├── fetch_messages.php         # AJAX endpoint for asynchronous chat message polling
├── found.php                  # Directory page listing all found items
├── index.php                  # Public home page with recent reports and quick links
├── item.php                   # Comprehensive item details page
├── login.php                  # User login handler (Email or Enrollment No.)
├── logout.php                 # Session termination and cleanup
├── lost.php                   # Directory page listing all lost items
├── README.md                  # Complete project documentation
├── register.php               # Student account registration page
├── report_found.php           # Shorthand route to report a found item
├── report_item.php            # Core handler & form for reporting lost and found items
├── report_lost.php            # Shorthand route to report a lost item
├── send_message.php           # POST endpoint to send in-chat messages and trigger alerts
└── updates.php                # User notifications and activity log
```

---

## 💻 Installation & Local Setup

Follow these step-by-step instructions to set up and run LostLink on a local development machine:

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (or any local environment with **Apache**, **PHP 8.0+**, and **MySQL/MariaDB**).
- A web browser (Google Chrome, Firefox, Microsoft Edge, Safari).

### Step 1: Clone or Copy the Repository
Place the project directory into your web server's document root:
- **XAMPP on Windows**: `C:\xampp\htdocs\WT_Project_2026`
- **XAMPP on macOS**: `/Applications/XAMPP/htdocs/WT_Project_2026`
- **Linux Apache**: `/var/www/html/WT_Project_2026`

### Step 2: Start Apache and MySQL
1. Launch the **XAMPP Control Panel**.
2. Click **Start** for both **Apache** and **MySQL**.

### Step 3: Import the Database
1. Open your browser and navigate to **phpMyAdmin**:  
   `http://localhost/phpmyadmin/`
2. Click on the **Import** tab in the top navigation bar.
3. Click **Choose File** and select `database/lostlink.sql` from your project folder.
4. Click **Import** (or **Go**) at the bottom.
   > The script automatically creates the `lostlink` database, all 4 tables, foreign keys, and indexes.

### Step 4: Verify Database Connection Config
Open `config/database.php` and verify the credentials match your MySQL server settings (default XAMPP values are preconfigured):

```php
const DB_HOST = 'localhost';
const DB_NAME = 'lostlink';
const DB_USER = 'root';
const DB_PASS = '';
```

### Step 5: Check Uploads Directory Permissions
Ensure that the `uploads/items/` folder exists and is writable by the web server:
- On Windows: Enabled by default.
- On Linux/macOS:
  ```bash
  chmod -R 775 uploads/items
  ```

### Step 6: Launch the Application
Open your browser and navigate to:
```
http://localhost/WT_Project_2026/
```

---

## 📖 User Guide: How to Use LostLink

### 1. Creating an Account
1. From the homepage, click **Create Account** or **Register** in the top navigation.
2. Fill out your details:
   - **Username**
   - **University Email** (e.g. `student@university.edu`)
   - **Department** (e.g. `Computer Science`)
   - **Semester** (e.g. `6`)
   - **Batch** (e.g. `2022-2026`)
   - **Enrollment Number** (e.g. `IU224105001`)
   - **Phone Number**
   - **Password** (minimum 8 characters) and confirm it.
3. Click **Create Account**. You will be redirected to the login page.

### 2. Logging In
1. Enter either your **University Email** or **Enrollment Number**.
2. Enter your password and click **Log In**.
3. You will be greeted by your personal **Dashboard**.

### 3. Reporting a Lost Item
1. In the sidebar or dashboard, click **Report Lost Item**.
2. Fill in:
   - **Item Name** (e.g. `Titan Watch`)
   - **Category** (e.g. `Accessories`)
   - **Date & Time** when the item was lost
   - **Location** (e.g. `Library 2nd Floor, Reading Room`)
   - **Description** (e.g. `Silver chain with blue dial and small scratch on the clasp`)
   - **Contact Phone**
   - **Photograph** (Optional, up to 5 MB)
3. Click **Submit Report**. The system assigns a unique code (e.g., `L003`) and opens the item page.

### 4. Reporting a Found Item
1. In the sidebar or dashboard, click **Report Found Item**.
2. Fill in the item specifications, location where found, and upload an optional image.
3. Click **Submit Report**. The item will be published with an `F-series` IU number (e.g., `F007`).

### 5. Finding & Searching for an Item
1. Visit **Lost Items** or **Found Items** from the sidebar or top bar.
2. Type into the search field:
   - Search by keyword, such as `Calculator`, `Library`, `Electronics`, or `L002`.
   - The table filters instantaneously as you type.
3. Click on any item name or IU number to view its full details page.

### 6. Contacting the Reporter (Live Chat)
1. On the item details page, click **Start Conversation**.
2. You will enter the private chat room showing the reporter’s academic credentials.
3. Type your message and click **Send**.
4. Keep the chat window open—new replies from the other user will appear automatically every 5 seconds without refreshing the page!

### 7. Managing Profile & Notifications
- **Updates Tab**: View alerts about newly submitted reports and incoming messages.
- **Account Tab**: Update your active semester, batch, or phone number, or change your password anytime.
- **Dark/Light Mode**: Click the **Dark mode / Light mode** button in the header at any time to toggle themes.

---

## 📄 License

This project was developed for educational and university web technology coursework. You are free to use, modify, and extend it for academic and institutional lost-and-found systems.
