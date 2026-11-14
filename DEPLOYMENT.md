# Deployment Guide – MK Securex

## 1. Server requirements
- PHP **8.2+** with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` (for WebP)
- MySQL 8 (or MariaDB 10.4+)
- Composer 2
- Apache (mod_rewrite) or Nginx

## 2. Get the code
```bash
git clone https://github.com/inforajeshdigital/mksecurex.git
cd mksecurex
composer install --no-dev --optimize-autoloader
```
> `vendor/` is not committed. If your hosting has no SSH/Composer, run `composer install --no-dev` locally and upload the `vendor` folder.

## 3. Environment (.env)
```bash
cp .env.example .env
php artisan key:generate
```
Edit `.env` privately on the server (never commit it):

| Key | Value |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://yourdomain.com` (HTTPS, no trailing slash) |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | your MySQL database |
| `MAIL_*` | SMTP details for enquiry emails (host, port 587/465, username, password, from address) |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | first admin login (used only by the seeder) |

## 4. Database
```bash
php artisan migrate --force
php artisan db:seed --force      # first install only – creates settings, pages, services, industries, menus, admin user
```
Re-running the seeder is safe: it does not overwrite pages/sections/settings you have edited.

## 5. Folders & permissions
```bash
chmod -R 775 storage bootstrap/cache public/uploads
```
Uploaded images are stored in `public/uploads` (no `storage:link` needed — works on shared hosting).

## 6. Web server
**Recommended:** point the domain’s document root to the `public/` folder.

**Shared hosting (document root = project folder):** the included root `.htaccess` forwards all requests to `public/` and blocks access to `.env`, `vendor`, `storage`, etc. Old `/public/...` URLs are 301-redirected to clean URLs.

**Nginx example**
```nginx
root /var/www/mksecurex/public;
index index.php;
location / { try_files $uri $uri/ /index.php?$query_string; }
location ~ \.php$ { include fastcgi_params; fastcgi_pass unix:/run/php/php8.2-fpm.sock; fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; }
location ~ /\.(?!well-known) { deny all; }
gzip on; gzip_types text/css application/javascript image/svg+xml application/xml;
```

## 7. Optimise (after every deploy)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Clear caches: `php artisan optimize:clear` — or Admin → Backup & Maintenance → Clear All Cache.

## 8. Updating the live site
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 9. After going live (admin checklist)
1. Log in at `/admin/login`, change the admin password (Admin → Change Password).
2. Contact Information: real address, phone, email, hours, Google Maps embed URL.
3. WhatsApp & Call: verified WhatsApp number (country code, digits only) and call number.
4. Website Settings: admin notification email; replace logo/favicon if needed (transparent PNG recommended).
5. Upload real photos for hero slides, services and industries (Media Library) with alt text.
6. Statistics section (Homepage → Sections) is disabled — enable only with verified figures.
7. Testimonials are placeholders and inactive — add genuine client feedback.
8. Review the Privacy Policy with your legal advisor.
9. Check `/robots.txt` and submit `/sitemap.xml` in Google Search Console.

## 10. Backup & restore
- **Backup:** Admin → Backup & Maintenance → *Download Backup (.sql)*, or `mysqldump -u USER -p mksecurex > backup.sql`. Also back up `public/uploads/` and `.env`.
- **Restore:** import the `.sql` file in phpMyAdmin (or `mysql -u USER -p mksecurex < backup.sql`) and copy back `public/uploads/`.

## 11. Security notes
- Keep `APP_DEBUG=false` in production; never share `APP_KEY`, DB or SMTP passwords.
- Admin login is rate-limited (6 attempts/min); enquiry form is rate-limited (5/min per IP) with a honeypot.
- Uploads are restricted to images (JPG, PNG, WebP, GIF, ICO) up to 4 MB.
- Give each staff member their own account with the minimum role needed (Admin, Editor, Sales).
