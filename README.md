# Crypto School Assessment

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-000000?style=for-the-badge&logo=inertia&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-B73BFE?style=for-the-badge&logo=vite&logoColor=FFD62E)

A Laravel + Vue 3 + Inertia.js based crypto school assessment project.

## 🚀 Installation

### Prerequisites
- PHP ≥ 8.3
- Composer
- Node.js ≥ 18 + npm
- MySQL/MariaDB
- Git

### Setup Steps

1. **Clone the repository**:
   ```bash
   git clone git@github.com:Uzeyir9695/crypto-school-assessment.git crypto-school
   cd crypto-school
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up database**:
    - Update `.env` with your database credentials:
      ```env
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=your_db_name
      DB_USERNAME=your_db_user
      DB_PASSWORD=your_db_password
      ```

5. **Run migrations & seed data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Create storage link**:
   ```bash
   php artisan storage:link
   ```

7. **Install frontend dependencies**:
   ```bash
   npm install
   npm run dev
   ```

8. **Start development server**:
   ```bash
   php artisan serve
   ```
