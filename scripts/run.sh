#!/usr/bin/env sh

crond
tail -f /var/log/cron.log &
exec /app/bin/web
