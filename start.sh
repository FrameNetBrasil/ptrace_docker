#!/usr/bin/env bash

echo "running start.sh"
#composer install --ignore-platform-reqs
php /home/src/server-ws.php &
php /home/src/server-trace.php