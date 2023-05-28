# Symfony Currency Exchange Rate API

This project implements a currency exchange rate API using Symfony 5. The application fetches data from the Open Exchange Rates API, saves these rates into a MySQL database, and leverages Redis for caching to optimize data fetching. 

## Project Structure

- **Command:** The console command that fetches exchange rates from the Open Exchange Rates API is located in the `src\Command\CurrencyRatesCommand` class.
- **Controller:** The endpoint that returns the exchange rates for a given set of currencies is handled in the `src\Controller\ExchangeRatesController` class.
- **Entity:** The `src\Entity\CurrencyRate` class is an entity class representing a record of a currency exchange rate.
- **Tests:** The unit tests for the console command and the endpoint are in the `tests\CurrencyRatesCommandTest` and `tests\ExchangeRatesControllerTest` classes respectively.

## Setup

1. **Clone the repository**
    ```bash
    git clone https://github.com/fvarli/currency_exchange_api.git
    ```
2. **Install dependencies**
    ```bash
    cd repository
    composer install
    ```
3. **Environment Variables:** Create a `.env.local` file at the project root and populate the following variables:
    ```
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
    REDIS_URL=redis://localhost
    OPEN_EXCHANGE_RATES_APP_ID=your_open_exchange_rates_app_id
    REDIS_HOST=localhost
    REDIS_PORT=6379
    ```
    Update `db_user`, `db_password`, `db_name` and `your_open_exchange_rates_app_id` with your actual database credentials and Open Exchange Rates App ID.

4. **Database:** Run the Doctrine migrations to create the database schema:
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

## Usage

1. **Fetching Exchange Rates:** Run the console command to fetch the exchange rates and save them into the MySQL database and Redis:
    ```bash
    php bin/console app:currency:rates USD EUR,JPY,GBP,TRY
    ```
    Replace `USD`, `EUR`, `JPY`, `GBP` , `TRY` with the currencies you want to fetch.

2. **Getting Exchange Rates:** Send a GET request to the endpoint to get the exchange rates for a given set of currencies:
    ```
    GET /api/exchange-rates?base_currency=USD&target_currencies=EUR,GBP,JPY,TRY
    ```
    Replace `USD` in `base_currency` and `EUR,GBP,JPY,TRY` in `target_currencies` with the currencies you want.

## Scheduling the Command

To schedule the command to run automatically, you can add an entry to your system's crontab. The following command will run the fetch command every day at 1 AM server time:

```bash
0 1 * * * cd /path/to/project/root && php bin/console app:currency:rates USD EUR,GBP,JPY,TRY >> /var/log/currency_rates_command.log 2>&1
```

Replace `/path/to/project/root` with the actual path to your project's root directory. This command also logs the output of the command to `/var/log/currency_rates_command.log` for monitoring purposes.

Remember to replace `EUR,GBP,JPY,TRY` with the actual currencies you want to fetch.

## Tests

To run the tests, use the following command:
```bash
php bin/phpunit
```

The test classes `CurrencyRatesCommandTest` and `ExchangeRatesControllerTest` are available for testing the functionality of the console command and the endpoint respectively.