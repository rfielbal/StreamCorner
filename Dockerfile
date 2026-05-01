FROM php:8.2-apache

# 1. ZONE ROOT : Installations de base
RUN apt-get update && apt-get install -y git unzip libicu-dev \
    && docker-php-ext-install intl pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- 2. LA CONFIGURATION APACHE DE TON COURS ---
# On active d'abord l'URL rewriting (souvent requis par Symfony)
RUN a2enmod rewrite

# On écrit exactement la configuration demandée par ton prof dans le bon fichier
RUN echo "Alias /streamCorner /var/www/html/streamCorner/public\n\
<Directory /var/www/html/streamCorner/public>\n\
    AllowOverride All\n\
    Order Allow,Deny\n\
    Allow from All\n\
</Directory>" > /etc/apache2/conf-available/streamCorner.conf

# On active l'alias (comme ton a2enconf elgato.conf)
RUN a2enconf streamCorner.conf

# (Pas besoin de systemctl reload apache2, car Docker démarre Apache après cette étape !)
# ------------------------------------------------

# 3. CRÉATION DU COMPTE "LOGIN"
RUN useradd -m login

# 4. ZONE LOGIN (Utilisateur)
# Attention ici : ton projet se trouvera dans le dossier "elgato"
WORKDIR /var/www/html/StreamCorner
USER login
