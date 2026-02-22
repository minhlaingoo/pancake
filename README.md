# Project Pancake

This is a Pancake project with frontend assets managed by NPM.

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:

- PHP (>= 8.2)
- Composer (>= 2.8)
- Node.js (>= 20.x) and NPM
- MySQL or any other supported database
- Git

## Installation

Follow these steps to set up the project locally:

### 1. Clone the Repository

```bash
git clone 
cd project-pancake
```

### 2. Copy .env.example to .env

```bash
cp .env.example .env
```

### 3. Dependency install

```bash
composer install
npm install
```

### 4. Generate app key

```bash
php artisan key:generate
```

### 5. Migrate database table

```bash
php artisan migrate
```

### 6. Seeding default data

```bash
php artisan db:seed
```

### 7. Building Project for Node js

```bash
npm run build
```

### 8. Start test on local (only local)

```bash
php artisan serve
npm run dev
```

### 9. Default Authentication

```json
{
    "email" : "pancake@admin.com",
    "password" : "password"
}
```

### Generating Openssl certificates

```bash
# 1. Generate CA private key
openssl genrsa -out ca.key 2048

# 2. Generate self-signed CA certificate
MSYS_NO_PATHCONV=1 openssl req -x509 -new -nodes -key ca.key -subj "/CN=TestCA" -days 365 -out ca.crt

# 3. Generate server key and CSR
openssl genrsa -out server.key 2048
MSYS_NO_PATHCONV=1 openssl req -new -key server.key -subj "/CN=localhost" -out server.csr

# 4. Sign server certificate with CA
openssl x509 -req -in server.csr -CA ca.crt -CAkey ca.key -CAcreateserial -out server.crt -days 365

# 5. (Optional) Generate client certs
openssl genrsa -out client.key 2048
MSYS_NO_PATHCONV=1 openssl req -new -key client.key -subj "/CN=client" -out client.csr
openssl x509 -req -in client.csr -CA ca.crt -CAkey ca.key -CAcreateserial -out client.crt -days 365
```
