# PresenSI — Complete Installation Guide

> **"Si Pintar Urusan Presensi"**
> Read this entire guide before beginning installation.
> See also: `README.md` for feature documentation.

---

_PresenSI by burnblazter <hello@fael.my.id>_
_Fork of [o-present](https://github.com/josephines1/o-present) by Josephine_
_License: GPL-3.0 | [github.com/burnblazter](https://github.com/burnblazter)_

---

## Table of Contents

- [Part 1: Local Installation (Laragon)](#part-1-local-installation-laragon)
- [Part 2: Shared Hosting Deployment (cPanel)](#part-2-shared-hosting-deployment-cpanel)
  - [Option A — Set Document Root to `/public`](#option-a--set-document-root-to-public-recommended) _(Recommended)_
  - [Option B — Index Forwarder](#option-b--index-forwarder-alternative) _(Alternative)_
- [Additional Notes](#additional-notes)

---

## Part 1: Local Installation (Laragon)

### Prerequisites

| Requirement                                  | Details                          |
| -------------------------------------------- | -------------------------------- |
| [Laragon Full](https://laragon.org) (latest) | All-in-one local dev environment |
| PHP 8.1 or newer                             | Bundled with Laragon Full        |
| MySQL                                        | Bundled with Laragon             |

> **Note:** On some versions of Windows, Laragon may trigger UAC (User Account Control) dialogs during installation or configuration reloads. Always click **[Yes] / [Allow]** on every UAC prompt to ensure processes complete correctly.

---

### Step 1 — Extract Project Files

Extract `presensi.zip` into the following directory:

```
C:\laragon\www\
```

The resulting folder structure should look like this:

```
C:\laragon\www\presensi\
                        ├── app/
                        ├── public/
                        ├── database/
                        ├── .env.example
                        └── ...
```

---

### Step 2 — Start Laragon

1. Open Laragon. If a UAC dialog appears, click **[Yes]**.
2. Click **[Start All]** in the Laragon main window. Wait until both the Apache and MySQL indicators turn green.
3. Click **[Menu]** → **PHP** → **Extensions** and ensure the following extensions are enabled (checked):
   - `intl`
   - `mbstring`
   - `json`
   - `mysqlnd`
   - `curl` _(required for Groq API and Telegram)_
   - `zip` _(required for bulk download feature)_

---

### Step 3 — Configure Virtual Host

1. In the Laragon main window, click **[Menu]** → **Apache** → **sites-enabled** → `auto.presensi.test.conf`. The configuration file will open in your text editor.

2. Replace the entire contents of the file with the following:

   ```apache
   define ROOT "C:/laragon/www/presensi/public"
   define SITE "presensi.test"

   <VirtualHost *:80>
       DocumentRoot "${ROOT}"
       ServerName ${SITE}
       ServerAlias *.${SITE}
       <Directory "${ROOT}">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>

   <VirtualHost *:443>
       DocumentRoot "${ROOT}"
       ServerName ${SITE}
       ServerAlias *.${SITE}
       <Directory "${ROOT}">
           AllowOverride All
           Require all granted
       </Directory>
       SSLEngine on
       SSLCertificateFile    C:/laragon/etc/ssl/laragon.crt
       SSLCertificateKeyFile C:/laragon/etc/ssl/laragon.key
   </VirtualHost>
   ```

3. Save the file (`Ctrl+S`) and close the text editor.

4. Back in the Laragon main window, click **[Menu]** → **Apache** → **Reload**. If a UAC dialog appears, click **[Yes]**.

---

### Step 4 — Create the Database

> Laragon uses **HeidiSQL** as its built-in database client. HeidiSQL connects instantly without requiring browser-based authentication, making it faster than phpMyAdmin for local development.

1. Click **[Menu]** → **Tools** → **HeidiSQL**.
2. In the HeidiSQL Session Manager:
   - Click **[New]** in the bottom-left corner.
   - Leave all settings at their defaults (`Host: 127.0.0.1`, `User: root`, `Password: (empty)`).
   - Click **[Open]**.
3. In the left panel, right-click the connection name (usually _"Unnamed"_) → **Create New** → **Database**.
4. Fill in the fields:
   - **Name:** `presensi_db`
   - **Collation:** `utf8mb4_general_ci`
   - Click **[OK]**.
5. Click on `presensi_db` in the left panel to make it the active database.
6. Click **[File]** → **[Run SQL File...]**.
7. Navigate to the project folder:
   ```
   C:\laragon\www\presensi\database\
   ```
   Select `presensi_db.sql` and click **[Open]**.
8. Wait for the import to complete. No error messages means the import was successful.

---

### Step 5 — Configure `.env`

1. Navigate to `C:\laragon\www\presensi\`.
2. Copy `.env.example` and rename the copy to `.env`.
   > Ensure the filename is exactly `.env`, **not** `.env.txt`.
3. Open `.env` in your text editor and update the following values:

   **Required changes:**

   ```ini
   app.baseURL = 'http://presensi.test/'

   database.default.database = presensi_db
   database.default.username = root
   database.default.password =

   GROQ_API_KEYS = your_groq_api_key
   # Get your API key at: https://console.groq.com/keys

   telegram.botToken = 'your_telegram_bot_token'
   telegram.chatId   = 'your_telegram_chat_id'
   ```

   **Leave these at their defaults:**

   ```ini
   CI_ENVIRONMENT = production
   app.appTimezone = 'Asia/Makassar'
   GROQ_MODEL = moonshotai/kimi-k2-instruct
   database.default.hostname = localhost
   database.default.DBDriver = MySQLi
   database.default.port     = 3306
   ```

4. Save the file (`Ctrl+S`).

---

### Step 6 — Access the System

1. Open your browser (Chrome, Edge, or Firefox).
2. Navigate to: `http://presensi.test/`
3. PresenSI is ready to use.

**Troubleshooting:**

| Symptom                   | Check                                                       |
| ------------------------- | ----------------------------------------------------------- |
| "Not Found" or error page | Confirm Laragon was reloaded after editing the `.conf` file |
| App not loading           | Confirm the project folder is named `presensi` (lowercase)  |
| Config errors             | Confirm `.env` exists and is not named `.env.example`       |
| Permissions issues        | Confirm all UAC dialogs were answered with **[Yes]**        |

---

## Part 2: Shared Hosting Deployment (cPanel)

Two deployment options are available. Choose the one that matches your hosting plan. **Option A is recommended** if your hosting supports document root configuration.

---

## Option A — Set Document Root to `/public` _(Recommended)_

### Step 1 — Configure Domain Document Root

1. Log in to cPanel.
2. Open **[Domains]** or **[Addon Domains]**.
3. Locate your domain and click **[Manage]**.
4. Find the **Document Root** field and change its value from:
   ```
   public_html
   ```
   to:
   ```
   public_html/presensi/public
   ```
5. Click **[Save]** or **[Submit]**.

---

### Step 2 — Upload and Extract Files

1. In cPanel, open **[File Manager]**.
2. Navigate to `public_html/`.
3. Click **[Upload]** in the toolbar, select `presensi.zip` from your computer, and wait for the progress bar to reach 100%.
4. Back in File Manager (refresh if needed), right-click `presensi.zip` → **[Extract]**.
5. Confirm the **Extract To** path is `/public_html/` and click **[Extract Files]**.
6. Verify the resulting structure:
   ```
   public_html/presensi/
                        ├── app/
                        ├── public/
                        ├── database/
                        ├── .env.example
                        └── ...
   ```

---

### Step 3 — Create a Database and User

1. In cPanel, open **[MySQL Databases]**.
2. Under **Create New Database**, enter a database name (e.g., `presensi_db`) and click **[Create Database]**.
3. Scroll to **MySQL Users** → **Add New User**:
   - **Username:** e.g., `presensi_user`
   - **Password:** use a strong password
   - Click **[Create User]**
     > **Important:** Note down the username, password, and database name.
4. Scroll to **Add User To Database**, select the user and database you just created, and click **[Add]**.
5. On the privileges page, check **[ALL PRIVILEGES]** and click **[Make Changes]**.

---

### Step 4 — Import the Database

1. In cPanel, open **[phpMyAdmin]**.
2. Click on your database name in the left panel.
3. Click the **[Import]** tab in the top toolbar.
4. Click **[Choose File]** and select `presensi_db.sql` from your local machine.
5. Leave all other settings at their defaults.
6. Click **[Go]** and wait for the green success message.

---

### Step 5 — Configure `.env`

1. In File Manager, navigate to `public_html/presensi/`.
2. Right-click `.env.example` → **[Copy]**, set the destination to `/public_html/presensi/`, rename it to `.env`, and click **[Copy File(s)]**.
3. Right-click `.env` → **[Edit]** → confirm by clicking **[Edit]**.
4. Update the following values:

   **Required changes:**

   ```ini
   app.baseURL = 'https://yourdomain.com/'

   database.default.database = your_database_name
   database.default.username = your_database_user
   database.default.password = your_database_password
   ```

   **Optional (fill in if features are used):**

   ```ini
   GROQ_API_KEYS = your_groq_api_key
   telegram.botToken = 'your_telegram_bot_token'
   telegram.chatId   = 'your_telegram_chat_id'
   ```

   **Leave these at their defaults:**

   ```ini
   CI_ENVIRONMENT = production
   app.appTimezone = 'Asia/Makassar'
   GROQ_MODEL = moonshotai/kimi-k2-instruct
   database.default.hostname = localhost
   database.default.DBDriver = MySQLi
   database.default.port     = 3306
   ```

5. Click **[Save Changes]**.

---

### Step 6 — Set `writable/` Permissions

1. In File Manager, navigate to `public_html/presensi/`.
2. Right-click the `writable` folder → **[Change Permissions]**.
3. Set the permission value to `755`.
4. Check **[Recurse into subdirectories]** and select **[Apply to all]**.
5. Click **[Change Permissions]**.

---

### Step 7 — Access the System

1. Open your browser.
2. Navigate to: `https://yourdomain.com/`
3. PresenSI is ready to use.

**Troubleshooting:**

| Symptom           | Check                                                                 |
| ----------------- | --------------------------------------------------------------------- |
| HTTP 500 error    | Verify PHP version in cPanel → **[Select PHP Version]** → minimum 8.1 |
| Missing features  | Enable required extensions: `intl`, `mbstring`, `json`, `mysqlnd`     |
| File write errors | Confirm `writable/` folder permissions are set to `755`               |
| Database errors   | Double-check all values in `.env` for typos                           |

---

## Option B — Index Forwarder _(Alternative)_

Use this option if your hosting plan does not allow you to modify the domain's Document Root setting.

### Steps 1–4 — Upload, Database, and Import

Follow **Steps 2, 3, and 4** from [Option A](#option-a--set-document-root-to-public-recommended).

---

### Step 5 — Create an Index Forwarder

1. In File Manager, navigate to `public_html/`.
2. If an `index.php` file already exists, right-click it → **[Rename]** → rename to `index.php.bak`.
3. Click **[+ File]** in the toolbar, name it `index.php`, and click **[Create New File]**.
4. Right-click the new `index.php` → **[Edit]** → confirm by clicking **[Edit]**.
5. Paste the following code:

   ```php
   <?php
   define('FCPATH', __DIR__ . '/');
   chdir(__DIR__ . '/presensi/public');
   require __DIR__ . '/presensi/public/index.php';
   ```

6. Click **[Save Changes]**.

---

### Step 6 — Edit `.htaccess`

1. Still in `public_html/`.
2. If no `.htaccess` file exists, click **[+ File]** → name it `.htaccess` → **[Create New File]**.
3. Right-click `.htaccess` → **[Edit]** → confirm by clicking **[Edit]**.
4. Append the following to the **bottom** of the file (after any existing content):

   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ presensi/public/$1 [L]
   ```

5. Click **[Save Changes]**.

---

### Steps 7–8 — Configure `.env`, Permissions, and Access

Follow **Steps 5, 6, and 7** from [Option A](#option-a--set-document-root-to-public-recommended).

---

## Additional Notes

### Required PHP Extensions

The following PHP extensions must be active on your server or local environment:

| Extension  | Purpose                           |
| ---------- | --------------------------------- |
| `intl`     | Internationalization support      |
| `mbstring` | Multibyte string handling         |
| `json`     | JSON encoding/decoding            |
| `mysqlnd`  | MySQL native driver               |
| `curl`     | Groq API and Telegram integration |
| `zip`      | Bulk download feature             |

On shared hosting, enable these via cPanel → **[Select PHP Version]** → **[Extensions]** tab.

```

```
