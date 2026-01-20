# Finalyze Backend

## Setup
1. Install dependencies:
   ```sh
   composer install
   ```
2. Configure Environment:
   - Copy `.env.example` to `.env`
   - Set database credentials (DB_CONNECTION=pgsql, DB_HOST, etc.)
   - Set Mercado Pago credentials (MP_ACCESS_TOKEN)

## Migrations
Run migrations to setup database schema:
```sh
php artisan migrate
```

## API Endpoints (Planned)
- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/plans`
- `POST /api/checkout/preference`
- `POST /api/financial/transactions`
