#!/usr/bin/env bash
dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd )"
parentdir="$(dirname "$dir")"
siteroot="$( cd "$( dirname "$parentdir" )" >/dev/null && pwd )"

#Export Nodes
echo Begin Node Export!!!
drush dce node 1 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node1.json
drush dce node 2 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node2.json
drush dce node 3 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node3.json
drush dce node 4 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node4.json
drush dce node 5 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node5.json
drush dce node 6 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node6.json
drush dce node 7 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node7.json
drush dce node 8 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node8.json
drush dce node 9 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node9.json
drush dce node 10 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node10.json
echo 10 Nodes Complete...
drush dce node 11 --file=$siteroot/web/modules/custom/cor_configuration_base_data/content/node/node11.json
echo Completed Node Export!!!