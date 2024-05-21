# Shopping

```sh
docker compose up --detach
symfony local:server:start
```

```sh
symfony composer install
symfony console doctrine:migrations:migrate --no-interaction
```

```sh
symfony composer update-translations
```

```sh
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn install
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn build
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn watch
```

## Coding standards

```sh
symfony composer coding-standards-check
```

```sh
symfony composer coding-standards-apply
```

```sh
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn coding-standards-check
```

```sh
docker run --volume ${PWD}:/app --workdir /app --tty --interactive node:latest yarn coding-standards-apply
```
