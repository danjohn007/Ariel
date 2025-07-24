# Sistema Web de Análisis de Precios y Programa de Obra

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)

## 📋 Descripción

Aplicación web migrada desde hojas de cálculo Excel hacia un sistema en línea moderno para gestionar presupuestos, conceptos, avances, recursos y cronogramas de obras de construcción. Construido en PHP puro y MySQL, cuenta con autenticación basada en roles y panel de control interactivo.

Este sistema representa la evolución digital de la gestión constructiva, permitiendo mayor eficiencia, trazabilidad y seguridad en cada etapa del proceso.

## ✨ Funcionalidades Destacadas

### 🔒 Autenticación y Seguridad
- Sistema de login seguro con sesiones separadas por tipo de usuario
- Control de acceso por rol: Administrador, Analista, Visitante
- Protección contra SQL Injection mediante prepared statements
- Validación de formularios (cliente y servidor)
- Sanitización de salidas y protección XSS
- Logs de auditoría completos

### 🏗️ Gestión de Obras
- Gestión completa de obras y proyectos
- Seguimiento de conceptos, materiales, mano de obra y proveedores
- Cálculo automático de precios unitarios, volúmenes y avances
- Estados de proyecto (Activo, Pausado, Completado, Cancelado)
- Asignación de responsables por obra

### 📊 Análisis de Precios
- Análisis detallado de precios unitarios por concepto
- Desglose por materiales, mano de obra y maquinaria
- Cálculo automático de costos indirectos y utilidad
- Gestión de proveedores y cotizaciones
- Historial de precios y variaciones

### 📅 Programa de Obra
- Programa de obra con estructura escalable
- Visualización por períodos (semanal, quincenal, mensual)
- Seguimiento de avances físicos y financieros
- Comparativo entre programado vs ejecutado
- Base preparada para calendario o diagrama de Gantt

### 📈 Dashboard y Reportes
- Dashboard interactivo con indicadores clave
- Estadísticas en tiempo real de obras y avances
- Reportes de progreso y rendimiento
- Visualización de datos con gráficos
- Actividad reciente del sistema

### 👥 Gestión de Usuarios
- Tres niveles de acceso con permisos específicos
- Administración completa de usuarios (solo Admin)
- Perfil de usuario personalizable
- Logs de actividad por usuario

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8+** (sin frameworks, PHP puro)
- **MySQL 8+** con esquema optimizado
- **PDO** para acceso seguro a datos
- **Sesiones nombradas** (session_name)
- **Dotenv** para configuración

### Frontend
- **HTML5** semántico y accesible
- **CSS3** con variables y animaciones
- **JavaScript ES6+** modular
- **Bootstrap 5.3** responsive
- **Bootstrap Icons** para iconografía
- **Chart.js** para gráficos

### Seguridad
- Prepared statements contra SQL Injection
- Tokens CSRF para formularios
- Sanitización y validación de datos
- Hashing seguro de contraseñas
- Sesiones seguras con configuración estricta

## 📁 Estructura del Proyecto

```
Ariel/
├── public/                 # Directorio web público
│   ├── index.php          # Punto de entrada (login)
│   ├── dashboard.php      # Dashboard principal
│   ├── logout.php         # Cerrar sesión
│   └── assets/            # Recursos estáticos
│       ├── css/           # Hojas de estilo
│       ├── js/            # JavaScript
│       └── images/        # Imágenes
├── src/                   # Código fuente PHP
│   ├── controllers/       # Controladores
│   ├── models/           # Modelos de datos
│   ├── views/            # Vistas y templates
│   └── includes/         # Clases y funciones
│       ├── Database.php   # Conexión a BD
│       ├── Auth.php       # Autenticación
│       └── functions.php  # Funciones auxiliares
├── config/               # Configuración
│   └── config.php        # Configuración principal
├── migrations/           # Scripts SQL
│   ├── 001_create_database.sql
│   └── 002_insert_test_data.sql
├── docs/                 # Documentación
├── .env                  # Variables de entorno
├── .env.example         # Ejemplo de configuración
├── setup_database.sh    # Script de instalación
└── README.md            # Este archivo
```

## 🚀 Instalación Rápida

### Prerrequisitos
- PHP 8.0+ con extensiones: PDO, pdo_mysql, mbstring, json, session
- MySQL 8.0+ o MariaDB 10.4+
- Servidor web (Apache/Nginx)
- Acceso a línea de comandos

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/danjohn007/Ariel.git
   cd Ariel
   ```

2. **Ejecutar script de instalación**
   ```bash
   chmod +x setup_database.sh
   ./setup_database.sh
   ```

3. **Configurar servidor web**
   
   **Apache Virtual Host:**
   ```apache
   <VirtualHost *:80>
       ServerName construccion.local
       DocumentRoot /ruta/a/Ariel/public
       DirectoryIndex index.php
       <Directory /ruta/a/Ariel/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

   **Nginx Server Block:**
   ```nginx
   server {
       listen 80;
       server_name construccion.local;
       root /ruta/a/Ariel/public;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

4. **Acceder al sistema**
   - Abrir navegador en `http://construccion.local` (o su dominio configurado)
   - Usar credenciales de prueba (ver tabla abajo)

## 👤 Usuarios de Prueba

| Rol | Correo | Contraseña | Permisos |
|-----|---------|-----------|----------|
| **Admin** | admin@construccion.com | password | Acceso completo al sistema |
| **Analista** | analista@construccion.com | password | Gestión de obras y datos |
| **Visitante** | visitante@construccion.com | password | Solo lectura |

> ⚠️ **IMPORTANTE**: Cambie estas contraseñas en producción

## 🔧 Configuración

### Variables de Entorno (.env)

```bash
# Base de Datos
DB_HOST=localhost
DB_PORT=3306
DB_NAME=construccion_db
DB_USER=root
DB_PASS=tu_password

# Aplicación
APP_NAME="Sistema de Análisis de Precios y Programa de Obra"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://tu-dominio.com

# Sesiones
SESSION_NAME=construccion_session
SESSION_LIFETIME=7200

# Seguridad
ENCRYPTION_KEY=tu-clave-secreta-aqui
PASSWORD_SALT=tu-salt-de-password-aqui
```

### Permisos de Archivos

```bash
# Permisos recomendados
chmod 755 public/ src/ config/ migrations/
chmod 600 .env
chmod +x setup_database.sh
```

## 📊 Base de Datos

### Tablas Principales

- **usuarios** - Gestión de usuarios y roles
- **obras** - Proyectos de construcción
- **conceptos** - Conceptos de obra y precios
- **materiales** - Catálogo de materiales
- **mano_obra** - Recursos humanos
- **maquinaria** - Equipos y maquinaria
- **analisis_precios** - Análisis de precios unitarios
- **programa_obra** - Programación temporal
- **proveedores** - Gestión de proveedores
- **logs_auditoria** - Trazabilidad del sistema

### Características de la BD

- **Integridad referencial** con foreign keys
- **Campos calculados** para totales automáticos
- **Indexes optimizados** para consultas rápidas
- **Campos de auditoría** (created_at, updated_at)
- **Soft deletes** con campos activo/inactivo

## 🔐 Seguridad Implementada

### Autenticación
- Hashing seguro con `password_hash()`
- Sesiones con nombre personalizado
- Regeneración de session ID tras login
- Control de tiempo de sesión

### Autorización
- Middleware de roles en cada página
- Verificación de permisos en backend
- Restricción de rutas por rol
- Logs de acciones por usuario

### Protección de Datos
- Prepared statements en todas las consultas
- Tokens CSRF en formularios
- Sanitización de entradas
- Escapado de salidas HTML
- Validación cliente y servidor

### Configuración Segura
- Variables sensibles en .env
- Permisos restrictivos de archivos
- Headers de seguridad HTTP
- Configuración de cookies seguras

## 🎨 Interfaz de Usuario

### Características del UI
- **Responsive Design** - Compatible con móviles y tablets
- **Bootstrap 5.3** - Framework CSS moderno
- **Iconografía consistente** - Bootstrap Icons
- **Tema personalizado** - Gradientes y animaciones
- **Accesibilidad** - ARIA labels y navegación por teclado
- **UX optimizada** - Breadcrumbs, paginación, búsqueda

### Componentes Destacados
- Dashboard con métricas en tiempo real
- Tablas interactivas con ordenamiento
- Formularios con validación en vivo
- Modales de confirmación
- Alertas y notificaciones
- Barras de progreso animadas

## 📱 Responsive Design

El sistema está optimizado para:
- **Desktop** (1200px+) - Funcionalidad completa
- **Tablet** (768px-1199px) - Navegación adaptada
- **Mobile** (320px-767px) - UI optimizada táctil

## 🚀 Funcionalidades en Desarrollo

### Próximas Implementaciones
- [ ] **CRUD completo** de conceptos y precios unitarios
- [ ] **Reportes PDF/Excel** exportables
- [ ] **Programa de obra visual** (Gantt interactivo)
- [ ] **API REST** para integraciones
- [ ] **Backup automático** de base de datos
- [ ] **Notificaciones por email**
- [ ] **Dashboard avanzado** con más métricas
- [ ] **Módulo de inventarios**
- [ ] **Gestión de documentos**
- [ ] **App móvil** (PWA)

### Mejoras Planificadas
- [ ] **Modo oscuro** en interfaz
- [ ] **Múltiples idiomas** (i18n)
- [ ] **Cache de consultas** para rendimiento
- [ ] **WebSockets** para actualizaciones en tiempo real
- [ ] **Integración con contabilidad**
- [ ] **Módulo de reportes avanzados**

## 🧪 Testing

### Testing Manual
1. Verificar login con diferentes roles
2. Probar CRUD de obras y conceptos
3. Validar cálculos de precios
4. Confirmar restricciones de permisos
5. Revisar logs de auditoría

### Testing de Seguridad
- [ ] Verificar protección SQL Injection
- [ ] Probar validación CSRF
- [ ] Confirmar sanitización XSS
- [ ] Validar control de sesiones
- [ ] Revisar permisos de archivos

## 🐛 Troubleshooting

### Problemas Comunes

**Error de conexión a BD:**
```bash
# Verificar credenciales en .env
# Confirmar que MySQL esté ejecutándose
sudo systemctl status mysql
```

**Permisos de archivos:**
```bash
# Restaurar permisos
chmod 755 public/ src/ config/
chmod 600 .env
```

**PHP no encuentra extensiones:**
```bash
# Instalar extensiones faltantes (Ubuntu/Debian)
sudo apt install php8.1-mysql php8.1-mbstring
```

**Error 500 en servidor:**
```bash
# Revisar logs de error de Apache/Nginx
sudo tail -f /var/log/apache2/error.log
```

## 🤝 Contribuir

### ¿Quieres contribuir?

1. **Fork** el repositorio
2. Crea una **rama** para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. **Commit** tus cambios (`git commit -am 'Agrega nueva funcionalidad'`)
4. **Push** a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crea un **Pull Request**

### Guías de Contribución
- Seguir estándares PSR-12 para PHP
- Documentar nuevas funciones
- Incluir tests para nuevas features
- Mantener compatibilidad con versiones soportadas

## 📞 Soporte

### ¿Problemas o sugerencias?

- **Issues en GitHub**: [Crear nuevo issue](https://github.com/danjohn007/Ariel/issues)
- **Información requerida**:
  - Pasos para reproducir el error
  - Entorno (PHP, MySQL, navegador)
  - Logs de error relevantes
  - Screenshots si es apropiado

### Información del Sistema
- **PHP**: Versión 8.0+
- **MySQL**: Versión 8.0+
- **Bootstrap**: Versión 5.3
- **Navegadores**: Chrome 90+, Firefox 88+, Safari 14+

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Créditos

- **Desarrollado por**: Equipo de Desarrollo
- **Framework CSS**: Bootstrap 5.3
- **Iconos**: Bootstrap Icons
- **Gráficos**: Chart.js
- **Inspirado en**: Metodologías de construcción modernas

---

## 📈 Estadísticas del Proyecto

![GitHub stars](https://img.shields.io/github/stars/danjohn007/Ariel)
![GitHub forks](https://img.shields.io/github/forks/danjohn007/Ariel)
![GitHub issues](https://img.shields.io/github/issues/danjohn007/Ariel)
![GitHub last commit](https://img.shields.io/github/last-commit/danjohn007/Ariel)

---

**¡Gracias por usar el Sistema de Análisis de Precios y Programa de Obra!** 🚀

Para más información, visita la [documentación completa](docs/) o contacta al equipo de desarrollo.