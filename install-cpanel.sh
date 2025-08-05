#!/bin/bash

echo "🚀 Instalador Sistema POS FADI para cPanel"
echo "=========================================="

# Verificar si estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Ejecutar desde el directorio raíz del proyecto"
    exit 1
fi

echo "📦 Instalando dependencias..."
composer install --no-dev --optimize-autoloader

echo "🔑 Generando clave de aplicación..."
php artisan key:generate --force

echo "📁 Configurando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🖨️ Verificando configuración de impresora..."
if grep -q "PRINTER_CONNECTION_TYPE=network" .env; then
    echo "✅ Configuración de red detectada"
    PRINTER_IP=$(grep "PRINTER_IP=" .env | cut -d'=' -f2)
    echo "📍 IP de impresora: $PRINTER_IP"
else
    echo "⚠️  Configurar PRINTER_CONNECTION_TYPE=network en .env"
fi

echo "🎯 Verificando URL de aplicación..."
APP_URL=$(grep "APP_URL=" .env | cut -d'=' -f2)
echo "🌐 URL configurada: $APP_URL"

echo ""
echo "✅ ¡Instalación completada!"
echo ""
echo "📋 Pasos siguientes:"
echo "1. Verificar que la impresora esté en IP: $PRINTER_IP"
echo "2. Probar conectividad: ping $PRINTER_IP"
echo "3. Acceder a: $APP_URL"
echo "4. Configurar impresora en la interfaz web"
echo ""
echo "📞 Soporte: admin@fadi.com.bo"
