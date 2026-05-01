# Étape 1 : Utiliser l'image PHP avec Apache
FROM php:8.2-apache

# Étape 2 : Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo_mysql mysqli
RUN docker-php-ext-enable pdo_mysql

# Étape 3 : Activer mod_rewrite d'Apache (pour les belles URLs)
RUN a2enmod rewrite

# Étape 4 : Configurer le répertoire de travail
WORKDIR /var/www/html

# Étape 5 : Copier tous les fichiers du projet
COPY . /var/www/html/

# Étape 6 : Donner les permissions (Render utilise www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Étape 7 : Configurer Apache pour forcer le passage par index.php
RUN echo '<Directory /var/www/html/> \
    Options Indexes FollowSymLinks \
    AllowOverride All \
    Require all granted \
    </Directory>' > /etc/apache2/sites-available/000-default.conf

# Étape 8 : Exposer le port (Render utilise le port 10000 souvent)
EXPOSE 80

# Étape 9 : Démarrer Apache
CMD ["apache2-foreground"]
