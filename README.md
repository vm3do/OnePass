# OnePass API - Secure Password Management

## 📌 Introduction

OnePass is a secure API for managing passwords, allowing users to **create, read, update, and delete** their stored credentials while ensuring data privacy. The API follows a **Zero-Knowledge-like encryption model**, where passwords are encrypted client-side before being sent to the server. It also includes **advanced security features** such as rate limiting, device verification, and IP management.

## 🚀 Features

### 🔑 Authentication (Implemented with Laravel Sanctum)

- Users must register and log in to interact with the API.
- Authentication is handled via **Bearer tokens**.

### 🔐 Password Management (CRUD)

- Users can **store, retrieve, update, and delete** their passwords securely.

### ⚡ Rate Limiting

- Limits login attempts to **10 requests per second**.
- Exceeding the limit results in a **1-hour block**.
- Users receive an email warning if suspicious activity is detected.

### 📍 Device & IP Management

- **Device Verification:** Users receive an email confirmation when logging in from a new device.
- **IP Whitelist & Blacklist:**
  - Users can add trusted IPs to a whitelist.
  - Admins can block suspicious IPs.
  - Users can manage their own whitelist via API routes.

### 🌍 Geolocation Restrictions

- API can restrict access based on country settings.

### 📦 Import Passwords

- Users can import passwords from **browser-based password managers** (Chrome, Firefox, Safari, etc.).

## 🛠 Installation

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/vm3do/OnePass.git
cd OnePass
```

### 2️⃣ Install Dependencies

```bash
composer install
```

### 3️⃣ Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

- Update **.env** with your database and mail settings.

### 4️⃣ Migrate Database

```bash
php artisan migrate
```

### 5️⃣ Run the Application

```bash
php artisan serve
```

## 🔑 Authentication

### Register

```http
POST /api/register
```

#### Request Body (JSON)

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "12341234",
  "password_confirmation": "12341234"
}
```

### Login

```http
POST /api/login
```

#### Response

```json
{
  "token": "your-auth-token"
}
```

**Use the token for authorization in subsequent requests:**

```http
Authorization: Bearer your-auth-token
```

## 📄 API Endpoints

### Password Management

| Method | Endpoint              | Description                 |
| ------ | --------------------- | --------------------------- |
| GET    | `/api/passwords`      | Get all saved passwords     |
| POST   | `/api/passwords`      | Save a new password         |
| PUT    | `/api/passwords/{id}` | Update an existing password |
| DELETE | `/api/passwords/{id}` | Delete a password           |

### IP Management

| Method | Endpoint                 | Description                     |
| ------ | ------------------------ | ------------------------------- |
| POST   | `/api/ip/whitelist`      | Add an IP to the whitelist      |
| DELETE | `/api/ip/whitelist/{id}` | Remove an IP from the whitelist |
| POST   | `/api/ip/blacklist`      | Add an IP to the blacklist      |
| DELETE | `/api/ip/blacklist/{id}` | Remove an IP from the blacklist |

## 🌍 Deployment

- The API is deployed on **AWS EC2** servers.
- Uses **SSL/TLS encryption** for secure communication.

## 👥 Contributors

| Name    | Role                                         |
| ------- | -------------------------------------------- |
| **Me**  | Scrum Master & Authentication, IP Management |
| Meryem  | UML Design & Rate Limiting                   |
| Omar    | Class Diagrams & Password Import Feature     |
| Souhail | Password CRUD & Device Verification          |

---

**OnePass - Secure Your Passwords with Confidence!** 🔒

