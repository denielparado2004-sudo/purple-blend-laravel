# Purple Blend

Purple Blend is a Laravel ordering application with a customer storefront and store administration.

## Local setup

1. Copy `.env.example` to `.env` and configure a PostgreSQL database or a local SQLite database.
2. Install dependencies, generate the application key, build frontend assets, and run migrations:

   ```sh
   composer install
   php artisan key:generate
   npm ci
   npm run build
   php artisan migrate --seed
   ```

3. Configure `PURPLE_BLEND_ADMIN_USERNAME`, `PURPLE_BLEND_ADMIN_EMAIL`, and `PURPLE_BLEND_ADMIN_PASSWORD`, then create the administrator:

   ```sh
   php artisan db:seed --class=AdminSeeder
   ```

4. Configure a real SMTP transport before using password reset emails.
5. Run `php artisan storage:link` to serve administrator-uploaded product photos.
6. Start Laravel with `php artisan serve`. The storefront is at `/`; administration is at `/admin/login`.

The catalog seeder imports menu item details only. It does not import customer accounts, password hashes, orders, or payment records from the legacy database. Product images are in `public/Images`; newly uploaded product photos use Laravel's public storage disk.

## Render and Supabase

Use the Dockerfile to build the application on Render. Set `APP_ENV=production`, `APP_DEBUG=false`, a generated `APP_KEY`, and `APP_URL` to the public HTTPS service URL in the Render environment. Configure the Supabase PostgreSQL connection values there, including `DB_SSLMODE=require`; do not put production credentials in `.env.example` or the Docker build context.

Set `MAIL_MAILER=smtp` and provide the SMTP host, port, scheme, username, password, and sender address through Render environment variables. Do not use the log mailer in production because password reset links contain bearer tokens.

The Supabase production database has already been migrated and populated with the existing application records. Do not run database reset commands or re-import the legacy data during deployment. The existing administrator account was also imported, so do not run the AdminSeeder against the production database.

Render should use the configured production environment variables to connect to Supabase. Database migrations should only be run when there are new, reviewed migrations to apply.
