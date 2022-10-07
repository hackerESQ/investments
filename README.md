# Investments

A Laravel application to track your investment portfolio performance, dividends, and stock splits using live market data. 

* [Installation](#installation)
* [Import/Export](#importexport)
* [Commands](#commands)
  * [Refresh Market Data](#refresh-market-data)
  * [Refresh Dividend Data](#refresh-dividend-data)
  * [Refresh Splits Data](#refresh-splits-data)
  * [Refresh Holding Data](#refresh-holding-data)
  * [Capture Daily Change](#capture-daily-change)
  
## Installation

To begin, you should pull the source from github using `git clone` like this:

```bash
git clone https://github.com/hackerESQ/investments && cd investments
```

Now, you'll need to install all required composer packages:

```bash
composer install
```

Finally, you should configure your database credentials in your `.env` file. Now, you can choose to install locally (e.g. on Homestead or Valet) or on Docker (with the help of Sail).

### Local

Assuming you already have your local development environment configured (e.g. if you're running valet or homestead), you can simply seed the database with the first user:

```bash
php artisan db:seed
```

That's it. You can now access Investments at `localhost` or `investments.test`, depending on your local development environment. The first user's credentials are: 

```
Username: user@user.com
Password: password
```

You should change these user credentials.

### Docker (Sail)

Running Investments in Docker ensures the scheduler and queues are configured appropriately. But, there's several `.env` variables you should ensure to configure as appropriate. These determine how your Docker containers will function:

```env
APP_URL=localhost
APP_SERVICE=investments.test
APP_PORT=80
```

You can now run `sail up -d` to start the webserver and database containers. But you'll need to create your first user, using the `sail artisan db:seed` command. This will run the seed command in the container and create the first user: 

```
Username: user@user.com
Password: password
```

You should change these user credentials.

## Import/Export

You can import and export all data within Investments. Imports and exports use Excel files.

### Import

To begin importing data, you'll need to get the import template. You can just export whether you have data or not. This is the import template. When you import data, it is upserted (i.e. existing data is updated and new data is inserted).

### Export

Export is simple. Each set of data is on a separate worksheet within the exported file.

## Commands

There are various commands available to help facilitate your investment tracking.

### Refresh Market Data
Coming soon.

### Refresh Dividend Data
Coming soon.

### Refresh Splits Data
Coming soon.

### Refresh Holding Data
Coming soon.

### Capture Daily Change
Coming soon.

## Finally
### Testing
You can run tests with the `composer test` command.

### Contributing
Feel free to create a fork and submit a pull request if you would like to contribute.

### Bug reports
Raise an issue on GitHub if you notice something broken.
