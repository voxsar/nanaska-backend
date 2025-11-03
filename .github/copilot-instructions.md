# Copilot Instructions - Laravel + Filament Backend

## Project Architecture

This is a Laravel 10 backend with **Filament v3 admin panel** integration. The stack includes:
- **Laravel Sanctum** for API authentication 
- **Spatie Laravel Backup** for automated backups
- **Vite** for asset compilation
- **Filament admin panel** accessible at `/admin`

## Key Patterns & Conventions

### Filament Admin Panel
- Admin panel configured in `app/Providers/Filament/AdminPanelProvider.php`
- Uses **Amber** as primary color scheme
- Auto-discovers resources in `app/Filament/Resources/` (create this directory when needed)
- Admin access restricted to `admin@nanaska.com` via `User::canAccessPanel()`
- User model implements `FilamentUser` contract in `app/Models/User.php`

### Authentication & Authorization  
- Single admin user seeded via `AdminUserSeeder` with email `admin@nanaska.com`
- Password: `admin@nanaska.com@123` (hardcoded in seeder)
- Sanctum middleware protects API routes (`/api/user`)
- Filament uses separate authentication middleware stack

### Development Workflow
```bash
# Install dependencies
composer install && npm install

# Database setup
php artisan migrate:fresh --seed

# Development servers
php artisan serve              # Laravel backend
npm run dev                   # Vite asset compilation

# Admin panel access
# Navigate to /admin with seeded credentials

# Filament commands
php artisan filament:upgrade   # Auto-runs after composer updates
php artisan make:filament-resource ModelName  # Create admin resources
```

### Project Structure
- **No custom controllers** yet - uses default Laravel structure
- **No Filament resources** created yet - admin panel is basic setup
- API routes minimal (`/api/user` endpoint only)
- Frontend assets in `resources/` compiled via Vite

### Backup Configuration
- Uses Spatie Laravel Backup package
- Configured in `config/backup.php` 
- Excludes `vendor/` and `node_modules/` from backups
- Backup name uses `APP_NAME` environment variable

## When Adding Features

### Creating Filament Resources
1. Create models first: `php artisan make:model ModelName -m`
2. Run migrations: `php artisan migrate`
3. Generate Filament resource: `php artisan make:filament-resource ModelName`
4. Resources auto-discovered in `app/Filament/Resources/`

### API Development
- Add routes to `routes/api.php`
- Use Sanctum middleware: `Route::middleware('auth:sanctum')`
- Create controllers in `app/Http/Controllers/`

### Database Changes
- Always create migrations: `php artisan make:migration`
- Update seeders if needed, especially for admin users
- Test with fresh migrations: `php artisan migrate:fresh --seed`

## Important Files
- `app/Models/User.php` - Admin authentication logic
- `app/Providers/Filament/AdminPanelProvider.php` - Admin panel configuration  
- `database/seeders/AdminUserSeeder.php` - Admin user creation
- `composer.json` - Note the Filament upgrade script in post-autoload-dump