#!/bin/sh
git pull
php app/console --env=prod cache:clear
php app/console  assets:install
php app/console  assetic:dump
php app/console  doctrine:schema:update --dump-sql
echo 'for apply DB changes - run the command: php app/console  doctrine:schema:update --force'
# php app/console  statr:table:update --dump-sql
echo ''
echo 'for apply DB changes - run the command: php app/console  statr:table:update --dump-sql  --force'

