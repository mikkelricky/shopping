# Shopping

```sh
docker compose pull
docker compose up --detach --wait
open "http://$(docker compose port nginx 8080)"
open "http://$(docker compose port mail 8025)"
```

```sh
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

```sh
docker compose exec phpfpm composer update-translations
```

```sh
docker compose run --rm node yarn install
docker compose run --rm node yarn build
```

```sh
docker compose run --rm node yarn watch
```

## Coding standards

```sh
docker compose exec phpfpm composer coding-standards-check
```

```sh
docker compose exec phpfpm composer coding-standards-apply
```

```sh
docker compose run --rm node yarn coding-standards-check
```

```sh
docker compose run --rm node yarn coding-standards-apply
```
