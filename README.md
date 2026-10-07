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
