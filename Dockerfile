FROM php:8.2-apache

# 1. ZONE ROOT : Installations de base
RUN apt-get update && apt-get install -y git unzip libicu-dev \
    && docker-php-ext-install intl pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- 2. LA CONFIGURATION APACHE DE TON COURS ---
# On active d'abord l'URL rewriting (souvent requis par Symfony)
RUN a2enmod rewrite

# On écrit exactement la configuration demandée par ton prof dans le bon fichier

RUN echo "Alias /assets /var/www/html/StreamCorner/public/assets\n\
<Directory /var/www/html/StreamCorner/public/assets>\n\
    AllowOverride All\n\
    Order Allow,Deny\n\
    Allow from All\n\
</Directory>\n\
\n\
Alias /streamCorner /var/www/html/StreamCorner/public\n\
<Directory /var/www/html/StreamCorner/public>\n\
    AllowOverride All\n\
    Order Allow,Deny\n\
    Allow from All\n\
</Directory>" > /etc/apache2/conf-available/streamCorner.conf

# On active l'alias
RUN a2enconf streamCorner.conf

# 3. CRÉATION DU COMPTE "LOGIN"
RUN useradd -m login

# 4. ZONE LOGIN (Utilisateur)
WORKDIR /var/www/html/StreamCorner
USER login
