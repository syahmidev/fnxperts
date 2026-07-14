# FNXPERTS — Mini CRM Admin Panel

A small CRM admin panel for managing **companies** and their **employees**, built as a
Laravel technical assessment. Administrators sign in to a protected dashboard and perform
full CRUD on companies (with logo uploads) and employees, backed by Eloquent
relationships, Form Request validation, Action classes, and a public read-only JSON API.

---

## Features

- Administrator authentication (login / logout) via **Laravel Fortify**
- **Public registration disabled** (both `GET` and `POST /register` are unregistered, not just hidden)
- Dashboard with company/employee totals and recent records
- **Companies CRUD** with logo upload (min. 100 × 100 px), stored on the public disk
- **Employees CRUD** with a company relationship
- Eloquent relationships: a company *has many* employees; an employee *belongs to* a company
- **Soft deletes** on companies and employees (records are recoverable, not permanently removed)
- **Trash management:** dedicated trashed listings with **restore** and **permanent delete**; restoring a company also restores the employees deleted alongside it
- Validation via dedicated **Form Request** classes
- State-changing logic isolated in **Action classes** (logo storage / replacement / cleanup in transactions)
- **Laravel API Resources** with an `employee_count` attribute
- Pagination at **10 records per page** for both listings
- Feature test suite (**Pest**) covering auth, CRUD, validation, logo lifecycle, cascade delete, pagination, and the API
- Database factories and seeders (seeded admin + 15 companies + 40 employees)

---

## Technology Stack

| Layer | Technology |
|---|---|
| Framework | Laravel **13.20** |
| Language | PHP **8.3+** (developed on 8.5) |
| Auth | Laravel Fortify |
| Frontend | Livewire 4, Blade, Flux UI, Tailwind CSS 4, Vite |
| Asset bundler | Bun |
| Database | PostgreSQL |
| Testing | Pest 4 |

> The Companies and Employees modules use **standard Laravel resource controllers + Blade
> views** as required by the assessment. The Livewire starter kit provides only the
> authentication and application shell.

---

## Requirements

- PHP **8.3+** (with `gd`, `pdo_pgsql` extensions)
- Composer 2
- Node.js 20+ and **Bun** (or npm)
- PostgreSQL 13+

---

## Installation

```bash
git clone <repository-url>
cd fnxperts

composer install
cp .env.example .env
php artisan key:generate
```

### Environment configuration

Configure the database connection in `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fnxperts
DB_USERNAME=postgres
DB_PASSWORD=
```

Create the database (once):

```bash
createdb -h 127.0.0.1 -U postgres fnxperts
# or: psql -h 127.0.0.1 -U postgres -c "CREATE DATABASE fnxperts;"
```

### Database setup and migration

```bash
php artisan migrate:fresh --seed
```

This runs all migrations and seeds:

- the administrator account (see credentials below),
- 15 companies,
- 40 employees distributed across those companies.

### Storage link

Uploaded company logos are stored in `storage/app/public/companies`. Expose them publicly with:

```bash
php artisan storage:link
```

### Build front-end assets

```bash
bun install
bun run build
```

---

## Running the application

Using the built-in server:

```bash
php artisan serve
```

Then visit <http://127.0.0.1:8000>.

For active front-end development (hot reload):

```bash
bun run dev
```

> This project was developed with **Laravel Herd**, which serves it at
> `https://fnxperts.test`. Either approach works — set `APP_URL` accordingly.

---

## Administrator credentials

```text
Email:    admin@admin.com
Password: password
```

> These credentials are for assessment/demo purposes only.

---

## API documentation

### `GET /api/companies/{company}`

Returns a single company together with its employees and an `employee_count` attribute.
The endpoint is **public and read-only** (no authentication), as permitted by the
assessment for this demo API.

**Path parameters**

| Parameter | Type | Description |
|---|---|---|
| `company` | integer | The company ID (route-model bound) |

**Responses**

| Status | Meaning |
|---|---|
| `200 OK` | Company found; body contains the company, its employees, and `employee_count` |
| `404 Not Found` | No company with the given ID |

**Example request**

```bash
curl https://fnxperts.test/api/companies/1
# or, with php artisan serve:
curl http://127.0.0.1:8000/api/companies/1
```

**Example response**

```json
{
    "data": {
        "id": 1,
        "name": "Example Company",
        "email": "company@example.com",
        "website": "https://example.com",
        "logo_url": "https://fnxperts.test/storage/companies/example.png",
        "employee_count": 2,
        "employees": [
            {
                "id": 1,
                "first_name": "John",
                "last_name": "Doe",
                "full_name": "John Doe",
                "email": "john@example.com",
                "phone": "+60123456789"
            },
            {
                "id": 2,
                "first_name": "Jane",
                "last_name": "Smith",
                "full_name": "Jane Smith",
                "email": "jane@example.com",
                "phone": "+60198765432"
            }
        ]
    }
}
```

`logo_url` is `null` when the company has no logo.

### API testing

Test with **Postman**, **Insomnia**, **Bruno**, or **cURL** using the endpoint above.
The endpoint is also covered by automated feature tests (`tests/Feature/CompanyApiTest.php`).

---

## Testing

```bash
php artisan test
```

The suite covers authentication, registration-disabled, company & employee CRUD,
validation rules, the logo lifecycle (upload / replace / delete), cascade deletion,
pagination counts, and the API contract.

## Code formatting

```bash
./vendor/bin/pint
```

---

## Screenshots

Located in [`docs/screenshots/`](docs/screenshots):

| View | File |
|---|---|
| Login | `login.png` |
| Dashboard | `dashboard.png` |
| Companies list | `companies-index.png` |
| Companies pagination | `pagination.png` |
| Trashed companies | `companies-trashed.png` |
| Trashed employees | `employees-trashed.png` |
| Company create form | `companies-form.png` |
| Company details | `company-details.png` |
| Employees list | `employees-index.png` |
| Employee create form | `employees-form.png` |
| API response | `api-response.png` |

---

## Design decisions

- **Soft deletes:** companies and employees use `SoftDeletes`, so deletes are recoverable.
  Soft-deleted records are automatically excluded from listings, the dashboard, and the API
  (route-model binding returns `404` for a soft-deleted company).
- **Cascade delete:** deleting a company also deletes its employees. Because a soft delete is
  an `UPDATE` (not a `DELETE`), the database foreign-key cascade does not fire, so the
  `DeleteCompany` action soft-deletes the employees explicitly inside a transaction. The
  foreign key still uses `cascadeOnDelete()`, which applies on a permanent `forceDelete()`.
- **Logo storage:** company logos are stored on Laravel's `public` disk under
  `storage/app/public/companies`; only the relative path is persisted, and the public URL
  is generated with `Storage::disk('public')->url()`.
- **Logo lifecycle in Actions:** create/update/delete logic lives in `App\Actions\Companies`
  so the controller stays thin. A new logo is stored before the DB write and rolled back on
  failure; the previous logo is removed only after a successful update. On a **soft delete**
  the logo file is **retained** (so the record stays restorable); it is purged only when the
  company is permanently deleted (a `forceDeleted` model hook). Missing files are ignored safely.
- **Public, read-only API:** the company API endpoint requires no authentication, per the
  assessment's allowance for this demo.
- **Registration disabled:** the Fortify `registration` feature is removed, so the register
  routes do not exist at all.
- **Pagination:** both listings show **10 records per page**.

---

## Project structure (key files)

```text
app/
├── Actions/
│   ├── Companies/{CreateCompany,UpdateCompany,DeleteCompany,RestoreCompany,ForceDeleteCompany}.php
│   └── Employees/{CreateEmployee,UpdateEmployee,DeleteEmployee}.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/CompanyController.php
│   │   ├── CompanyController.php
│   │   ├── DashboardController.php
│   │   └── EmployeeController.php
│   ├── Requests/{Store,Update}{Company,Employee}Request.php
│   └── Resources/{CompanyResource,EmployeeResource}.php
├── Models/{Company,Employee}.php
database/
├── factories/{CompanyFactory,EmployeeFactory}.php
├── migrations/…create_companies_table.php, …create_employees_table.php
└── seeders/{AdminUserSeeder,CompanySeeder,EmployeeSeeder,DatabaseSeeder}.php
resources/views/{companies,employees}/…
routes/{web.php,api.php}
tests/Feature/{CompanyTest,EmployeeTest,CompanyApiTest,RegistrationDisabledTest}.php
```
