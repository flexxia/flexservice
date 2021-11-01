<!--  -->
sudo composer require flexxia/flexservice

### to add on composer.json for project
######################
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

# drush
vendor/drush/drush/drush cr

######################
composer require --dev flexxia/flexservice:dev-master

sudo php -d memory_limit=-1 /Users/Dong/Documents/app/composer/1.10.19/composer.phar require --dev "flexxia/flexservice:dev-master"

#
 --ignore-platform-reqs

sudo php -d memory_limit=4096M /usr/local/bin/composer require --dev "flexxia/flexservice:dev-master"

## 指定版本
sudo php -d memory_limit=4096M /usr/local/bin/composer require "flexxia/flexservice dev-master#0d7d6c88"


######################
# Remove
sudo composer remove flexxia/flexservice


######################
# show on packagist
composer show flexxia/flexservice

sudo composer config -g repo.packagist composer https://packagist.org

<!--  -->
# 删除
sudo rm -r web/modules/custom/flexservice/.git

sudo git rm -r --cached web/modules/custom/flexservice/*
sudo git rm -r --cached web/modules/custom/flexservice
sudo git rm -r -f --cached web/modules/custom/flexservice

sudo git add -f web/modules/custom/flexservice
sudo git add -f web/modules/custom/flexservice/*

sudo git update-index --really-refresh

sudo git update-index --assume-unchanged web/modules/custom/flexservice/
sudo git update-index --no-assume-unchanged web/modules/custom/flexservice/


<!--  -->
# .gitignore
# Do not Ignore flexservice folder
######################
!web/modules/custom/flexservice/
!web/modules/custom/flexservice**/*
!web/modules/custom/flexservice/*


<!--  -->
# @deprecated
sudo git rm --cached web/modules/custom/flexrepo
sudo rm -rf .git/modules/web/modules/custom/flexrepo
sudo rm -rf web/modules/custom/flexrepo

sudo nano .gitmodules

sudo nano .git/config
