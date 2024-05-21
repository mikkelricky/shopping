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

## Fixtures

```sh
docker compose exec phpfpm bin/console hautelook:fixtures:load
# or (for the impatient, but brave):
docker compose exec phpfpm bin/console hautelook:fixtures:load --no-interaction
```

## Coding standards

```sh
task dev:coding-standards:check
```

```sh
docker run --volume ${PWD}:/code --rm pipelinecomponents/yamllint yamllint fixtures
```

## Testing

```sh
docker compose run --rm node yarn install
docker compose run --rm playwright npx playwright install
docker compose run --rm playwright npx playwright test
open playwright-report/index.html
```

```sh
# @see https://docs.itkdev.dk/docs/test/cypress/getting_started
# @see https://gist.github.com/cschiewek/246a244ba23da8b9f0e7b11a68bf3285#file-x11_docker_mac-md
# Xquartz &
# Calling `xhost` will (apparently) start Xquartz
# @see https://gist.github.com/cschiewek/246a244ba23da8b9f0e7b11a68bf3285#file-x11_docker_mac-md
# Install XQuartz: brew install xquartz
xhost + 127.0.0.1
docker compose run --rm --env DISPLAY=host.docker.internal:0 playwright npx playwright test --ui
```
