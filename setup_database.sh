#!/bin/bash

# Script de instalación para Sistema de Análisis de Precios y Programa de Obra
# PHP + MySQL

echo "========================================================"
echo "Sistema de Análisis de Precios y Programa de Obra"
echo "Script de Instalación"
echo "========================================================"

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
show_message() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

show_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

show_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

show_step() {
    echo -e "\n${BLUE}[STEP]${NC} $1"
}

# Verificar si el script se ejecuta como root (para algunos comandos)
check_permissions() {
    if [[ $EUID -eq 0 ]]; then
        show_warning "Ejecutándose como root. Algunos archivos pueden requerir cambios de permisos."
    fi
}

# Verificar dependencias del sistema
check_dependencies() {
    show_step "Verificando dependencias del sistema..."
    
    # Verificar PHP
    if ! command -v php &> /dev/null; then
        show_error "PHP no está instalado. Por favor instale PHP 8.0 o superior."
        exit 1
    fi
    
    PHP_VERSION=$(php -v | head -1 | cut -d ' ' -f 2 | cut -d '.' -f 1,2)
    show_message "PHP versión encontrada: $PHP_VERSION"
    
    # Verificar MySQL/MariaDB
    if ! command -v mysql &> /dev/null; then
        show_error "MySQL/MariaDB no está instalado. Por favor instale MySQL 8.0+ o MariaDB 10.4+."
        exit 1
    fi
    
    MYSQL_VERSION=$(mysql --version)
    show_message "MySQL/MariaDB encontrado: $MYSQL_VERSION"
    
    # Verificar extensiones PHP requeridas
    show_message "Verificando extensiones PHP..."
    
    required_extensions=("pdo" "pdo_mysql" "mbstring" "json" "session")
    
    for ext in "${required_extensions[@]}"; do
        if php -m | grep -q "^$ext$"; then
            show_message "✓ Extensión $ext: instalada"
        else
            show_error "✗ Extensión $ext: NO instalada"
            exit 1
        fi
    done
}

# Configurar base de datos
setup_database() {
    show_step "Configurando base de datos..."
    
    # Solicitar credenciales de MySQL
    echo -n "Ingrese el host de MySQL [localhost]: "
    read db_host
    db_host=${db_host:-localhost}
    
    echo -n "Ingrese el puerto de MySQL [3306]: "
    read db_port
    db_port=${db_port:-3306}
    
    echo -n "Ingrese el usuario de MySQL [root]: "
    read db_user
    db_user=${db_user:-root}
    
    echo -n "Ingrese la contraseña de MySQL: "
    read -s db_password
    echo
    
    echo -n "Ingrese el nombre de la base de datos [construccion_db]: "
    read db_name
    db_name=${db_name:-construccion_db}
    
    # Verificar conexión
    show_message "Verificando conexión a MySQL..."
    
    if mysql -h"$db_host" -P"$db_port" -u"$db_user" -p"$db_password" -e "SELECT 1;" &> /dev/null; then
        show_message "✓ Conexión a MySQL exitosa"
    else
        show_error "✗ No se pudo conectar a MySQL. Verifique las credenciales."
        exit 1
    fi
    
    # Crear base de datos y ejecutar migraciones
    show_message "Creando base de datos y ejecutando migraciones..."
    
    mysql -h"$db_host" -P"$db_port" -u"$db_user" -p"$db_password" < "migrations/001_create_database.sql"
    
    if [ $? -eq 0 ]; then
        show_message "✓ Base de datos creada exitosamente"
    else
        show_error "✗ Error al crear la base de datos"
        exit 1
    fi
    
    # Insertar datos de prueba
    show_message "Insertando datos de prueba..."
    
    mysql -h"$db_host" -P"$db_port" -u"$db_user" -p"$db_password" "$db_name" < "migrations/002_insert_test_data.sql"
    
    if [ $? -eq 0 ]; then
        show_message "✓ Datos de prueba insertados exitosamente"
    else
        show_warning "⚠ Error al insertar datos de prueba (la base de datos principal fue creada)"
    fi
    
    # Guardar configuración en .env
    show_message "Guardando configuración de base de datos..."
    
    # Crear archivo .env desde el ejemplo
    cp .env.example .env
    
    # Actualizar configuración de base de datos
    sed -i "s/DB_HOST=localhost/DB_HOST=$db_host/" .env
    sed -i "s/DB_PORT=3306/DB_PORT=$db_port/" .env
    sed -i "s/DB_NAME=construccion_db/DB_NAME=$db_name/" .env
    sed -i "s/DB_USER=root/DB_USER=$db_user/" .env
    sed -i "s/DB_PASS=/DB_PASS=$db_password/" .env
    
    # Generar claves de seguridad
    encryption_key=$(openssl rand -hex 32)
    password_salt=$(openssl rand -hex 16)
    
    sed -i "s/ENCRYPTION_KEY=your-secret-key-here-development/ENCRYPTION_KEY=$encryption_key/" .env
    sed -i "s/PASSWORD_SALT=your-password-salt-here-development/PASSWORD_SALT=$password_salt/" .env
    
    show_message "✓ Archivo .env configurado"
}

# Configurar permisos
setup_permissions() {
    show_step "Configurando permisos de archivos..."
    
    # Hacer el directorio public como directorio web
    if [ -d "public" ]; then
        chmod 755 public
        show_message "✓ Permisos configurados para directorio public"
    fi
    
    # Permisos para archivos de configuración
    if [ -f ".env" ]; then
        chmod 600 .env
        show_message "✓ Permisos restringidos para archivo .env"
    fi
    
    # Permisos para directorios de datos
    chmod -R 755 src config migrations
    show_message "✓ Permisos configurados para directorios del sistema"
}

# Verificar configuración del servidor web
check_webserver() {
    show_step "Verificando configuración del servidor web..."
    
    show_message "Para completar la instalación, configure su servidor web para:"
    echo
    echo "1. Apuntar el DocumentRoot al directorio 'public/'"
    echo "2. Habilitar mod_rewrite (Apache) o configurar rewrites (Nginx)"
    echo "3. Asegurar que PHP esté habilitado"
    echo
    
    show_message "Ejemplos de configuración:"
    echo
    echo "=== APACHE VIRTUAL HOST ==="
    echo "<VirtualHost *:80>"
    echo "    ServerName construccion.local"
    echo "    DocumentRoot $(pwd)/public"
    echo "    DirectoryIndex index.php"
    echo "    <Directory $(pwd)/public>"
    echo "        AllowOverride All"
    echo "        Require all granted"
    echo "    </Directory>"
    echo "</VirtualHost>"
    echo
    
    echo "=== NGINX SERVER BLOCK ==="
    echo "server {"
    echo "    listen 80;"
    echo "    server_name construccion.local;"
    echo "    root $(pwd)/public;"
    echo "    index index.php;"
    echo ""
    echo "    location / {"
    echo "        try_files \$uri \$uri/ /index.php?\$query_string;"
    echo "    }"
    echo ""
    echo "    location ~ \.php\$ {"
    echo "        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;"
    echo "        fastcgi_index index.php;"
    echo "        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;"
    echo "        include fastcgi_params;"
    echo "    }"
    echo "}"
    echo
}

# Mostrar información de usuarios de prueba
show_test_users() {
    show_step "Usuarios de prueba disponibles:"
    echo
    echo "┌─────────────┬───────────────────────────────┬────────────┬─────────────────────────────────┐"
    echo "│ Rol         │ Email                         │ Contraseña │ Permisos                        │"
    echo "├─────────────┼───────────────────────────────┼────────────┼─────────────────────────────────┤"
    echo "│ Admin       │ admin@construccion.com        │ password   │ Acceso completo                 │"
    echo "│ Analista    │ analista@construccion.com     │ password   │ Gestión de obras y datos        │"
    echo "│ Visitante   │ visitante@construccion.com    │ password   │ Solo lectura                    │"
    echo "└─────────────┴───────────────────────────────┴────────────┴─────────────────────────────────┘"
    echo
}

# Función principal
main() {
    show_message "Iniciando instalación..."
    
    check_permissions
    check_dependencies
    setup_database
    setup_permissions
    check_webserver
    show_test_users
    
    echo
    echo "========================================================"
    show_message "¡Instalación completada exitosamente!"
    echo "========================================================"
    echo
    show_message "Próximos pasos:"
    echo "1. Configure su servidor web (Apache/Nginx)"
    echo "2. Apunte el DocumentRoot a: $(pwd)/public/"
    echo "3. Acceda al sistema en su navegador"
    echo "4. Use los usuarios de prueba para comenzar"
    echo
    show_warning "IMPORTANTE: Cambie las contraseñas de los usuarios de prueba en producción"
    echo
}

# Ejecutar función principal
main "$@"