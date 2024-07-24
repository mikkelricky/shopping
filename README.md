# Shopping

## Installation

Download a release from <https://github.com/mikkelricky/shopping/releases> and extract it:

``` shell
curl --location https://github.com/mikkelricky/shopping/releases/download/release-dev-feature_pre-release/shopping-main.tar.gz > shopping.tar.gz
# Extract skipping top-level folder (`shopping/`)
tar xvf shopping.tar.gz --strip-components=1
# Edit/create .env.local
bin/console doctrine:migrations:migrate --no-interaction
bin/console cache:clear
```

## Development

``` shell
docker compose pull
docker compose up --build --detach --wait
open "http://$(docker compose port php 80)"
open "http://$(docker compose port mail 8025)"
```

``` shell
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

``` shell
docker compose exec phpfpm composer update-translations
```

``` shell
docker compose run --rm node npm install
docker compose run --rm node npm run build
```

``` shell
docker compose run --rm node npm run watch
```

### Fixtures

``` shell
docker compose exec phpfpm bin/console hautelook:fixtures:load
# or (for the impatient, but brave):
docker compose exec phpfpm bin/console hautelook:fixtures:load --no-interaction
```

### Coding standards

``` shell
task dev:coding-standards:check
```

``` shell
docker run --volume ${PWD}:/code --rm pipelinecomponents/yamllint yamllint fixtures
```

### Testing

``` shell
docker compose run --rm node npm install
docker compose run --rm playwright npx playwright install
docker compose run --rm playwright npx playwright test
open playwright-report/index.html
```

``` shell
# @see https://docs.itkdev.dk/docs/test/cypress/getting_started
# @see https://gist.github.com/cschiewek/246a244ba23da8b9f0e7b11a68bf3285#file-x11_docker_mac-md
# Xquartz &
# Calling `xhost` will (apparently) start Xquartz
# @see https://gist.github.com/cschiewek/246a244ba23da8b9f0e7b11a68bf3285#file-x11_docker_mac-md
# Install XQuartz: brew install xquartz
xhost + 127.0.0.1
docker compose run --rm --env DISPLAY=host.docker.internal:0 playwright npx playwright test --ui
```

## Release

``` shell
task dev:release:test
```
