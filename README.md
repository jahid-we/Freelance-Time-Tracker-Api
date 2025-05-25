# Freelance Time Tracker API

This is a RESTful API built with Laravel that allows freelancers to track and manage their working hours across multiple clients and projects.

## 🚀 Features

- **Authentication:**
  - Register, login, and logout using **Sanctum**.
  
- **Clients Management:**
  - Create, update, delete, and list clients.

- **Projects Management:**
  - Create, update, delete, and list projects, including filtering by client.

- **Notifications**:
  - Sends a queued email notification when a user exceeds 8 daily work hours.

---

## 🛠️ Tech Stack

- **Laravel 12**
- **Sanctum** for authentication
- **Eloquent ORM** for database interaction
- **Laravel Queues** (with `database` driver)
- **Notification System** for email alerts

---

## 🧑‍💻 API Endpoints

### 🔐 Authentication Routes

- `POST /api/register` — Register a new freelancer.
- `POST /api/login` — Login a freelancer.
- `POST /api/logout` — Logout a freelancer (requires `auth:sanctum`).

---

### 👤 Client Routes (require `auth:sanctum`)

- `POST /api/create-client` — Create a new client.
- `GET /api/get-clients` — List all clients.
- `GET /api/get-client/{id}` — View a specific client.
- `POST /api/update-client/{id}` — Update a client.
- `DELETE /api/delete-client/{id}` — Delete a client.
- `DELETE /api/delete-all-clients` — Delete all clients.

---

### 📁 Project Routes (require `auth:sanctum`)

- `POST /api/create-project` — Create a new project.
- `GET /api/get-all-projects` — List all projects.
- `GET /api/get-project/{id}` — View a specific project.
- `POST /api/update-project/{id}` — Update a project.
- `DELETE /api/delete-project/{id}` — Delete a project.
- `DELETE /api/delete-all-projects` — Delete all projects.
- `GET /api/get-projects-by-client/{clientId}` — Get all projects by client ID.

---

### ⏱️ Time Log Routes (require `auth:sanctum`)

- `POST /api/start-timelog/{projectId}` — Start a new time log for a project.
- `POST /api/end-timelog/{projectId}` — End the current active time log.
- `POST /api/manual-entry/{projectId}` — Create a manual time log.
- `GET /api/get-timelogs` — List all time logs.
- `GET /api/get-timelog/{id}` — View a specific time log.
- `POST /api/update-timelog/{id}` — Update a time log.
- `DELETE /api/delete-timelog/{id}` — Delete a time log.
- `DELETE /api/delete-all-timelogs` — Delete all time logs.

### 📊 Reports

- `GET /api/report?date=YYYY-MM-DD` — Get all time logs for a specific date.
- `GET /api/report?client_id=id&date=YYYY-MM-DD` — Get time logs for a specific client on a specific date.
- `GET /api/report?project_id=id&date=YYYY-MM-DD` — Get time logs for a specific project on a specific date.
- `GET /api/report?from=YYYY-MM-DD&to=YYYY-MM-DD` — Get time logs within a specific date range.
- `GET /api/report?client_id=id&from=YYYY-MM-DD&to=YYYY-MM-DD` — Get time logs for a specific client within a date range.
- `GET /api/report?project_id=id&from=YYYY-MM-DD&to=YYYY-MM-DD` — Get time logs for a specific project within a date range.

> ⏰ Note: If a user logs more than 8 hours in a single day (via start, manual entry, or update), an email notification is automatically queued and sent.


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

### Time Logs
- `id` (primary key)
- `user_id` (foreign key to `users` table)
- `project_id` (foreign key to `projects` table)
- `start_time` (timestamp, nullable)
- `end_time` (timestamp, nullable)
- `description` (text, nullable)
- `hours` (decimal, 8, 2)
- `tags` (enum: billable, non-billable, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

---

## 🛠️ Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/jahid-we/Freelance-Time-Tracker-Api.git
cd freelance-time-tracker


# 2. Install Dependencies
composer install

# 3. Create .env File
cp .env.example .env

# 4. Generate App Key
php artisan key:generate

# 5. Migrate and Seed Database
php artisan migrate --seed

# 6. Start Queue Worker
php artisan queue:work

# 7. Start Development Server
php artisan serve

```

### 📬 Postman Collection

> You can access and test the API endpoints using the following Postman collection:

👉 [Click here to open the Postman Collection](https://www.postman.com/jahidhasan37/workspace/laravel-jahid/collection/32325662-ab500bb0-493d-4985-92a9-b706217905b7?action=share&creator=32325662&active-environment=32325662-1ca1441b-aa03-45ce-9924-a4616d2eb092)

