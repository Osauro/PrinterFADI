@echo off
echo ====================================
echo    LIMPIEZA COMPLETA DEL SISTEMA POS
echo ====================================
echo.

echo [1/8] Limpiando cache de configuracion...
php artisan config:clear
echo.

echo [2/8] Limpiando cache de rutas...
php artisan route:clear
echo.

echo [3/8] Limpiando vistas compiladas...
php artisan view:clear
echo.

echo [4/8] Limpiando cache de eventos...
php artisan event:clear
echo.

echo [5/8] Limpiando cache de aplicacion...
php artisan cache:clear
echo.

echo [6/8] Regenerando autoloader...
composer dump-autoload -q
echo.

echo [7/8] Optimizacion completa...
php artisan optimize:clear
echo.

echo [8/8] Limpiando archivos temporales...
del /Q storage\framework\cache\data\* 2>nul
del /Q storage\logs\*.log 2>nul
echo.

echo ====================================
echo    LIMPIEZA COMPLETADA EXITOSAMENTE
echo ====================================
echo.
echo El sistema esta listo para usar.
echo Pagina principal: http://localhost
echo.
pause
