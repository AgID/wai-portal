# Web Analytics Italia

[![CircleCI](https://circleci.com/gh/AgID/wai-portal.svg?style=svg)](https://circleci.com/gh/agid/wai-portal)

## Development

### Local environment

#### Requirements

- php >= 7.3 (extensions requirements can be discovered after running composer)
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

```shell
bin/phing build               # build the portal
bin/phing test                # perform tests
bin/phing clean               # delete containers and data
bin/phing stop                # stop containers
bin/phing start               # start containers
bin/phing pma                 # start phpMyAdmin container (not started with bin/phing start)
bin/phing kibana              # start Kibana container (not started with bin/phing start)
bin/phing sentinel            # start Redis Sentinel container (not started with bin/phing start)
bin/phing redis-commander     # start Redis Commander container (not started with bin/phing start)
bin/phing build-portal-image  # build a docker image for the portal application
bin/phing build-matomo-image  # build a docker image for matomo
```

#### SPID authentication

This project use the [SPID Laravel](https://github.com/italia/spid-laravel) package.
Refer to its [README](https://github.com/italia/spid-laravel/blob/master/README.md) file for configuring and testing.

## Licenses

`AGPL-3.0-or-later License` is generally applied to all the code in this repository if not otherwise specified.

This project is developed using Laravel which is released under the `MIT License`.

`SIL Open Font License 1.1` is applied to the Titillium font included from CSS files.
