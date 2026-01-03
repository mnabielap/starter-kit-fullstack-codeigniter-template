# 🚀 Starter Kit Fullstack CodeIgniter 4

![PHP](https://img.shields.io/badge/PHP-%3E%3D%208.2-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6-EF4223.svg?style=for-the-badge&logo=codeigniter&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED.svg?style=for-the-badge&logo=docker&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3.svg?style=for-the-badge&logo=bootstrap&logoColor=white)

A robust, production-ready **Fullstack Application Template** built with **CodeIgniter 4**.
This project goes beyond the basic MVC structure, implementing a **Service Layer Architecture** and a **Dual Interface System** (HTML Views + JSON API) to ensure scalability and maintainability.

---

## ✨ Key Features

*   **🧠 Service Layer Architecture**: Business logic is separated from Controllers using Services.
*   **🔌 Dual Interface**:
    *   **Web**: Server-side rendered Views with Bootstrap 5 (Dashboard, Login, CRUD).
    *   **API**: RESTful JSON API for external clients (Mobile/React/Vue).
*   **🔒 Secure Authentication**:
    *   **JWT** (JSON Web Tokens) for API security.
    *   **Session Auth** for Web Interface.
    *   Comprehensive flow: Login, Register, Forgot Password, Email Verification.
*   **🛡️ Robust Middleware**: CORS, Rate Limiting, Role-Based Access Control (RBAC).
*   **💾 Database Agnostic**: Seamlessly switch between **MySQL** and **SQLite** via `.env`.
*   **🐳 Docker Ready**: Full containerization support with persistent volumes and custom networking.
*   **📝 API Documentation**: Integrated **Swagger UI** (OpenAPI 3.0).
*   **🧪 Automated Testing**: Python-based script suite for endpoint verification.

---

## 📂 Project Structure

```text
├── api_tests/            # Python scripts for API Testing
├── app/
│   ├── Config/           # App Configuration (Routes, Auth, Database)
│   ├── Controllers/      # Request Handlers
│   │   ├── Api/          # JSON API Controllers (V1)
│   │   └── Web/          # HTML View Controllers
│   ├── Entities/         # Data Objects (User, Token)
│   ├── Filters/          # Middleware (JWT, Cors, RateLimit)
│   ├── Models/           # Database Interactions
│   ├── Services/         # Business Logic Layer
│   └── Views/            # HTML Templates (Layouts, Pages)
├── public/               # Web Entry Point & Assets
├── writable/             # Logs, Cache, and SQLite DB
├── .env.example          # Environment variables template
├── compose.yaml          # Docker Compose (Optional)
├── Dockerfile            # Docker Configuration
└── spark                 # CI4 Command Line Tool
```

---

## 🛠️ Getting Started (Local Development)

> [!TIP]
> **Recommended:** We strongly suggest running the project locally first to understand the structure before containerizing it.

### Prerequisites
*   PHP >= 8.2
*   Composer
*   MySQL (or use the built-in SQLite)

### 1. Installation

Clone the repository and install dependencies:

```bash
git clone https://github.com/mnabielap/starter-kit-fullstack-codeigniter-template.git
cd starter-kit-fullstack-codeigniter-template
composer install
```

### 2. Environment Setup

Copy the example environment file:

```bash
# Windows
copy .env.example .env

# Mac/Linux
cp .env.example .env
```

Open `.env` and configure your settings:
*   **For SQLite (Default):** No changes needed.
*   **For MySQL:** Uncomment the MySQL block and add your credentials.

### 3. Generate Keys & Migrate

Generate the application encryption key and create the database tables.

```bash
php spark key:generate
php spark migrate
php spark db:seed AdminSeeder
```

### 4. Run the Application

Start the local development server:

```bash
php spark serve
```

*   **Dashboard:** `http://localhost:8080`
*   **API Docs:** `http://localhost:8080/api/v1/docs`

---

## 🐳 Running with Docker

If you prefer Docker, follow these steps to set up a persistent environment with a separate MySQL container on a custom network.

### 1. Create Network
Create a shared network for the application and database to communicate.

```bash
docker network create fullstack_codeigniter_network
```

### 2. Create Volumes
Create volumes to ensure your Database data and Uploaded files persist even if containers are deleted.

```bash
docker volume create fullstack_codeigniter_db_volume
docker volume create fullstack_codeigniter_media_volume
```

### 3. Setup Environment
Create a specific `.env` file for Docker.

```bash
# Windows
copy .env.example .env.docker
# Mac/Linux
cp .env.example .env.docker
```

> [!IMPORTANT]
> **Critical Configuration in `.env.docker`:**
> 1. Set `CI_ENVIRONMENT=production` (or development).
> 2. **NO SPACES** around the `=` sign.
> 3. Set `app.baseURL=http://localhost:5005/`
> 4. Set `DB_HOSTNAME=fullstack-codeigniter-mysql` (Container Name).

### 4. Run MySQL Container
Start the MySQL database container.

```bash
docker run -d \
  --name fullstack-codeigniter-mysql \
  --network fullstack_codeigniter_network \
  -v fullstack_codeigniter_db_volume:/var/lib/mysql \
  -e MYSQL_ROOT_PASSWORD=rootpassword \
  -e MYSQL_DATABASE=starter_kit_db \
  -e MYSQL_USER=user \
  -e MYSQL_PASSWORD=userpassword \
  mysql:8.0
```

### 5. Build & Run App Container
Build the image and run the application container on port **5005**.

```bash
# Build Image
docker build -t fullstack-codeigniter-app .

# Run Container
docker run -d -p 5005:5005 \
  --env-file .env.docker \
  --network fullstack_codeigniter_network \
  -v fullstack_codeigniter_db_volume:/var/www/html/writable \
  -v fullstack_codeigniter_media_volume:/var/www/html/public/assets/images \
  --name fullstack-codeigniter-container \
  fullstack-codeigniter-app
```

### 6. Final Setup
Since the database is new, seed the default Admin user:

```bash
docker exec -it fullstack-codeigniter-container php spark db:seed AdminSeeder
```

🚀 **Done!** Access your app at: **`http://localhost:5005`**

---

## 📦 Docker Management Cheat Sheet

Essential commands to manage your lifecycle.

#### 📜 View Logs
See what's happening inside the container (Migrations, Errors, Access Logs).
```bash
docker logs -f fullstack-codeigniter-container
```

#### 🛑 Stop Container
Safely stop the running application.
```bash
docker stop fullstack-codeigniter-container
```

#### ▶️ Start Container
Resume a stopped container.
```bash
docker start fullstack-codeigniter-container
```

#### 🗑 Remove Container
Remove the container to free up names/ports (Your data stays safe in volumes).
```bash
docker stop fullstack-codeigniter-container
docker rm fullstack-codeigniter-container
```

#### 📂 View Volumes
List all persistent storage volumes.
```bash
docker volume ls
```

#### ⚠️ Remove Volume
**WARNING:** This deletes your database and uploads **PERMANENTLY**.
```bash
docker volume rm fullstack_codeigniter_db_volume
```

---

## 🧪 API Testing

This project comes with a suite of Python scripts to test the API endpoints automatically without needing Postman.

### Setup
1.  Navigate to the `api_tests` folder.
2.  Ensure you have Python installed.
3.  The scripts use `utils.py` to manage configuration and tokens automatically.

### Running Tests
Execute the files in order. **No arguments needed.**

**1. Authentication Flow:**
```bash
# Register a new user
python A1.auth_register.py

# Login (Saves token to secrets.json)
python A2.auth_login.py

# Refresh Token
python A3.auth_refresh.py
```

**2. User Management (Admin):**
*Note: You must log in via `A2.auth_login.py` first.*

```bash
# Create a User
python B1.user_create.py

# Get All Users
python B2.user_get_all.py

# Get Specific User
python B3.user_get_one.py

# Update User
python B4.user_update.py

# Delete User
python B5.user_delete.py
```

---

## 📝 Credentials

**Default Admin:**
*   **Email:** `admin@example.com`
*   **Password:** `password123`

---

## 📄 License

This project is open-source and available under the **MIT License**.