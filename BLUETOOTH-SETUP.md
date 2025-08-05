# 📶 Configuración Bluetooth - Sistema POS FADI

## 🎯 Soporte Bluetooth Multiplataforma

El Sistema POS FADI ahora incluye soporte completo para impresoras térmicas Bluetooth en Windows, Linux y macOS.

## 🔧 Tipos de Conexión Disponibles

### 1. 🌐 **Red/IP** (Recomendado para cPanel)
- Conexión por IP de red
- Ideal para servidores web
- Puerto estándar: 9100

### 2. 📶 **Bluetooth** (Nuevo - Móvil/Portátil)
- Conexión inalámbrica
- Ideal para tablets/móviles
- Multiplataforma

### 3. 🖥️ **Windows Local**
- Impresoras instaladas localmente
- Solo para desarrollo local

### 4. 📁 **Linux/Unix**
- Dispositivos de archivo
- Servidores Linux

## 📶 Configuración Bluetooth

### **Requisitos**
- ✅ Impresora térmica con Bluetooth
- ✅ Dispositivo emparejado previamente
- ✅ Dirección MAC conocida
- ✅ Drivers Bluetooth instalados

### **Pasos de Configuración**

#### 📱 **1. Emparejar Impresora**

**Windows:**
```cmd
# Abrir configuración Bluetooth
ms-settings:bluetooth

# O desde línea de comandos
powershell Add-PnpDevice
```

**Linux:**
```bash
# Escanear dispositivos
bluetoothctl scan on

# Emparejar con impresora
bluetoothctl pair XX:XX:XX:XX:XX:XX
bluetoothctl connect XX:XX:XX:XX:XX:XX
```

**macOS:**
```bash
# Abrir Preferencias del Sistema > Bluetooth
# O usar terminal
blueutil --pair XX:XX:XX:XX:XX:XX
```

#### ⚙️ **2. Configurar en Sistema POS**

1. **Acceder a configuración**: `https://tudominio.com/pos/`
2. **Seleccionar "Bluetooth"** en tipo de conexión
3. **Ingresar dirección MAC**: `XX:XX:XX:XX:XX:XX`
4. **Nombre del dispositivo**: `POS-Printer` (opcional)
5. **Probar conexión**

#### 🔍 **3. Obtener Dirección MAC**

**Windows:**
```cmd
# Lista dispositivos Bluetooth
Get-PnpDevice -Class Bluetooth
```

**Linux:**
```bash
# Lista dispositivos emparejados
bluetoothctl paired-devices

# Información detallada
hcitool scan
```

**macOS:**
```bash
# Lista dispositivos
system_profiler SPBluetoothDataType
```

## 🛠️ Configuración Técnica

### **Variables de Entorno**
```env
# Configuración Bluetooth
PRINTER_CONNECTION_TYPE=bluetooth
PRINTER_BLUETOOTH_ADDRESS=XX:XX:XX:XX:XX:XX
PRINTER_BLUETOOTH_NAME=POS-Printer
```

### **Formatos de Dirección MAC Soportados**
- `XX:XX:XX:XX:XX:XX` (recomendado)
- `XX-XX-XX-XX-XX-XX`
- `XXXXXXXXXXXX`

## 🔧 Troubleshooting Bluetooth

### ❌ **"No hay conexión Bluetooth activa"**

**Verificaciones:**
```bash
# 1. Verificar emparejamiento
bluetoothctl info XX:XX:XX:XX:XX:XX

# 2. Verificar servicios
systemctl status bluetooth

# 3. Reiniciar servicio
sudo systemctl restart bluetooth
```

### ❌ **"Error conectando vía Bluetooth"**

**Soluciones:**
1. **Verificar distancia** - Máximo 10 metros
2. **Comprobar batería** - Impresora encendida
3. **Reiniciar dispositivos** - Impresora y servidor
4. **Verificar interferencias** - Otros dispositivos Bluetooth

### ❌ **"Sistema operativo no soportado"**

**Compatibilidad:**
- ✅ Windows 10/11
- ✅ Ubuntu 18.04+
- ✅ macOS 10.15+
- ✅ Debian 10+
- ✅ CentOS 8+

## 📱 Casos de Uso Ideales

### **1. Punto de Venta Móvil**
- Tablet Android/iOS
- Impresora Bluetooth portátil
- Ventas en ferias/eventos

### **2. Delivery/Domicilio**
- Smartphone del repartidor
- Impresión de recibos en destino
- Sin necesidad de red WiFi

### **3. Oficina Pequeña**
- Escritorio sin cables
- Múltiples dispositivos
- Configuración limpia

### **4. Servidor Remoto**
- VPS con Bluetooth
- Impresión desde la nube
- Acceso global

## 🔄 Integración con Otros Tipos

### **Fallback Automático**
El sistema puede configurarse para usar múltiples tipos:

```php
// Orden de prioridad
1. Bluetooth (si está disponible)
2. Red/IP (si hay conectividad)
3. Local (último recurso)
```

### **Configuración Dinámica**
```javascript
// Detectar tipo óptimo automáticamente
function detectBestConnection() {
    if (bluetoothAvailable()) return 'bluetooth';
    if (networkAvailable()) return 'network';
    return 'local';
}
```

## 📊 Ventajas Bluetooth vs Otros

| Característica | Bluetooth | Red/IP | Windows | Linux |
|---------------|-----------|--------|---------|-------|
| **Portabilidad** | ✅ Excelente | ❌ Limitada | ❌ Fija | ❌ Fija |
| **Configuración** | ⚠️ Media | ✅ Fácil | ✅ Fácil | ⚠️ Media |
| **Distancia** | ⚠️ 10m | ✅ Ilimitada | ✅ Local | ✅ Local |
| **Velocidad** | ⚠️ Media | ✅ Alta | ✅ Alta | ✅ Alta |
| **Estabilidad** | ⚠️ Media | ✅ Alta | ✅ Alta | ✅ Alta |
| **Movilidad** | ✅ Total | ❌ Ninguna | ❌ Ninguna | ❌ Ninguna |

## 🚀 Implementación Técnica

### **Conector Personalizado**
```php
namespace App\PrintConnectors;

class BluetoothPrintConnector implements PrintConnector
{
    // Detección automática de SO
    // Conexión vía COM/rfcomm/serial
    // Manejo de errores robusto
    // Compatibilidad multiplataforma
}
```

### **Métodos de Conexión por SO**

**Windows:**
- Puerto COM virtual (COM3-COM8)
- Conexión de red fallback

**Linux:**
- Dispositivo rfcomm (/dev/rfcomm0)
- Puerto serie (/dev/ttyS*)

**macOS:**
- Dispositivo serie (/dev/cu.usbserial*)
- Integración nativa

---

## 📞 Soporte Técnico

Para soporte con configuración Bluetooth:
- 📧 **Email**: bluetooth@fadi.com.bo
- 📱 **WhatsApp**: +591 73010688
- 🔧 **Documentación**: Ver DEPLOY-CPANEL.md

**¡Sistema POS FADI ahora con Bluetooth universal!** 📶
