# Shopping

```sh
docker-sync start

```

```sh
docker-compose exec phpfpm composer install
docker-compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

```sh
# Force the default translations to use ICU MessageFormat
docker-compose exec phpfpm bin/console translation:update --force en --prefix=''
docker-compose exec phpfpm bin/console translation:update --force da
```


```sh
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn install
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn build
```
