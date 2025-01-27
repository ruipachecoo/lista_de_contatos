FROM php:8.1-apache

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto
COPY public/ /var/www/html/public/
COPY config/ /var/www/html/config/
COPY routes/ /var/www/html/routes/

# Criar diretório de logs do PHP
RUN mkdir -p /var/log

# Criar arquivo de logs do PHP
RUN touch /var/log/php_errors.log

# Definir dono e grupo do diretório de logs do PHP
RUN chown -R www-data:www-data /var/log

# Definir permissões do diretório de logs do PHP
RUN chmod 775 /var/log

# Definir dono e grupo do arquivo de logs do PHP
RUN chown www-data:www-data /var/log/php_errors.log

# Definir permissões do arquivo de logs do PHP
RUN chmod 774 /var/log/php_errors.log 

# Criar diretório de logs do Apache
RUN mkdir -p /var/log/apache2

# Criar arquivos de logs do Apache
RUN touch /var/log/apache2/error.log && \
    touch /var/log/apache2/access.log && \
    touch /var/log/apache2/other_vhosts_access.log

# Definir dono e grupo dos logs do Apache
RUN chown -R www-data:www-data /var/log/apache2 && \
    chown www-data:www-data /var/log/apache2/error.log && \
    chown www-data:www-data /var/log/apache2/access.log && \
    chown www-data:www-data /var/log/apache2/other_vhosts_access.log

# Definir permissões do diretório e arquivos do Apache
RUN chmod -R 775 /var/log/apache2 && \
    chmod 774 /var/log/apache2/error.log && \
    chmod 774 /var/log/apache2/access.log && \
    chmod 774 /var/log/apache2/other_vhosts_access.log

# Atualizar a configuração do Apache
COPY apache-config/000-default.conf /etc/apache2/sites-available/000-default.conf

# Habilitar o mod_rewrite do Apache
RUN a2enmod rewrite

# Carregar configurações de sites
RUN a2ensite 000-default.conf

# Instalar a extensões
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Expor a porta 80
EXPOSE 80

