# Comece com a mesma imagem base que estávamos usando
FROM php:8.2-apache

# Instale as extensões PHP necessárias para MySQL
RUN docker-php-ext-install mysqli pdo_mysql && docker-php-ext-enable pdo_mysql

# Define o charset padrão para UTF-8 em todo o ambiente
COPY docker/app/charset.ini /usr/local/etc/php/conf.d/charset.ini

# Cria o diretório de logs e defina o usuário do apache (www-data) como dono
RUN mkdir -p /var/www/html/logs && chown www-data:www-data /var/www/html/logs
    