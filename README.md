# Mapping Grants

L&D project for mapping and querying nested JSON grant data.

## Tech Stack

- **Backend:** Laravel (PHP) + Vite
- **Database:** MariaDB
- **Frontend:** Vanilla JS (class-based, no framework)
- **Infrastructure:** Docker via Laravel Sail
- **DB Admin:** Adminer

## Getting Started

### Prerequisites

- Docker Desktop
- PHP + Composer (for initial setup only; app runs in containers afterward)

### Setup

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env
php artisan key:generate

# Start containers (app, mariadb, adminer)
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Install JS dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

### Services

| Service   | URL                     | Notes                          |
|-----------|--------------------------|---------------------------------|
| App       | http://localhost         | Laravel app                    |
| Adminer   | http://localhost:8080    | DB browser (server: `mariadb`) |
| MariaDB   | localhost:3306           | Credentials in `.env`          |

## Data

Raw JSON source files live in `data/` (not committed — see `.gitignore`).

Data model approach: nested JSON is loaded as-is into a `json` column initially.
Frequently-queried fields will be promoted to dedicated columns or generated
columns as the schema stabilizes.

## Project Status

Week 1 — infrastructure and tooling setup. Data loading and mapping work in progress.