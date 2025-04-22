# Kodano Task

A Symfony 7 API application using API Platform for managing products and categories.

## Requirements

- PHP 8.2+
- MySQL 8.0
- Composer
- Docker and Docker Compose

## Installation

1. Clone the repository:
```
git clone https://github.com/marlemiesz/kodano
```

2. Start the Docker containers:
```
docker-compose up -d
```

3. Enter the PHP container and install dependencies:
```
docker-compose exec php bash
composer install
```

4. Create database schema:
```
php bin/console doctrine:schema:create
```

5. Load fixtures (sample data):
```
php bin/console doctrine:fixtures:load --no-interaction
```

## API Documentation

After installation, the API documentation is available at:

**http://127.0.0.1:8000/api/docs**

This Swagger UI interface allows you to browse and test all available endpoints.

## API Endpoints

### Products

- `GET /api/products` - List all products
- `GET /api/products/{id}` - Get a specific product
- `POST /api/products` - Create a new product
- `PUT /api/products/{id}` - Update a product
- `DELETE /api/products/{id}` - Delete a product
- `POST /api/products/{id}/link-categories` - Link a product with categories

### Categories

- `GET /api/categories` - List all categories
- `GET /api/categories/{id}` - Get a specific category
- `POST /api/categories` - Create a new category
- `PUT /api/categories/{id}` - Update a category
- `DELETE /api/categories/{id}` - Delete a category

## Data Validation

- Category code must be unique and no longer than 10 characters
- Product must belong to at least one category
- Product price must be zero or positive

## Notification System

The application implements a notification system that logs and sends emails when product operations occur:

- Product creation
- Product update
- Product deletion
- Product category linking

Notifications are sent via:
- Application logs
- Email (configured but not actually sent in development)

The system is designed to be easily extended with other notification types like Slack or SMS.

## Testing

### Running Unit Tests

Inside the PHP container, run tests with PHPUnit:

Run all tests:
```
cd /var/www/kodano-task && ./vendor/bin/phpunit
```

## Project Structure

- `/src/Entity` - Doctrine entities
- `/src/Repository` - Doctrine repositories
- `/src/DataPersister` - API Platform data persistence handlers
- `/src/Service/Notification` - Notification services
- `/src/State/Processor` - API Platform state processors
- `/src/Dto` - Data Transfer Objects
- `/tests` - PHPUnit tests 