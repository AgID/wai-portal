# Analytics Italia (working title)

## Development

### Local environment

#### Requirements
- php >= 7.1
- composer
- node (nodenv)
- docker

#### Getting started
```php
composer install
bin/phing build
```

#### Containers port open on localhost
- web (nginx) => port 80 and 443
- mail (mailhog) => port 8025
- analytics service (matomo) => port 8090 and 9443


#### Available tasks
```php
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
