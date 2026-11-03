FROM php:8.2-apache

# Habilita mod_rewrite
RUN a2enmod rewrite

# Instala APENAS o que não vem na imagem oficial
RUN apt-get update && apt-get install -y \
        libonig-dev \
    && docker-php-ext-install mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copia os arquivos do projeto
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Render exige escutar em 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:8080>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]
