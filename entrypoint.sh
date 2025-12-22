#!/bin/bash
set -e

echo "🚀 Starting CodeIgniter 4 Container..."

# 1. Create Required Folders (Fix for empty Docker Volumes)
# When a volume is mounted, these folders might be missing. We must recreate them.
echo "📂 Ensuring writable directories exist..."
mkdir -p /var/www/html/writable/cache
mkdir -p /var/www/html/writable/logs
mkdir -p /var/www/html/writable/session
mkdir -p /var/www/html/writable/uploads
mkdir -p /var/www/html/writable/database

# 2. Fix Permissions
echo "🔧 Fixing permissions for writable folders..."
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable
chown -R www-data:www-data /var/www/html/public
chmod -R 775 /var/www/html/public

# 3. SQLite Setup (Prevents errors if switching drivers)
if [ ! -f /var/www/html/writable/database/database.sqlite ]; then
    echo "📂 Creating SQLite database file..."
    touch /var/www/html/writable/database/database.sqlite
    chown www-data:www-data /var/www/html/writable/database/database.sqlite
fi

# 4. Wait for Database (MySQL) - Retry Logic
echo "⏳ Waiting for Database connection..."
MAX_TRIES=30
COUNT=0

while [ $COUNT -lt $MAX_TRIES ]; do
    php -r "
        \$host = getenv('database.default.hostname') ?: 'fullstack-codeigniter-mysql';
        \$port = getenv('database.default.port') ?: 3306;
        \$fp = @fsockopen(\$host, \$port, \$errno, \$errstr, 1);
        if (\$fp) { fclose(\$fp); exit(0); }
        exit(1);
    "
    if [ $? -eq 0 ]; then
        echo "✅ Database is ready!"
        break
    fi
    echo "zzz Waiting for Database... ($COUNT/$MAX_TRIES)"
    sleep 2
    COUNT=$((COUNT+1))
done

# 5. Run Migrations (Force 'Yes' for Production mode)
echo "🔄 Running Database Migrations..."
echo y | php spark migrate --all

# 6. Start Apache
echo "✅ Starting Apache on port 5005..."
exec apache2-foreground