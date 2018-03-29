# Web Analytics Italia

[![CircleCI](https://img.shields.io/circleci/project/github/teamdigitale/webanalytics-onboarding.svg?colorB=0066cc)](https://circleci.com/gh/teamdigitale/webanalytics-onboarding)

## Development

### Local environment

#### Requirements
- php >= 7.1
- [composer](https://getcomposer.org/)
- node ([nodenv](https://github.com/nodenv/nodenv))
- docker and docker-compose

#### Getting started
- Refer to [build.properties.example](env/build.properties.example) file
  for initial configuration.
- Run:
  ```
  composer install
  bin/phing build
  ```

#### Ports open on docker host
- web (nginx) => port 80/http and 443/https
- mail (mailhog) => port 8025/http
- analytics service (matomo) => port 8090/http and 9443/https
- phpMyAdmin => port 8080/http (not started automatically)

#### Available tasks
```
bin/phing test          # perform tests
bin/phing ws            # enter workspace container
bin/phing clean         # delete containers and data
bin/phing clean-deep    # delete containers, images and data
bin/phing stop          # stop containers
bin/phing start         # start containers
bin/phing pma           # start phpMyAdmin container
```

#### SPID authentication
This project use the [SPID Laravel](https://github.com/italia/spid-laravel) package.
Refer to its [README](https://github.com/italia/spid-laravel/blob/master/README.md) file for configuring and testing.

## Deployment

## Licenses

`AGPL-3.0-or-later License` is generally applied to all the code in this repository if not otherwise specified.

This project is developed using Laravel which is released under the `MIT License`.

`SIL Open Font License 1.1` is applied to the Titillium font included from CSS files.
