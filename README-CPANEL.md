# 🖨️ Sistema POS FADI - Despliegue cPanel

Sistema de impresión térmica POS ultra-minimalista optimizado para despliegue en servidores web con cPanel.

## 🚀 Instalación Rápida

### 1. Subir archivos
```bash
# Comprimir proyecto (sin vendor)
tar -czf pos-fadi.tar.gz --exclude=vendor --exclude=.git .

# Subir a cPanel File Manager: public_html/pos/
# Extraer archivos
```

### 2. Configurar entorno
```bash
# Copiar archivo de configuración
cp .env.cpanel .env

# Editar variables según tu servidor
nano .env
```

### 3. Instalar
```bash
# Ejecutar instalador automático
chmod +x install-cpanel.sh
./install-cpanel.sh
```

## 🌐 Acceso
- **URL**: `https://tudominio.com/pos/`
- **Configuración**: Interfaz web completa
- **Impresión**: Automática vía IP de red

## 📋 Archivos Importantes

- `DEPLOY-CPANEL.md` - Documentación completa de despliegue
- `.env.cpanel` - Configuración de ejemplo para producción
- `install-cpanel.sh` - Script de instalación automática
- `.htaccess` - Configuración Apache optimizada

## 🖨️ Configuración de Impresora

1. **Asignar IP estática**: `192.168.1.100`
2. **Puerto**: `9100` (estándar)
3. **Tipo**: Red/IP en la configuración web

## 🔧 Requisitos
- PHP 8.2+
- MySQL
- Composer
- Impresora térmica con red

## 📞 Soporte
- **Email**: admin@fadi.com.bo
- **Tel**: +591 73010688
- **Web**: https://fadi.com.bo

---
**Sistema POS FADI** - Impresión térmica profesional desde la web
