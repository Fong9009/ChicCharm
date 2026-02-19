#!/bin/bash
set -e
# Railway sets PORT; Apache defaults to 80. Listen on PORT so the proxy can connect.
PORT="${PORT:-80}"
echo "Listen ${PORT}" > /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf
exec apache2-foreground
