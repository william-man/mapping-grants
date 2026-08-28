# Mapping Grants

L&D project for mapping and querying nested JSON grant data.

## Tech Stack

- **Backend:** Laravel (PHP) + Vite
- **Database:** MariaDB
- **Frontend:** Vanilla JS (class-based, no framework)
- **Infrastructure:** Docker via Laravel Sail
- **DB Admin:** Adminer
- Data import: Python (ijson + pymysql) — see import/

## Getting Started

### Prerequisites

- Docker Desktop
- PHP + Composer (for initial setup only; app runs in containers afterward)
- Python 3 (for the data import pipeline, run outside Docker — see below)

### Setup

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env
php artisan key:generate

# Start containers (app, mariadb, adminer)
./script/server
```

### In a new terminal:

```bash
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

**Adminer login:**
- System: `MySQL/MariaDB`
- Server: `mariadb`
- Username: `sail`
- Password: `password`
- Database: `laravel`

## Data

### Getting the source files

Raw JSON source files are not committed (see .gitignore) — grant data is published as 360Giving-format JSON, one file per funder.

Download the source archive and unzip it so you end up with:
```
data/json_all/*.json
```

### Running the import

The import pipeline lives in `import/` and runs outside Docker, directly against the database via its host-forwarded port (127.0.0.1:3306, not the mariadb hostname — that only resolves inside the Docker network).

```bash
cd import
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
python3 main_import.py
```

## API

Base URL: `http://localhost/api/v1`

| Method | Route | Returns |
|---|---|---|
| GET | `/grants` | All grants, paginated (20 per page) |
| GET | `/grants/{id}` | One grant by its internal id |
| GET | `/recipient-postcodes` | All unique, non-null postcode values |
| GET | `/funding-organisations` | All unique funders (id and name) |

Route files live in `routes/api/`, one per resource, auto-loaded by `routes/api.php` — no manual registration needed for existing routes.
