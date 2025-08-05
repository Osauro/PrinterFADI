@echo off
echo ====================================
echo    VERIFICACION DEL SISTEMA POS
echo ====================================
echo.

echo [Verificando configuracion...]
php artisan config:show app.name 2>nul
echo.

echo [Verificando base de datos...]
php -r "
try {
    $pdo = new PDO('mysql:host=%DB_HOST%;dbname=%DB_DATABASE%', '%DB_USERNAME%', '%DB_PASSWORD%');
    echo 'Conexion a BD: OK' . PHP_EOL;
} catch (Exception $e) {
    echo 'Error de BD: Solo para impresion (OK)' . PHP_EOL;
}
"
echo.

echo [Verificando rutas...]
php artisan route:list --compact
echo.

echo [Verificando permisos...]
if exist "storage\framework\cache" (
    echo Permisos de cache: OK
) else (
    echo Creando directorios de cache...
    mkdir storage\framework\cache\data 2>nul
)
echo.

echo [Verificando dependencias de impresion...]
php -r "
if (class_exists('Mike42\Escpos\Printer')) {
    echo 'Libreria de impresion: OK' . PHP_EOL;
} else {
    echo 'Error: Libreria de impresion no encontrada' . PHP_EOL;
}
"
echo.

echo ====================================
echo    VERIFICACION COMPLETADA
echo ====================================
echo.
echo Para probar el sistema:
echo 1. Ir a: http://localhost
echo 2. Configurar impresora
echo 3. Probar impresion
echo.
pause
