# Kodano Task

A Symfony 7 API application using API Platform for managing products and categories.

## Requirements

- PHP 8.2+
- MySQL 8.0
- Composer
- Docker and Docker Compose (optional)

## Installation

### Using Docker

1. Clone the repository:
```
git clone <repository-url>
cd kodano-task
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

### Manual Installation

1. Clone the repository:
```
git clone <repository-url>
cd kodano-task
```

2. Install dependencies:
```
composer install
```

3. Configure the `.env` file with your database settings:
```
DATABASE_URL="mysql://app:app@127.0.0.1:3306/app?serverVersion=8.0&charset=utf8mb4"
```

4. Create database schema:
```
php bin/console doctrine:schema:create
```

5. Start the Symfony web server:
```
symfony serve
```

## API Endpoints

The API documentation is available at `/api/docs`.

### Products

- `GET /api/products` - List all products
- `GET /api/products/{id}` - Get a specific product
- `POST /api/products` - Create a new product
- `PUT /api/products/{id}` - Update a product
- `DELETE /api/products/{id}` - Delete a product

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

Notifications are sent via:
- Application logs
- Email (configured but not actually sent in development)

The system is designed to be easily extended with other notification types like Slack or SMS.

## Testing

Run tests with PHPUnit:
```
php bin/phpunit
```

## Project Structure

- `/src/Entity` - Doctrine entities
- `/src/Repository` - Doctrine repositories
- `/src/DataPersister` - API Platform data persistence handlers
- `/src/Service/Notification` - Notification services
- `/tests` - PHPUnit tests 