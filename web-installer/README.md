# POS FADI - Instalador Web

Sistema de instalación automática para POS FADI que descarga directamente desde GitHub.

## 🌐 Instalador Web

### Componentes:

1. **`index.html`** - Página web moderna para descargar el instalador
2. **`pos-fadi-installer.bat`** - Script de instalación automática
3. **`pos-fadi-installer.nsi`** - Instalador NSIS profesional
4. **`license.txt`** - Licencia y términos de uso

## 🚀 Características del Instalador

### ✅ **Instalación Automática:**
- Descarga código fuente desde GitHub
- Instala PHP 8.2 portable
- Configura Composer automáticamente
- Instala dependencias de Laravel
- Configura base de datos remota
- Crea accesos directos

### ✅ **Sin Dependencias:**
- No requiere XAMPP, Laragon o WAMP
- PHP embebido incluido
- Git portable incluido
- Composer integrado

### ✅ **Configuración Automática:**
- Conecta automáticamente con fadi.com.bo
- Configura impresora por defecto
- Crea variables de entorno
- Configura firewall de Windows

## 📋 Instrucciones de Uso

### Para subir a fadi.com.bo:

1. **Subir archivos a tu servidor web:**
   ```
   /pos-installer/
   ├── index.html
   ├── pos-fadi-installer.bat
   ├── pos-fadi-installer.exe (compilado)
   └── license.txt
   ```

2. **URL de acceso:**
   ```
   https://fadi.com.bo/pos-installer/
   ```

3. **Los usuarios podrán:**
   - Visitar la página
   - Ver características del sistema
   - Descargar el instalador
   - Instalar automáticamente

## 🛠️ Compilar Instalador NSIS

### Requisitos:
- NSIS 3.08+ (https://nsis.sourceforge.io/)
- Plugin NSISdl
- Plugin nsisunz

### Compilar:
```bash
makensis pos-fadi-installer.nsi
```

## 📦 Lo que hace el instalador:

### 1. **Descarga automática:**
- Git portable desde GitHub
- PHP 8.2 desde windows.php.net
- Composer desde getcomposer.org

### 2. **Clonación del proyecto:**
```bash
git clone https://github.com/Osauro/PrinterFADI.git
```

### 3. **Instalación de dependencias:**
```bash
composer install --no-dev --optimize-autoloader
```

### 4. **Configuración automática:**
- Archivo `.env` con conexión a fadi.com.bo
- Configuración de impresora por defecto
- Directorios de storage
- Clave de aplicación Laravel

### 5. **Scripts de utilidad:**
- `Iniciar POS FADI.bat` - Inicia servidor local
- `Actualizar POS FADI.bat` - Actualiza desde GitHub
- `Abrir POS FADI.bat` - Abre en navegador

### 6. **Accesos directos:**
- Escritorio: "POS FADI"
- Menú inicio: "POS FADI"

## 🌍 Ubicación de instalación:

```
%LOCALAPPDATA%\POS-FADI\
├── app/                    # Aplicación Laravel
├── tools/
│   ├── php/               # PHP 8.2 portable
│   ├── git/               # Git portable
│   └── composer.phar      # Composer
├── Iniciar POS FADI.bat   # Script de inicio
├── Actualizar POS FADI.bat # Script de actualización
└── Abrir POS FADI.bat     # Abrir en navegador
```

## 🔗 Conexión con la base de datos:

El instalador configura automáticamente la conexión a:
- **Host:** fadi.com.bo
- **Puerto:** 3306
- **Base de datos:** paybol_fadi
- **Usuario:** paybol_admin
- **Contraseña:** [configurada automáticamente]

## 🖥️ Uso del sistema:

1. **Iniciar:** Doble clic en "POS FADI" del escritorio
2. **Acceder:** http://localhost:8080
3. **Configurar:** Impresora desde la interfaz web
4. **Actualizar:** Ejecutar "Actualizar POS FADI"

## 📞 Soporte:

- **WhatsApp:** +591 73010688
- **Email:** info@fadi.com.bo
- **Website:** https://fadi.com.bo

## 🔄 Actualizaciones:

El sistema incluye actualización automática desde GitHub:
- Descarga la última versión
- Actualiza dependencias
- Mantiene configuración local

---

**Desarrollado para FADI con ❤️**
