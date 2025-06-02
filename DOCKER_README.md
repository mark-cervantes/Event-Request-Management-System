# Sta Cruz Project - Docker Setup

This project consists of three main components:
- **PHP Backend** (sta_cruz_prj) - API endpoints running on PHP 8.2 with Apache
- **Vue Frontend** (vue-project) - Vue.js application with Vite
- **MySQL Database** - Database with initial schema from init.sql

## Prerequisites

- Docker
- Docker Compose

## Getting Started

1. **Clone or navigate to the project directory**
   ```bash
   cd "/home/cmark/Downloads/FINAL PROTOTYPE-20250602T033132Z-1-001/FINAL PROTOTYPE"
   ```

2. **Build and start all services**
   ```bash
   docker-compose up --build
   ```

   Or run in detached mode:
   ```bash
   docker-compose up --build -d
   ```

3. **Access the applications**
   - **Vue Frontend**: http://localhost:5173
   - **PHP Backend**: http://localhost:8080
   - **PHPMyAdmin**: http://localhost:8081 (for database management)
   - **MySQL Database**: localhost:3306

## Service Details

### MySQL Database
- **Port**: 3306
- **Database**: sta_cruz_db
- **Root Password**: rootpassword
- **User**: appuser
- **Password**: apppassword
- **Initial Schema**: Loaded from `init.sql`

### PHP Backend
- **Port**: 8080
- **PHP Version**: 8.2
- **Web Server**: Apache
- **Extensions**: PDO, MySQLi, GD, mbstring, etc.
- **Document Root**: `/var/www/html` (mapped to `./sta_cruz_prj`)

### Vue Frontend
- **Port**: 5173
- **Node Version**: 18
- **Build Tool**: Vite
- **Hot Reload**: Enabled

### PHPMyAdmin
- **Port**: 8081
- **Username**: root
- **Password**: rootpassword

## Useful Commands

### Start services
```bash
docker-compose up
```

### Start services in background
```bash
docker-compose up -d
```

### Stop services
```bash
docker-compose down
```

### Rebuild services
```bash
docker-compose up --build
```

### View logs
```bash
# All services
docker-compose logs

# Specific service
docker-compose logs php-backend
docker-compose logs vue-frontend
docker-compose logs mysql
```

### Execute commands in containers
```bash
# PHP container
docker-compose exec php-backend bash

# Vue container
docker-compose exec vue-frontend sh

# MySQL container
docker-compose exec mysql mysql -u root -p
```

### Reset database
```bash
docker-compose down -v
docker-compose up --build
```

## Development

- PHP files are in `./sta_cruz_prj` and changes are reflected immediately
- Vue files are in `./vue-project` and hot reload is enabled
- Database schema changes require rebuilding the MySQL service

## Troubleshooting

1. **Port conflicts**: If ports 3306, 5173, 8080, or 8081 are already in use, modify the port mappings in `docker-compose.yml`

2. **Permission issues**: If you encounter permission issues, run:
   ```bash
   sudo chown -R $USER:$USER .
   ```

3. **Database connection issues**: Ensure the MySQL service is fully started before the PHP service connects. The `depends_on` directive should handle this automatically.

4. **Vue development server not accessible**: Make sure the Vite server is configured to listen on `0.0.0.0` (already configured in `vite.config.js`)
