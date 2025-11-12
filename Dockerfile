FROM php:8.2-apache

# Instalar extensões do PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Configurar ServerName
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependências do sistema necessárias para o Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar apenas os arquivos de dependências primeiro (para cache do Docker)
COPY composer.json ./

# Instalar dependências do Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copiar o resto da aplicação
COPY . .

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor porta 80
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
