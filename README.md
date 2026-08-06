# Garment Payroll System — PHP + MySQL Edition

This is a rebuild of the original localStorage-based app as a real PHP + MySQL
web app, with a proper server-side login system.

## What changed from the old version

- **Login is real now.** Passwords are hashed with bcrypt (`password_hash` /
  `password_verify`), sessions are server-side, all queries use prepared
  statements, and every form is protected against CSRF.
- **Data lives in MySQL**, not the browser. Workers and work entries are
  shared across every device/browser that logs in — that's the "real-time DB
  handling" you asked for. Open the app on your phone and your laptop and
  you'll see the same data on both.
- **One shared stylesheet** (`css/style.css`) instead of the same ~400-line
  `<style>` block copy-pasted into every page. Change a color once, it
  updates everywhere — this matters for whoever supports this after you.
- **Salary totals are computed in SQL** (`SUM(quantity) * rate`, grouped by
  worker) instead of being calculated in JavaScript from data pulled out of
  localStorage.

## File structure

```
config.php              - database connection settings (edit this first)
login.php                - login page (replaces old index.html)
logout.php
home.php                  - dashboard with live stats from the DB
workers.php               - add / edit / delete workers
work-entry.php             - submit a daily work entry
manage-entries.php          - edit / delete existing entries
salary-report.php            - weekly salary report
includes/auth.php             - session + login guard + CSRF helpers
includes/header.php            - shared nav bar
includes/footer.php             - shared footer
css/style.css                    - all styling, one file
images/                            - same images as before
sql/schema.sql                      - run this once to create the database
```

## Default login

- **Email:** `sreedhar@gmail.com`
- **Password:** `kpn1234`

Change this after your first login — either run a quick UPDATE on the
`admins` table with a new bcrypt hash (`php -r "echo password_hash('newpass',
PASSWORD_DEFAULT);"`), or add a "change password" page later; that's a small
addition on top of what's here.

## Setup — Option A: XAMPP on your PC (good if you want a local copy first)

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and
   **MySQL** from the control panel.
2. Copy this whole folder into `htdocs` (e.g. `C:\xampp\htdocs\garment-payroll`).
3. Open `http://localhost/phpmyadmin`, click **Import**, choose
   `sql/schema.sql`, and click Go. This creates the `garment_payroll`
   database, its tables, and the default admin login.
4. `config.php` already has the right defaults for XAMPP (`root` user, no
   password) — no changes needed.
5. Visit `http://localhost/garment-payroll/login.php`.

## Setup — Option B: Free mobile-friendly hosting (no terminal needed)

Since you're often working from your phone, **InfinityFree** is a good fit —
everything is done through a browser: a file manager for uploads and
phpMyAdmin for the database, no SSH or command line required.

1. Sign up at infinityfree.com and create a new hosting account (you'll get a
   free subdomain like `garmentpay.rf.gd`, or you can point your own domain
   at it).
2. In the control panel, open **phpMyAdmin**, create a database, and use its
   **Import** tab to upload `sql/schema.sql`. Note the database name, host,
   username, and password it gives you.
3. Open **File Manager** (or use an FTP app like FTPManager on your phone),
   go to `htdocs`, and upload every file/folder from this project.
4. Edit `config.php` in the File Manager's built-in text editor and put in
   the real `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` from step 2.
5. Visit your site's `login.php`.

Other options that work the same way: **000webhost**, or paid hosts like
**Hostinger** if you want more reliability — the steps are the same, just a
different control panel.

> Heads up: GitHub Pages and Netlify (what the old localStorage version used)
> **cannot** run PHP or MySQL — they only serve static files. This version
> needs a real PHP host.

## Security notes

- Never commit real database credentials to a public GitHub repo — if this
  repo is public, keep `config.php` out of it (add it to `.gitignore`) and
  only fill in the real values on the server.
- The default password above is meant to be changed immediately after setup.
