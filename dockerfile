FROM php:8.2-apache

# Installer les extensions PHP
RUN docker-php-ext-install pdo_mysql mysqli

# Activer mod_rewrite
RUN a2enmod rewrite

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers
COPY . /var/www/html/

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Pas besoin de configurer manuellement Apache !
# La configuration par défaut fonctionne très bien

EXPOSE 80

CMD ["apache2-foreground"]
