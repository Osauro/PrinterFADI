# 🌐 Sistema POS FADI - Adaptado para cPanel

## 🎯 Adaptación Completada para Hosting Web

### ✅ **Cambios Realizados**

#### 📡 **Sistema de Conexión Múltiple**
```php
// Antes: Solo Windows local
new WindowsPrintConnector($nombreImpresora);

// Ahora: Conexión adaptable
switch ($this->tipoConexion) {
    case 'network':    // Para cPanel/Web - IP de red
    case 'windows':    // Para Windows local  
    case 'file':       // Para Linux/Unix
}
```

#### 🔧 **Nuevas Configuraciones en .env**
```env
# Configuración de Conexión (para cPanel/Web)
PRINTER_CONNECTION_TYPE=network    # network/windows/file
PRINTER_IP=192.168.1.100          # IP de la impresora térmica
PRINTER_PORT=9100                 # Puerto TCP (estándar 9100)
```

#### 🖥️ **Interfaz Web Actualizada**
- ✅ Selector de tipo de conexión
- ✅ Configuración de IP y puerto para red
- ✅ Campos dinámicos según tipo de conexión
- ✅ Instrucciones específicas para cPanel

### 🚀 **Instrucciones de Uso en cPanel**

#### **Paso 1: Configuración de Red**
1. **Conectar impresora térmica a la red local**
   - Usar cable Ethernet o WiFi
   - Obtener la IP asignada (ej: 192.168.1.100)
   - Verificar que el puerto 9100 esté abierto

#### **Paso 2: Configuración del Sistema**
1. **Subir archivos a cPanel**
   ```
   public_html/
   ├── pos/          # Subir todo el proyecto aquí
   ```

2. **Configurar base de datos**
   - Usar la BD remota existente (paybol_fadi)
   - Verificar conexión en .env

3. **Configurar impresora**
   - Ir a: `https://tudominio.com/pos/`
   - Tipo de conexión: **"Red/IP (cPanel/Web)"**
   - IP de impresora: `192.168.1.100` (la IP real)
   - Puerto: `9100` (estándar)

#### **Paso 3: Prueba de Funcionamiento**
1. **Test de conexión**
   - Botón "Probar Impresión"
   - Verificar que imprime correctamente

2. **Test de impresión real**
   - Imprimir venta desde: `https://tudominio.com/pos/venta/123`
   - Imprimir boleta desde: `https://tudominio.com/pos/boleta/456`

### 🔧 **Tipos de Conexión Disponibles**

#### 🌐 **Network (Recomendado para cPanel)**
```php
// Conexión TCP/IP a impresora de red
new NetworkPrintConnector('192.168.1.100', 9100);
```
- ✅ **Ideal para**: Hosting web, servidores remotos
- ✅ **Ventajas**: Funciona desde cualquier ubicación
- ❗ **Requisito**: Impresora con conectividad de red

#### 🖥️ **Windows (Solo local)**
```php
// Conexión a impresora Windows local
new WindowsPrintConnector('POS80');
```
- ✅ **Ideal para**: Instalaciones localhost
- ❌ **No funciona**: En servidores web remotos

#### 📁 **File (Linux/Unix)**
```php
// Conexión por archivo de dispositivo
new FilePrintConnector('/dev/usb/lp0');
```
- ✅ **Ideal para**: Servidores Linux con USB
- ⚠️ **Limitado**: Requiere acceso físico al servidor

### 🎛️ **Configuraciones Específicas cPanel**

#### **Variables de Entorno (.env)**
```env
# Configuración optimizada para cPanel
APP_URL=https://tudominio.com/pos
APP_ENV=production
APP_DEBUG=false

# Conexión impresora de red
PRINTER_CONNECTION_TYPE=network
PRINTER_IP=192.168.1.100
PRINTER_PORT=9100
PRINTER_NAME=POS80

# Base de datos remota
DB_CONNECTION=mysql
DB_HOST=fadi.com.bo
DB_DATABASE=paybol_fadi
```

#### **Rutas de Acceso**
```
https://tudominio.com/pos/                    # Configuración
https://tudominio.com/pos/venta/{id}          # Imprimir venta
https://tudominio.com/pos/boleta/{id}         # Imprimir boleta
https://tudominio.com/pos/transferencia/{id}  # Imprimir transferencia
```

### 🔧 **Troubleshooting cPanel**

#### **Problema: No conecta a impresora**
```bash
# Verificar conectividad
ping 192.168.1.100

# Verificar puerto abierto
telnet 192.168.1.100 9100
```

#### **Problema: Permisos de archivo**
```bash
# En cPanel File Manager
chmod 755 pos/
chmod 644 pos/.env
chmod -R 755 pos/storage/
```

#### **Problema: Base de datos**
```php
// Verificar conexión en .env
DB_HOST=fadi.com.bo          # ✅ Correcto
DB_HOST=localhost            # ❌ No funciona en cPanel
```

### 📊 **Ventajas del Sistema Adaptado**

1. **🌐 Flexibilidad Total**
   - Funciona en localhost Y cPanel
   - Múltiples tipos de conexión
   - Configuración dinámica

2. **🔧 Fácil Configuración**
   - Interfaz web intuitiva
   - Configuración desde navegador
   - Sin instalación cliente

3. **📱 Acceso Remoto**
   - Impresión desde cualquier dispositivo
   - No requiere software local
   - Compatible con móviles

4. **🚀 Distribución Simplificada**
   - Un solo código para todos los entornos
   - Configuración por variables
   - Fácil mantenimiento

### 🎯 **Resultado Final**

El sistema ahora es **100% compatible con cPanel** y puede:
- ✅ Conectarse a impresoras de red vía TCP/IP
- ✅ Funcionar en servidores remotos
- ✅ Mantener compatibilidad con instalaciones locales
- ✅ Configurarse completamente desde web
- ✅ Imprimir desde cualquier ubicación

**Status**: 🌟 **LISTO PARA PRODUCCIÓN EN CPANEL** 🌟
