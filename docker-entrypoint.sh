#!/bin/bash
set -e

# Railway (and similar PaaS) inject a PORT env var the container must listen
# on, rather than the default 80 - rewrite Apache's config to match.
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/" /etc/apache2/sites-enabled/000-default.conf

exec "$@"
