# Currency Exchange Service

This is a simple web application for allowing a user to purchase foreign currencies.

## 🚀 Getting Started

These instructions will get you a copy of the project up and running on 
your local machine for development and testing purposes.

### Prerequisites

* [Docker Desktop](https://www.docker.com/products/docker-desktop/)
* [Node.js & npm](https://nodejs.org/en)

---

### 1. Clone the Repository

```shell
git clone git@github.com:Daniel-C02/currency-exchange-service.git
cd currency-exchange-service
```

---

### 2. Configure Environment
   
Copy the example environment file. You may configure your database and 
other settings here, but the defaults are set up for Laravel Sail.

```shell
cp .env.example .env
```

---

### 3. Install Composer Dependencies

This command runs `composer install` inside a temporary Docker container.

```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

---

### 4. Start Docker Containers

Start the application containers (web, database, etc.) in the background.

```shell
./vendor/bin/sail up -d
```

Note: You can now use sail as an alias for interacting with 
the application (e.g., sail artisan..., sail npm...).

---

### 5. Generate Application Key

```shell
sail artisan key:generate
```

---

### 6. Run Database Migrations & Seed

This will create the database schema and populate it with initial data.

```shell
sail artisan migrate:fresh --seed
```

---

### 7. Clear Configuration Cache

Any time you change your .env file, you must run this command to load the new configuration.

```shell
sail artisan config:cache
```

If the command above gives you trouble, you can also run:

```shell
sail artisan optimize:clear
```

---

### 8. Install NPM Dependencies

```shell
npm install
```

---

### 9. Build Frontend Assets

Run the Vite development server to compile assets and enable hot-reloading.

```shell
npm run dev
```

You can now access the application at http://localhost

---

## 🛠️ Artisan Commands

Fetching Currency Rates

This project includes an Artisan command to fetch the latest currency exchange rates from an external API. 
The command will update the rates for the currencies defined in the App\Options\CurrencyOptions class.
The command is also scheduled to run daily at midnight in the routes\console scheduler.

To run the command manually, use:

```shell
sail artisan currency:fetch-rates
```



---

## 📧 Email Catching (Mailpit)

This project uses Mailpit to intercept all outgoing emails during development. 
This allows you to test email functionality (like the order confirmations email) without sending real emails.

After running `sail up -d`, you can view the Mailpit dashboard in your browser at:

**[http://localhost:8025](http://localhost:8025)**

