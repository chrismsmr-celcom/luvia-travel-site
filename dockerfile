FROM php:8.2-fpm

# Installer nginx
RUN apt-get update && apt-get install -y nginx \
    && docker-php-ext-install pdo_mysql mysqli \
    && apt-get clean

# Configurer PHP-FPM
RUN echo "listen = 9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf

# Configurer Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Copier les fichiers
WORKDIR /var/www/html
COPY . /var/www/html/

# Script de démarrage
RUN echo '#!/bin/bash\n\
php-fpm -D\n\
nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
