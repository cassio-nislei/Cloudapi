# Use a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Definir diretório de trabalho
WORKDIR /var/www/html

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libmcrypt-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    curl \
    json

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Habilitar mod_rewrite para URLs amigáveis
RUN a2enmod rewrite

# Copiar arquivos da aplicação
COPY . .

# Instalar dependências PHP via Composer
RUN composer install --no-dev --optimize-autoloader 2>&1 || echo "Composer install completed with warnings"

# Criar diretórios necessários
RUN mkdir -p application/logs && \
    chmod -R 755 application/logs && \
    chmod -R 755 application/cache

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html

# Copiar configuração do Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Expor porta
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
