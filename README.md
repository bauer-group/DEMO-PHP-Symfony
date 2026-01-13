# DEMO-PHP-Dockerized

A modern, containerized PHP Todo application built with Symfony 7.2 and Doctrine ORM.

Designed as a reference implementation for containerized PHP development with VS Code, featuring full Xdebug debugging support and multiple deployment options.

## Features

- **Modern PHP Stack**: Symfony 7.2, Doctrine ORM, PHP 8.3
- **Containerized**: Docker-based development and production environments
- **VS Code Integration**: Dev Containers, Xdebug debugging, tasks
- **Multiple Deployments**: Coolify, Traefik, Standalone
- **Responsive UI**: Dark theme, Bootstrap 5, mobile-friendly
- **CI/CD Ready**: GitHub Actions with semantic releases
- **Testing**: PHPUnit with SQLite for fast isolated tests

## Quick Start

### Development (Docker Compose)

```bash
# Clone the repository
git clone https://github.com/bauer-group/DEMO-PHP-Dockerized.git
cd DEMO-PHP-Dockerized

# Start development environment (no .env needed for dev)
docker compose -f docker-compose.development.yml up -d

# Access the application
open http://localhost:8080
```

### Development (VS Code Dev Container)

1. Open the project in VS Code
2. Install the "Remote - Containers" extension
3. Press `F1` → "Dev Containers: Reopen in Container"
4. Wait for the container to build and dependencies to install
5. Access the application at `http://localhost:8080`

### Production

```bash
# Configure environment
cp .env.example .env
# Edit .env with secure production values (passwords, APP_SECRET)

# Start production stack
docker compose -f docker-compose.coolify.yml up -d
```

## Project Structure

```
DEMO-PHP-Dockerized/
├── .devcontainer/               # VS Code Dev Container configuration
├── .github/                     # GitHub Actions workflows
│   ├── workflows/
│   │   ├── docker-release.yml   # Build & publish to GHCR
│   │   ├── docker-maintenance.yml
│   │   ├── teams-notifications.yml
│   │   └── ai-issue-summary.yml
│   └── dependabot.yml
├── .vscode/                     # VS Code settings
│   ├── launch.json              # Xdebug configurations
│   ├── tasks.json               # Build tasks
│   └── extensions.json          # Recommended extensions
├── docs/                        # Documentation
│   └── SETUP.md                 # Repository setup guide
├── src/                         # PHP application source
│   ├── bin/                     # Console entry point
│   ├── config/                  # Symfony configuration
│   ├── docker/                  # Docker configuration files
│   ├── migrations/              # Doctrine migrations
│   ├── public/                  # Web entry point
│   ├── src/                     # Application source code
│   │   ├── Controller/          # HTTP controllers
│   │   ├── Entity/              # Doctrine entities
│   │   ├── Form/                # Symfony forms
│   │   └── Repository/          # Doctrine repositories
│   ├── templates/               # Twig templates
│   ├── tests/                   # PHPUnit tests
│   ├── .env                     # Symfony defaults (dev)
│   ├── .env.test                # Test environment (SQLite)
│   ├── composer.json
│   ├── phpunit.xml.dist
│   └── Dockerfile
├── docker-compose.coolify.yml       # Production/Coolify
├── docker-compose.development.yml   # Development with Xdebug
├── docker-compose.traefik.yml       # Traefik reverse proxy
├── .env.example                     # Environment template
└── README.md
```

## Docker Compose Files

| File | Use Case | Features |
|------|----------|----------|
| `docker-compose.coolify.yml` | Production, Coolify | Optimized, port 8080 |
| `docker-compose.development.yml` | Local development | Hot-reload, Xdebug, phpMyAdmin |
| `docker-compose.traefik.yml` | Behind Traefik | HTTPS, Let's Encrypt |

## VS Code Tasks

Access via `Ctrl+Shift+P` → "Tasks: Run Task":

| Task | Description |
|------|-------------|
| Docker: Start Development | Start dev environment |
| Docker: Stop Development | Stop dev environment |
| Docker: Rebuild Development | Rebuild with fresh image |
| Docker: View Logs | Stream application logs |
| Composer: Install | Install PHP dependencies |
| Symfony: Clear Cache | Clear Symfony cache |
| Doctrine: Run Migrations | Execute database migrations |
| Doctrine: Generate Migration | Create new migration |
| PHPUnit: Run Tests | Execute test suite |

## Debugging with Xdebug

### VS Code Configuration

1. Start the development environment
2. Open the Debug panel (`Ctrl+Shift+D`)
3. Select "Listen for Xdebug" configuration
4. Press `F5` to start debugging
5. Set breakpoints in your PHP files
6. Refresh the browser - debugger will pause at breakpoints

### Debug Configurations

- **Listen for Xdebug**: Standard debugging for browser requests
- **Listen for Xdebug (Dev Container)**: When using Dev Containers
- **Debug Current Script**: Debug CLI scripts
- **Debug Symfony Console**: Debug console commands

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/` | GET | Todo list (HTML) |
| `/new` | GET/POST | Create todo |
| `/{id}/edit` | GET/POST | Edit todo |
| `/{id}/toggle` | POST | Toggle completion |
| `/{id}/delete` | POST | Delete todo |
| `/api/todos` | GET | List todos (JSON) |
| `/api/todos/{id}/toggle` | POST | Toggle (JSON) |
| `/health` | GET | Health check |

## Environment Variables

See [.env.example](.env.example) for all available variables.

| Variable | Default | Description |
|----------|---------|-------------|
| `STACK_NAME` | `todo-app` | Docker stack name |
| `TIME_ZONE` | `Etc/UTC` | Container timezone |
| `MYSQL_DATABASE` | `todo_db` | Database name |
| `MYSQL_USER` | `todo_user` | Database user |
| `MYSQL_PASSWORD` | - | Database password |
| `MYSQL_ROOT_PASSWORD` | - | MySQL root password |
| `APP_ENV` | `prod` | Symfony environment |
| `APP_DEBUG` | `0` | Debug mode (0/1) |
| `APP_SECRET` | - | Symfony secret key |
| `APP_PORT` | `8080` | Application HTTP port |
| `SERVICE_HOSTNAME` | `todo.example.com` | Public hostname (Traefik) |
| `PROXY_NETWORK` | `EDGEPROXY` | Traefik external network |

## Database Migrations

Migrations run automatically on container start. Manual commands:

```bash
# Generate migration from entity changes
docker compose -f docker-compose.development.yml exec app php bin/console make:migration

# Run pending migrations
docker compose -f docker-compose.development.yml exec app php bin/console doctrine:migrations:migrate

# Check migration status
docker compose -f docker-compose.development.yml exec app php bin/console doctrine:migrations:status
```

## Testing

Tests use SQLite in-memory database for fast, isolated execution.

```bash
# Run all tests
docker compose -f docker-compose.development.yml exec app composer test

# Run with coverage report
docker compose -f docker-compose.development.yml exec app composer test:coverage

# Coverage report will be in src/build/coverage/
```

## Deployment Options

### Coolify

1. Create new Docker Compose service in Coolify
2. Point to repository and select `docker-compose.coolify.yml`
3. Configure environment variables in Coolify dashboard
4. Deploy

### Traefik

1. Ensure Traefik is running with external network `EDGEPROXY`
2. Configure DNS records for `SERVICE_HOSTNAME`
3. Deploy with `docker compose -f docker-compose.traefik.yml up -d`

### Standalone

```bash
cp .env.example .env
# Edit .env with your values

docker compose -f docker-compose.coolify.yml up -d
# Access at http://localhost:8080
```

## CI/CD Pipeline

The GitHub Actions workflow handles:

1. **Validation**: Docker Compose & shell script syntax
2. **Release**: Semantic versioning with changelog
3. **Build**: Docker image for linux/amd64
4. **Publish**: GitHub Container Registry (GHCR)

For workflow secrets and repository setup, see [docs/SETUP.md](docs/SETUP.md).

## License

MIT License - see [LICENSE](LICENSE) file.
