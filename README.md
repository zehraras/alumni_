# Alumni System

A modern Alumni Management and Tracking System designed to manage graduate information, facilitate networking, and keep alumni connected with their institution.

## System Overview & Features
- **Alumni Management:** Directory and profiles for university graduates.
- **Networking & Communications:** Connection channels and event management.
- **Data Persistence:** Relational database storage with full integrity.

## Tech Stack
- **Back-end:** PHP (Laravel)
- **Database:** PostgreSQL
- **AI Assistant:** Antigravity

## How to Run the Project

This system strictly adheres to single-command containerized deployment via Docker.

### Prerequisites
- Docker & Docker Desktop installed.

### Quick Start
Run the following command in the root directory to build and start all required services:

```bash
docker compose up -d --build
```
## MVC Architecture
This application follows the Model-View-Controller (MVC) architectural pattern to separate concerns:

- **`models/` (Model):** Handles data logic and data representation. E.g., `UserModel` manages reading and writing user data (currently from `data/users.json`).
- **`controllers/` (Controller):** Handles incoming requests, processes user input, interacts with the Model, and returns the appropriate response (JSON or HTML). E.g., `UserController` for web views, `ApiUserController` for API endpoints.
- **`views/` (View):** Contains the HTML templates and UI components to present data to the user.
- **`index.php` (Router):** The entry point of the application that routes incoming HTTP requests to the appropriate controllers.

## Data Design Documentation
For detailed data structure, entity definitions, planned SQL schemas, and ER diagram, see:
👉 **[ddd.md](ddd.md)**

## Database Migrations
Migrations are located in `migrations/` and manage the database schema for PostgreSQL:
- `migrations/001_create_users_table.sql`: Schema for `users` table.
- `migrations/002_create_announcements_table.sql`: Schema for `announcements` table.

To run migrations:
```bash
./migrate.sh
```
or with PHP:
```bash
php migrate.php
```

## Database Factories & Seeders
Factories and seeders populate the database with realistic sample data:
- **Factories (`factories/`):**
  - `factories/UserFactory.php`: Generates fake alumni profiles.
  - `factories/AnnouncementFactory.php`: Generates fake department announcements.
- **Seeders (`seeders/`):**
  - `seeders/UserSeeder.php`: Seeds 10 user records.
  - `seeders/AnnouncementSeeder.php`: Seeds 5 announcement records.
  - `seeders/DatabaseSeeder.php`: Master seeder.
  - `seeders/seed.sql`: Direct SQL seed queries.

To run seeders:
```bash
./seed.sh
```
or with PHP:
```bash
php seed.php
```


