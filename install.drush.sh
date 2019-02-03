#!/bin/bash

while getopts ":g:i:j:n:r:o:d:e:t:u:h:x:s:" opt; do
  case ${opt} in
    g )
      DBHOST=$OPTARG
      ;;
    i )
      DBUSER=$OPTARG
      ;;
    j )
      DBPASS=$OPTARG
      ;;
    n )
      DBNAME=$OPTARG
      ;;
    r )
      RDHOST=$OPTARG
      ;;
    o )
      RDNUMBER=$OPTARG
      ;;
    d )
      DNAME=$OPTARG
      ;;
    e )
      DPASS=$OPTARG
      ;;
    t )
      DSITENAME=$OPTARG
      ;;
    u )
      DSITEEMAIL=$OPTARG
      ;;
    x )
      DELETESETTING=$OPTARG
      ;;
    s )
      SUBSITE=$OPTARG
      ;;
    h )
      echo "Command Line Options"
      echo "-g Database Host"
      echo "-i Database Username"
      echo "-j Database Password"
      echo "-n Databasse name"
      echo "-r Redis Host (Optional, default: localhost)"
      echo "-o Redis Number (Optional, default: 1)"
      echo "-d Drupal Admin Name"
      echo "-e Drupal Admin Password"
      echo "-t Drupal Sitename"
      echo "-u Drupal Site email address"
      echo "-x Set to \"yes\" to delete the existing settings files. Default to no. No quotes around the value"
      echo "-s Subsite Directory. If this is specified only a subsite will be installed on an existing site. If no existing site it will fail."
      exit 1
      ;;
    \? )
      echo "Invalid Option: -$OPTARG" 1>&2
      exit 1
      ;;
  esac
done

# This determines the location of this script.
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
DWD=${DIR}

if [[ -z "${DELETESETTING// }" ]] || [[ ! -z "${SUBSITE// }" ]]; then
  DELETESETTING="no"
fi

if [[ "$DELETESETTING" = "yes" ]] ; then
  chmod -R 777 ${DWD}/web/sites/default/
  rm ${DWD}/web/sites/default/settings.php
fi

SET0="#Redis Settings"
SET1="\$settings['redis.connection']['interface'] = 'PhpRedis';"
SET2="\$settings['redis.connection']['host'] = 'redisAddress';"
SET3="\$settings['cache']['default'] = 'cache.backend.redis';"
SET4="\$settings['redis.connection']['base'] = redisBaseID;"

# Composer install is run as this will load what is in the composer.lock

if [[ -z "${SUBSITE// }" ]]; then
  composer install --no-dev
fi

# Set defaults on Redis.
if [[ -z "${RDHOST// }" ]]; then
  RDHOST="localhost"
fi
if [[ -z "${RDNUMBER// }" ]]; then
  RDNUMBER="1"
fi

SET2_result="${SET2/redisAddress/$RDHOST}"
SET4_result="${SET4/redisBaseID/$RDNUMBER}"

REDIS_SETTINGS=$'\n'"${SET0}"$'\n'"${SET1}"$'\n'"${SET2_result}"$'\n'"${SET3}"$'\n'"${SET4_result}"

if [[ -z "${SUBSITE// }" ]]; then
  echo "Base Site Installation"
  ## Standard site install
  drush site-install corona_standard -y \
  --site-name=$DSITENAME \
  --site-mail=$DSITEEMAIL \
  --account-name=$DNAME \
  --account-pass=$DPASS \
  --account-mail=$DSITEEMAIL \
  --db-url=mysql://$DBUSER:$DBPASS@$DBHOST/$DBNAME -v
else
  echo "Subsite Installation"
  drush site-install -y --sites-subdir=$SUBSITE \
  --db-url=mysql://$DBUSER:$DBPASS@$DBHOST/$DBNAME \
  --account-name=$DNAME \
  --account-pass=$DPASS -v
  exit 0
fi

chmod 777 ${DWD}/web/sites/default
chmod 644 ${DWD}/web/sites/default/settings.php
# echo "$REDIS_SETTINGS" >> ${DWD}/web/sites/default/settings.php
chmod 444 ${DWD}/web/sites/default/settings.php
chmod 555 ${DWD}/web/sites/default

## Cleanup and delete text files
#rm ${DWD}/web/INSTALL.txt
#rm ${DWD}/web/README.txt
#rm ${DWD}/web/core/CHANGELOG.txt
#rm ${DWD}/web/core/COPYRIGHT.txt
#rm ${DWD}/web/core/INSTALL.mysql.txt
#rm ${DWD}/web/core/INSTALL.pgsql.txt
#rm ${DWD}/web/core/INSTALL.sqlite.txt
#rm ${DWD}/web/core/INSTALL.txt
#rm ${DWD}/web/core/MAINTAINERS.txt
#rm ${DWD}/web/core/UPDATE.txt
#
## Rename the License file to stay compliant but not easily found
#mv ${DWD}/web/core/LICENSE.txt ${DWD}/web/core/license-file.txt

echo "Optimize Composer Autoloader"
cd ${DWD}
composer dump-autoload --optimize
echo "Cleaning all caches."
# Last minute cleanse.
drush cr
