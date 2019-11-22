sudo php -d memory_limit=2048M /usr/local/bin/composer require "flexxia/flexservice:1.2.1"

sudo composer require "flexxia/flexservice:>1.2.1"

sudo composer require flexxia/flexservice

composer require vendor/package dev-master#0d7d6c88


### to add on composer.json for project
"extra": {
    "installer-paths": {
        "web/core": ["type:drupal-core"],
        "web/libraries/{$name}": ["type:drupal-library"],
        "web/modules/contrib/{$name}": ["type:drupal-module"],
        "web/profiles/contrib/{$name}": ["type:drupal-profile"],
        "web/themes/contrib/{$name}": ["type:drupal-theme"],
        "web/modules/custom/{$name}": ["type:drupal-custom-module"],
        "drush/contrib/{$name}": ["type:drupal-drush"]
    }
}


###
sudo composer require --dev flexxia/flexservice:dev-master
sudo php -d memory_limit=2048M /usr/local/bin/composer require --dev "flexxia/flexservice:dev-master"
sudo php -d memory_limit=2048M /usr/local/bin/composer require "flexxia/flexservice dev-master#0d7d6c88"


###
sudo composer remove flexxia/flexservice
sudo php -d memory_limit=2048M /usr/local/bin/composer remove flexxia/flexservice

composer show flexxia/flexservice


/**
 *
 */
sudo git update-index --assume-unchanged web/modules/custom/flexservice/

git rm -r --cached web/modules/custom/flexservice/*

git add -f web/modules/custom/flexservice


/**
 *
 */
sudo cp -rp source/web/libraries/primeng7app/. new/web/libraries/primeng7app/

/**
 *
 */
sudo git rm --cached web/modules/custom/flexrepo
sudo rm -rf .git/modules/web/modules/custom/flexrepo
sudo rm -rf web/modules/custom/flexrepo

sudo nano .gitmodules

sudo nano .git/config
