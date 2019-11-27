# Web Analytics Italia

[![CircleCI](https://img.shields.io/circleci/project/github/agid/wai-portal.svg?colorB=0066cc)](https://circleci.com/gh/agid/wai-portal)

## Development

### Local environment

#### Requirements
- php >= 7.3
- [composer](https://getcomposer.org/)
- node ([nodenv](https://github.com/nodenv/nodenv))
- docker and docker-compose

#### Getting started
- Refer to [build.properties.example](env/build.properties.example) file
  for initial configuration.
- Run:
  ```
  composer install --no-scripts
  bin/phing build
  ```

#### Ports open on docker host
- portal (nginx) => port 80/http and 443/https
- mail (mailhog) => port 8025/http
- analytics service (matomo) => port 8090/http and 9443/https
- phpMyAdmin => port 8080/http (not started automatically)
- kibana => port 5601/http (not started automatically)

*Ports are configurable*

#### Available tasks
```
bin/phing build         # build the portal
bin/phing test          # perform tests
bin/phing clean         # delete containers and data
bin/phing stop          # stop containers
bin/phing start         # start containers
bin/phing pma           # start phpMyAdmin container (not started with bin/phing start)
bin/phing kibana        # start kibana container (not started with bin/phing start)
bin/phing sentinel      # start redis sentinel container (not started with bin/phing start)
```

#### SPID authentication
This project use the [SPID Laravel](https://github.com/italia/spid-laravel) package.
Refer to its [README](https://github.com/italia/spid-laravel/blob/master/README.md) file for configuring and testing.

## Deployment

## Licenses

`AGPL-3.0-or-later License` is generally applied to all the code in this repository if not otherwise specified.

This project is developed using Laravel which is released under the `MIT License`.

`SIL Open Font License 1.1` is applied to the Titillium font included from CSS files.
