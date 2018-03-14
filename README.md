# Analytics Italia (working title)

[![CircleCI](https://img.shields.io/circleci/project/github/teamdigitale/piwik-onboarding.svg?colorB=0066cc)](https://circleci.com/gh/teamdigitale/piwik-onboarding)

## Development

### Local environment

#### Requirements
- php >= 7.1
- composer
- node (nodenv)
- docker

#### Getting started
```
export APP_ENV=local
composer install
bin/phing build
```

#### Containers port open on localhost
- web (nginx) => port 80/http and 443/https
- mail (mailhog) => port 8025/http
- analytics service (matomo) => port 8090/http and 9443/https
- phpMyAdmin => port 8080/http (not started automatically)

#### Matomo container
Login with `root` / `matomo`


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

#### SPID identities
This project is not yet configured as a SPID Service Provider.
You can use the following fake SPID identities only with the SPID Test Identity Provider.
```
analytics_italia_1 / analytics_italia_1
analytics_italia_2 / analytics_italia_2
analytics_italia_3 / analytics_italia_3
```

## Licenses

`AGPL-3.0-or-later License` is generally applied to all the code in this repository if not otherwise specified.

This project is developed using Laravel which is released under the `MIT License`.

`SIL Open Font License 1.1` is applied to the Titillium font included from CSS files.
