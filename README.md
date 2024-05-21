# Shopping

```sh
docker-compose up -d
symfony local:server:start
```

```sh
composer install
bin/console doctrine:migrations:migrate --no-interaction
```

```sh
# Force the default translations to use ICU MessageFormat
bin/console translation:update --force en --prefix=''
bin/console translation:update --force da
```


```sh
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn install
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn build
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn watch
```
