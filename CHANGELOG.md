# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of PHP Todo App
- Symfony 7.2 with Doctrine ORM
- Docker containerization with multi-stage builds
- VS Code Dev Container support with Xdebug debugging
- Three Docker Compose configurations:
  - Production/Coolify (`docker-compose.coolify.yml`)
  - Development (`docker-compose.development.yml`)
  - Traefik (`docker-compose.traefik.yml`)
- Responsive dark-themed UI with Bootstrap 5
- Todo CRUD operations with priority and due dates
- REST API endpoints for JSON responses
- Health check endpoint for container orchestration
- Automatic database migrations on container start
- GitHub Actions CI/CD pipeline
- Dependabot configuration for automated updates
- Comprehensive VS Code tasks for common operations
- PHPUnit test suite
