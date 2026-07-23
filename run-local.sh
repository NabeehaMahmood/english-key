#!/usr/bin/env bash
# Local dev launcher — Talha's Linux box (system MariaDB + dedicated ekauser).
# Local helper only; teammates on XAMPP don't need it (do not commit).
cd "$(dirname "$0")"
export MYSQLHOST=127.0.0.1 MYSQLPORT=3306 MYSQLDATABASE=academy \
       MYSQLUSER=ekauser MYSQLPASSWORD=ekapass123 \
       APP_DEBUG=true SITE_URL=http://127.0.0.1:8000
echo "======================================================"
echo "  EnglishKeys running at:  http://127.0.0.1:8000"
echo "  Admin panel:             http://127.0.0.1:8000/admin/    (admin / ChangeMe123!)"
echo "  Press Ctrl+C to stop."
echo "======================================================"
exec php -S 127.0.0.1:8000 router.php
