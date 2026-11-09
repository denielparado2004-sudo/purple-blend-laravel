# Purple Blend

Purple Blend is a Laravel ordering application for the customer storefront and store administration.

## Setup

1. Configure `.env` with a MySQL database, or keep the default SQLite connection for local development.
2. Run database migrations and load the 126-item catalog:

   ```sh
   php artisan migrate --seed
   ```

3. Set `PURPLE_BLEND_ADMIN_USERNAME` and `PURPLE_BLEND_ADMIN_PASSWORD` in `.env`, then create the administrator:

   ```sh
   php artisan db:seed --class=AdminSeeder
   ```

4. Run `php artisan storage:link` to serve administrator-uploaded product photos.
5. Start Laravel with `php artisan serve` and open the printed local URL. The storefront is at `/`; administration is at `/admin/login`.

The customer menu, shopping cart, registration/sign-in, password reset, checkout, order history, and profile pages are implemented in Laravel. Administrators can manage products, stock, orders, staff accounts, and payment status, and view customers and 30-day sales. Product images are in `public/images`; newly uploaded product photos use Laravel's public storage disk.

Configure a working Laravel mail transport in `.env` for password reset links. The catalog seeder imports menu item details only. It does not import customer accounts, password hashes, orders, or payment records from the legacy database; products with no corresponding source photo use the storefront placeholder.
