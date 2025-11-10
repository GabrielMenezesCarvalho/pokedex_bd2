# Comece com a mesma imagem base que estávamos usando
FROM php:8.2-apache

# Instale as extensões PHP necessárias para MySQL
# docker-php-ext-install é um script oficial para isso
RUN docker-php-ext-install mysqli pdo_mysql && docker-php-ext-enable pdo_mysql

# Define o charset padrão para UTF-8 em todo o ambiente
COPY docker/app/charset.ini /usr/local/etc/php/conf.d/charset.ini