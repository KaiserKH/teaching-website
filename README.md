# Teaching Website (Private Tuition)

Quick local setup:

1. Create a copy of `config.php` and set DB credentials:

   - Edit `config.php` values for DB host/dbname/user/password.

2. Import the database schema:

   ```sql
   -- from terminal
   mysql -u root -p < schema.sql
   ```

3. Place the project in your hosting `public` folder or set document root to `/workspaces/teaching-website/public`.
4. Ensure `uploads/` is writable by PHP.
5. Open the site at `/home`.

Seed admin: `admin@demo.com` password `DemoPass123` (hashed in SQL seed).
3. Environment configuration

   - Copy `.env.example` to `.env` at the project root and update values:

     ```bash
     cp .env.example .env
     # edit .env and set DB_USER, DB_PASS, BASE_URL, etc.
     ```

4. Place the project in your hosting `public` folder or set document root to `/workspaces/teaching-website/public`.

5. Ensure `uploads/` is writable by PHP (set permissions):

   ```bash
   mkdir -p uploads
   chmod 775 uploads
   ```

6. Run locally (built-in PHP server):

   ```bash
   php -S localhost:8000 -t public
   # open http://localhost:8000/home
   ```

7. Deploy to shared hosting (cPanel-compatible)

   - Compress and upload project files to the server (use File Manager or FTP/SFTP).
   - In cPanel, create a new MySQL database and user, and assign the user to the database.
   - Import `schema.sql` using phpMyAdmin into the created database.
   - In the project root, create `.env` with the DB credentials and `BASE_URL`.
   - Set the document root to the `public` directory (or move public contents into `public_html`).
   - Ensure `uploads/` is writable by the webserver.

Testing notes

- Seed admin: `admin@demo.com` with password `DemoPass123` (seeded in schema.sql). Change the password after first login.
- Demo student: `student@demo.com` with password `DemoPass123` (approved status in seed data).

Quick tests after setup

- Visit `/home` and `/courses` to ensure pages render.
- Submit the admission form at `/admission` and approve it in `/admin/admissions` (admin login required).
- Login as student at `/login` to access `/student/dashboard`.
- Upload a note in `/admin/uploads` and confirm it appears in `/student/notes` for that subject.

Security & production checklist

- Do NOT commit `.env` with real credentials to public repositories. Use `.env.example` instead.
- Replace the demo `send_mail()` with PHPMailer and SMTP in production. See next steps in this README.
- Use HTTPS and set appropriate file permissions.
- Backup your database regularly.

Composer & Payment integration

- This project can use PHPMailer and Razorpay SDK via Composer. On your development machine or server, run:

```bash
composer install
```

- After installing, the app will use PHPMailer for SMTP email sending (configured in `.env`).

- Razorpay setup:
   - Add `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET` to your `.env`.
   - The student fees page shows a "Pay Online (Razorpay)" button which creates an order and opens Razorpay Checkout.
   - The server verifies the payment signature and marks the fee as paid.

- UPI payments:
   - Students can also upload a UPI screenshot and enter the transaction ID. These uploads are stored in `uploads/payments/` and the payment is marked `pending` for admin verification.

Files added:

- `composer.json` — declares `phpmailer/phpmailer` and `razorpay/razorpay`.
- `lib/mailer.php` — wrapper that uses PHPMailer (when installed) or falls back to `mail()`.
- `public/api/razorpay_create_order.php` — server endpoint to create Razorpay orders.
- `public/api/razorpay_verify.php` — server endpoint to verify Razorpay payments and mark fees paid.
- `public/api/upload_upi_payment.php` — endpoint for students to upload UPI screenshots and txn IDs.

After deploying to hosting, run `composer install` in the project root (if your host supports it) or vendor the required libraries.
