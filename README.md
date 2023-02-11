# :zap: Boulder route backend :zap:
REST API with Symfony and API Platform, used as a backend for a boulder route app.

Use **`make help`** in the terminal to list available commands. Makefile is used as the entrypoint to the project

---

### Install Dependencies

`make install`

---

### Configure Database, Environment Variables

- Make a copy of `.env` and rename it to `.env.local`
- Update it with your database credentials

If you haven't created a database, you can create it with doctrine:

```bash
php ./bin/console doctrine:database:create
```

- `make checkdb` - prints the raw sql queries to be executed
- `make syncdb` - executes the queries to sync the database with its entities

---

### Run Project

`make dev-server`

(**Note**: This make recipe uses the [Symfony binary](https://symfony.com/download) so it may not work if you're using another server like Apache/NgInx to serve your files. Installing the binary is necessary to use this recipe)

- `make refresh` - clears and warmup/refresh cache

---

### Tutorial

Find the Tutorial on **[Digital Fortress](https://digitalfortress.tech/)**

- [Setup Rest API with Symfony and API Platform](https://digitalfortress.tech/tutorial/rest-api-with-symfony-and-api-platform/)
- [Handling File Uploads with API Platform](https://digitalfortress.tech/php/file-upload-with-api-platform-and-symfony/)

### Live

#### CLI
```/opt/php80/bin/php /composer/composer.phar update --no-dev```

```/opt/php80/bin/php bin/console lexik:jwt:generate-keypair --overwrite --quiet --env prod```