# Mechanical FIX

Sistema web completo para la gestión de servicios de mecánicos a domicilio, desarrollado en PHP con arquitectura MVC y Bootstrap.

## 🚀 Características

- **Arquitectura MVC**: Estructura organizada y escalable
- **Multi-usuario**: Diferentes roles (Admin, Coordinador, Mecánico, Cliente)
- **Responsive**: Diseño adaptativo con Bootstrap 5
- **Geolocalización**: Integración con Google Maps API
- **Gestión completa**: Desde solicitud hasta facturación
- **Reportes**: Analytics y exportación de datos
- **Seguridad**: Autenticación, autorización y validaciones

## 📋 Requisitos del Sistema

- PHP 8.0 o superior
- MySQL 8.0 o superior
- Apache/Nginx
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - JSON
  - OpenSSL
  - mbstring

## 🛠️ Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/danjohn007/Ariel.git
cd Ariel
```

### 2. Configurar la base de datos
```sql
-- Crear la base de datos
CREATE DATABASE mechanical_fix CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar el esquema
mysql -u root -p mechanical_fix < config/database.sql
```

### 3. Configurar el archivo de configuración
Editar `config/config.php` con tus datos:

```php
// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'mechanical_fix');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');

// Configurar APIs (opcional)
define('GOOGLE_MAPS_API_KEY', 'tu_api_key');
define('STRIPE_PUBLISHABLE_KEY', 'tu_stripe_key');
```

### 4. Configurar el servidor web

#### Apache
Crear archivo `.htaccess` en la raíz:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
Configurar el servidor:
```nginx
server {
    listen 80;
    server_name mechanicalfix.local;
    root /path/to/Ariel;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. Configurar permisos
```bash
# Dar permisos de escritura a la carpeta uploads
chmod 755 uploads/
chown www-data:www-data uploads/
```

## 🎯 Usuarios por Defecto

### Administrador
- **Email**: admin@mechanicalfix.com
- **Contraseña**: admin123

## 📱 Módulos del Sistema

### 1. **Módulo de Solicitudes de Servicio**
- Formulario público para clientes
- Geolocalización con Google Maps
- Subida de archivos (fotos/videos)
- Generación de folio único

### 2. **Módulo de Administración**
- Gestión de solicitudes
- Asignación de mecánicos
- Control de estados
- Filtros y búsquedas

### 3. **Módulo del Mecánico**
- Dashboard de servicios asignados
- Check-in GPS
- Reportes de trabajo
- Firma digital del cliente

### 4. **Módulo del Cliente**
- Historial de servicios
- Seguimiento en tiempo real
- Calificaciones y reseñas
- Descargar comprobantes

### 5. **Módulo de Cotizaciones y Pagos**
- Cotizaciones automáticas
- Integración con pasarelas de pago
- Gestión de facturas
- Registro de pagos

### 6. **Módulo de Reportes**
- Analytics de servicios
- Reportes de ingresos
- Rendimiento por mecánico
- Exportación a Excel/PDF

### 7. **Módulo de Seguridad**
- Autenticación por roles
- Control de permisos
- Bitácora de actividades
- Recuperación de contraseñas

## 🔧 Uso del Sistema

### Para Clientes
1. Acceder a la página principal
2. Hacer clic en "Solicitar Servicio"
3. Llenar el formulario con datos del vehículo
4. Especificar ubicación y problema
5. Recibir confirmación y seguimiento

### Para Coordinadores
1. Iniciar sesión en el sistema
2. Ver solicitudes pendientes
3. Asignar mecánicos disponibles
4. Dar seguimiento a servicios

### Para Mecánicos
1. Acceder al dashboard
2. Ver servicios asignados
3. Hacer check-in al llegar
4. Completar reporte de trabajo
5. Obtener firma del cliente

### Para Administradores
1. Gestión completa del sistema
2. Administrar usuarios
3. Configurar tipos de servicio
4. Ver reportes y analytics

## 🔐 Seguridad

- Validación y sanitización de datos
- Protección CSRF
- Contraseñas hasheadas
- Control de acceso por roles
- Logging de actividades

## 📊 API Endpoints

El sistema incluye endpoints para:
- Gestión de servicios
- Actualización de ubicación
- Notificaciones
- Reportes

## 🎨 Personalización

### Temas
Modificar `public/css/style.css` para personalizar:
- Colores principales
- Tipografía
- Espaciado
- Animaciones

### Configuraciones
Ajustar en `config/config.php`:
- Límites de archivos
- Configuración de email
- APIs externas
- Zona horaria

## 🚀 Despliegue

### Producción
1. Configurar `APP_ENV` a `'production'`
2. Desactivar `display_errors`
3. Configurar HTTPS
4. Establecer claves seguras
5. Configurar backups

### Docker (Opcional)
```dockerfile
# Crear Dockerfile para containerización
FROM php:8.0-apache
COPY . /var/www/html/
RUN docker-php-ext-install pdo pdo_mysql
```

## 🔧 Troubleshooting

### Problemas Comunes

1. **Error de conexión a BD**
   - Verificar credenciales en `config/config.php`
   - Confirmar que MySQL esté ejecutándose

2. **Error 404 en rutas**
   - Verificar configuración de mod_rewrite
   - Revisar archivo `.htaccess`

3. **Permisos de archivo**
   - Verificar permisos en carpeta `uploads/`
   - Confirmar ownership del servidor web

## 📝 Contribuir

1. Fork del proyecto
2. Crear rama para feature (`git checkout -b feature/nueva-caracteristica`)
3. Commit cambios (`git commit -am 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Crear Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico:
- Email: support@mechanicalfix.com
- Documentación: [Wiki del proyecto]
- Issues: [GitHub Issues]

## 🙏 Agradecimientos

- Bootstrap por el framework CSS
- Google Maps por la API de geolocalización
- FontAwesome por los iconos
- Comunidad PHP por las mejores prácticas

---

**Mechanical FIX** - Servicio profesional de mecánicos a domicilio 🔧