# 🌐 Guía de Despliegue en cPanel - Sistema POS FADI

## 📋 Resumen del Despliegue

Este documento detalla cómo desplegar el Sistema POS FADI en un servidor web con cPanel para permitir impresión remota desde cualquier ubicación.

## 🎯 Ventajas del Despliegue en cPanel

- 🌍 **Acceso remoto** - Imprimir desde cualquier dispositivo con internet
- 📱 **Compatible móvil** - Funciona en tablets y smartphones
- 🏢 **Multi-ubicación** - Una sola instalación, múltiples puntos de impresión
- 🔄 **Sin instalación local** - Solo requiere navegador web
- 📊 **Gestión centralizada** - Configuración desde cualquier lugar

## 📋 Requisitos Previos

### 🖥️ Servidor/Hosting
- ✅ **PHP 8.2** o superior
- ✅ **MySQL** base de datos
- ✅ **cPanel** con File Manager
- ✅ **Composer** instalado
- ✅ **SSL** certificado (recomendado)

### 🖨️ Impresora Térmica
- ✅ **Impresora POS** con conexión de red
- ✅ **IP estática** configurada
- ✅ **Puerto 9100** abierto (estándar)
- ✅ **Red local** accesible desde servidor

## 🚀 Proceso de Instalación

### **Paso 1: Preparar Archivos**

1. **Comprimir proyecto** (excluyendo vendor/):
```bash
# Desde el directorio del proyecto
tar -czf pos-fadi.tar.gz --exclude=vendor --exclude=.git .
```

2. **Subir archivo** a cPanel via File Manager
   - Ubicación: `public_html/pos/`
   - Extraer: `pos-fadi.tar.gz`

### **Paso 2: Configurar Base de Datos**

1. **Crear BD en cPanel**:
   - Ir a "MySQL Databases"
   - Crear DB: `usuario_pos_fadi`
   - Crear usuario: `usuario_pos_user`
   - Asignar permisos completos

2. **Importar estructura** (si es necesario):
```sql
-- Solo si necesitas tablas locales
-- El sistema usa BD remota por defecto
```

### **Paso 3: Configurar Variables de Entorno**

Editar archivo `.env` en cPanel File Manager:

```env
# Configuración Principal
APP_NAME=Sistema-POS-FADI
APP_ENV=production
APP_KEY=base64:GENERAR_NUEVA_CLAVE
APP_DEBUG=false
APP_URL=https://tudominio.com/pos

# Idioma
APP_LOCALE=es
APP_FALLBACK_LOCALE=es

# Logs
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

# Base de Datos (usar BD remota existente)
DB_CONNECTION=mysql
DB_HOST=fadi.com.bo
DB_PORT=3306
DB_DATABASE=paybol_fadi
DB_USERNAME=paybol_admin
DB_PASSWORD=Nagato5421

# Sesiones
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache
CACHE_STORE=file

# === CONFIGURACIÓN DE IMPRESORA PARA cPANEL ===
PRINTER_NAME=POS80
PAPER_WIDTH=37
PRINTER_SHOW_LOGO=true
PRINTER_SHOW_QR=true
PRINTER_AUTO_CUT=true
PRINTER_SOUND_ALERT=false
PRINTER_COMPANY_NAME="DISTRIBUIDORA FADI"
PRINTER_BRAND="¤ FADI ¤"
PRINTER_FOOTER_MESSAGE="___GRACIAS POR SU COMPRA___"
PRINTER_CONTACT="CEL: 73010688"

# === CONFIGURACIÓN DE RED (CRÍTICO PARA cPANEL) ===
PRINTER_CONNECTION_TYPE=network
PRINTER_IP=192.168.1.100
PRINTER_PORT=9100
```

### **Paso 4: Instalar Dependencias**

En **Terminal de cPanel** (o SSH):

```bash
# Navegar al directorio
cd public_html/pos

# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Generar clave de aplicación
php artisan key:generate

# Limpiar cache
php artisan config:clear
php artisan cache:clear
```

### **Paso 5: Configurar Permisos**

```bash
# Permisos de storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Propietario correcto (ajustar según cPanel)
chown -R usuario:usuario storage bootstrap/cache
```

### **Paso 6: Configurar Impresora de Red**

#### 🖨️ **Configuración de Impresora**

1. **Asignar IP estática** a la impresora:
   - Acceder al panel de la impresora
   - Configurar IP: `192.168.1.100`
   - Máscara: `255.255.255.0`
   - Gateway: `192.168.1.1`

2. **Verificar conectividad**:
```bash
# Desde el servidor o red local
ping 192.168.1.100
telnet 192.168.1.100 9100
```

3. **Configurar firewall** (si es necesario):
   - Puerto 9100 TCP abierto
   - Acceso desde IP del servidor

## 🔧 Configuración Final

### **Acceso al Sistema**

1. **URL principal**:
```
https://tudominio.com/pos/
```

2. **Configurar impresora**:
   - Tipo de conexión: "Red/IP (cPanel/Web)"
   - IP: `192.168.1.100`
   - Puerto: `9100`
   - Probar conexión

### **URLs de Impresión**

```
# Configuración
https://tudominio.com/pos/

# Imprimir venta
https://tudominio.com/pos/venta/123

# Imprimir boleta
https://tudominio.com/pos/boleta/456

# Imprimir transferencia
https://tudominio.com/pos/transferencia/789

# Prueba de impresión
POST: https://tudominio.com/pos/test-print
```

## 🛠️ Configuración Avanzada

### **SSL/HTTPS (Recomendado)**

1. **Activar SSL** en cPanel
2. **Forzar HTTPS**:
```apache
# En .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### **Subdirectorio vs Subdominio**

#### 📁 **Opción 1: Subdirectorio** (Más simple)
```
https://tudominio.com/pos/
```

#### 🌐 **Opción 2: Subdominio** (Más profesional)
```
https://pos.tudominio.com/
```

Para subdominio:
1. Crear subdominio en cPanel
2. Apuntar a `/pos` directory
3. Actualizar `APP_URL` en `.env`

### **Optimización de Rendimiento**

```bash
# Cache de configuración
php artisan config:cache

# Cache de rutas
php artisan route:cache

# Cache de vistas
php artisan view:cache

# Autoloader optimizado
composer dump-autoload --optimize
```

## 🔍 Troubleshooting

### **Problemas Comunes**

#### ❌ **Error 500 - Internal Server Error**
```bash
# Revisar logs
tail -f storage/logs/laravel.log

# Verificar permisos
chmod -R 775 storage bootstrap/cache

# Limpiar cache
php artisan config:clear
```

#### ❌ **No se conecta a la impresora**
```bash
# Verificar red
ping 192.168.1.100

# Verificar puerto
telnet 192.168.1.100 9100

# Revisar configuración
cat .env | grep PRINTER
```

#### ❌ **Composer no funciona**
```bash
# Actualizar composer
composer self-update

# Reinstalar dependencias
rm -rf vendor
composer install --no-dev
```

### **Logs y Diagnóstico**

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Ver logs de Apache/Nginx
tail -f /var/log/apache2/error.log

# Verificar configuración PHP
php artisan tinker
```

## 📊 Monitoreo y Mantenimiento

### **Respaldos Automáticos**

```bash
# Script de respaldo (crear en cPanel cron)
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
tar -czf backup_pos_$DATE.tar.gz pos/
# Excluir vendor y logs grandes
```

### **Actualizaciones**

```bash
# Actualizar código
git pull origin main

# Actualizar dependencias
composer update --no-dev

# Limpiar cache
php artisan config:clear
php artisan view:clear
```

## 🎯 Configuración de Producción

### **Optimizaciones Finales**

```env
# .env para producción
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
SESSION_SECURE_COOKIE=true
```

```bash
# Comandos de optimización
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize --classmap-authoritative
```

## 🏁 Verificación Final

### **Checklist de Despliegue**

- ✅ **Sistema accesible** via HTTPS
- ✅ **Impresora conectada** y probada
- ✅ **Base de datos** funcionando
- ✅ **Logs sin errores**
- ✅ **SSL activado**
- ✅ **Cache optimizado**
- ✅ **Permisos correctos**
- ✅ **Backup configurado**

### **URLs de Prueba**

1. **Página principal**: `https://tudominio.com/pos/`
2. **Test de impresión**: Botón "Probar Impresión"
3. **Configuración**: Formulario de ajustes
4. **Conectividad**: Estado de la impresora

---

## 📞 Soporte Técnico

Para soporte adicional:
- 📧 **Email**: admin@fadi.com.bo
- 📱 **WhatsApp**: +591 73010688
- 🌐 **Sitio**: https://fadi.com.bo

**¡Sistema POS FADI listo para producción en cPanel!** 🚀
