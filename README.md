# Freelance Time Tracker API

This is a RESTful API built with Laravel that allows freelancers to track and manage their working hours across multiple clients and projects.

## 🚀 Features

- **Authentication:**
  - Register, login, and logout using **Sanctum**.
  
- **Clients Management:**
  - Create, update, delete, and list clients.

- **Projects Management:**
  - Create, update, delete, and list projects, including filtering by client.

---

## 🛠️ Tech Stack

- **Laravel 12**
- **Sanctum** for authentication
- **Eloquent ORM** for database interaction

---

## 🧑‍💻 API Endpoints

### Authentication Routes

- `POST /api/register` — Register a new freelancer.
- `POST /api/login` — Login a freelancer.
- `POST /api/logout` — Logout a freelancer (protected by `auth:sanctum`).

### Client Routes

- `POST /api/create-client` — Create a new client (protected by `auth:sanctum`).
- `GET /api/get-clients` — Get all clients (protected by `auth:sanctum`).
- `GET /api/get-client/{id}` — Get a single client by ID (protected by `auth:sanctum`).
- `POST /api/update-client/{id}` — Update a client by ID (protected by `auth:sanctum`).
- `GET /api/delete-client/{id}` — Delete a client by ID (protected by `auth:sanctum`).
- `GET /api/delete-all-clients` — Delete all clients (protected by `auth:sanctum`).

### Project Routes

- `POST /api/create-project` — Create a new project (protected by `auth:sanctum`).
- `GET /api/get-all-projects` — Get all projects (protected by `auth:sanctum`).
- `GET /api/get-project/{id}` — Get a single project by ID (protected by `auth:sanctum`).
- `POST /api/update-project/{id}` — Update a project by ID (protected by `auth:sanctum`).
- `GET /api/delete-project/{id}` — Delete a project by ID (protected by `auth:sanctum`).
- `GET /api/delete-all-projects` — Delete all projects (protected by `auth:sanctum`).
- `GET /api/get-projects-by-client/{clientId}` — Get all projects by a specific client (protected by `auth:sanctum`).

---

## 🧱 Database Structure

### Users (Freelancers)
- `id` (primary key)
- `name` (string)
- `email` (string, unique)
- `password` (hashed string)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Clients
- `id` (primary key)
- `user_id` (foreign key to `users` table)
- `name` (string)
- `email` (string)
- `contact_person` (string)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Projects
- `id` (primary key)
- `client_id` (foreign key to `clients` table)
- `title` (string)
- `description` (text)
- `status` (string: active/completed)
- `deadline` (date)
- `created_at` (timestamp)
- `updated_at` (timestamp)

---

## 🛠️ Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/jahid-we/Freelance-Time-Tracker-Api.git
cd freelance-time-tracker


**2 Install Dependencies**
composer install

**3 Create .env file**
cp .env.example .env

**4 Generate App Key**
php artisan key:generate

**5 Migrate Database**
php artisan migrate --seed

**6 Start Development Server**
php artisan serve

