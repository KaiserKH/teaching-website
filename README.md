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
