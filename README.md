<h1 align="center">
  Audiocasts
</h1>

<p align="center">
   A platform to host RSS podcast feeds for your audiobooks 🎧 
</p>

---

## 🛠️ Development & Testing

```shell
# Install PHP dependencies
composer install

# Install JavaScript dependencies
yarn install

# Build frontend for development
yarn run build

# Setup application config and database
php artisan audiocasts:init
```

To start a local PHP dev server which serves the application on port **8000** run the following command from the root of
the application.

```shell
php artisan serve
```

To compile the frontend for development whenever a change happens, run this command.

```shell
yarn run watch
```

## ⚖️ License

Audiocasts is available under the MIT license. Consult the [LICENSE](LICENSE) file for more information.
