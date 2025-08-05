@echo off
setlocal EnableDelayedExpansion
title POS FADI - Instalador Automatico v1.0
color 0A

:: ===================================================================
::                    POS FADI - INSTALADOR AUTOMATICO
::               Descarga desde GitHub e instala automáticamente
:: ===================================================================

echo.
echo  ================================================================
echo   ____   ___  ____    _____  _    ____  ___
echo  ^|  _ \ / _ \^|  _ \  ^|  ___^|^| ^|  ^|  _ \^|_ _^|
echo  ^| ^|_) ^| ^| ^| ^| ^|_) ^| ^| ^|_   ^| ^|  ^| ^| ^| ^|^| ^|
echo  ^|  __/^| ^|_^| ^|  _ ^<  ^|  _^|  ^| ^|  ^| ^|_^| ^|^| ^|
echo  ^|_^|    \___/^|_^| \_\ ^|_^|    ^|_^|  ^|____/^|___^|
echo.
echo                    INSTALADOR AUTOMATICO v1.0
echo  ================================================================
echo.

:: Configuración
set "REPO_URL=https://github.com/Osauro/PrinterFADI.git"
set "INSTALL_DIR=%LOCALAPPDATA%\POS-FADI"
set "DESKTOP_SHORTCUT=%USERPROFILE%\Desktop\POS FADI.lnk"
set "START_MENU=%APPDATA%\Microsoft\Windows\Start Menu\Programs\POS FADI.lnk"
set "PHP_VERSION=8.2.12"
set "COMPOSER_URL=https://getcomposer.org/composer-stable.phar"

echo [INFO] Configuracion del instalador:
echo        Repositorio: %REPO_URL%
echo        Directorio:  %INSTALL_DIR%
echo        Version PHP: %PHP_VERSION%
echo.

:: Verificar permisos de administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Este instalador requiere permisos de administrador.
    echo [INFO]  Haga clic derecho y seleccione "Ejecutar como administrador"
    pause
    exit /b 1
)

:: Verificar conexión a internet
echo [1/8] Verificando conexion a internet...
ping -n 1 google.com >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] No se puede conectar a internet.
    echo [INFO]  Verifique su conexion y vuelva a intentar.
    pause
    exit /b 1
)
echo [OK] Conexion a internet verificada.

:: Verificar/Instalar Git
echo [2/8] Verificando Git...
where git >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] Git no encontrado. Descargando e instalando...

    :: Descargar Git portable
    if not exist "%TEMP%\PortableGit.exe" (
        echo [INFO] Descargando Git portable...
        powershell -Command "& {Invoke-WebRequest -Uri 'https://github.com/git-for-windows/git/releases/download/v2.42.0.windows.2/PortableGit-2.42.0.2-64-bit.7z.exe' -OutFile '%TEMP%\PortableGit.exe'}"
    )

    :: Extraer Git portable
    echo [INFO] Extrayendo Git portable...
    if not exist "%INSTALL_DIR%\tools\git" mkdir "%INSTALL_DIR%\tools\git"
    "%TEMP%\PortableGit.exe" -o"%INSTALL_DIR%\tools\git" -y

    :: Agregar Git al PATH temporal
    set "PATH=%INSTALL_DIR%\tools\git\bin;%PATH%"
)
echo [OK] Git disponible.

:: Verificar/Instalar PHP
echo [3/8] Configurando PHP %PHP_VERSION%...
if not exist "%INSTALL_DIR%\tools\php\php.exe" (
    echo [INFO] Descargando PHP %PHP_VERSION%...

    :: Crear directorio PHP
    if not exist "%INSTALL_DIR%\tools\php" mkdir "%INSTALL_DIR%\tools\php"

    :: Descargar PHP
    powershell -Command "& {Invoke-WebRequest -Uri 'https://windows.php.net/downloads/releases/archives/php-%PHP_VERSION%-nts-Win32-vs16-x64.zip' -OutFile '%TEMP%\php.zip'}"

    :: Extraer PHP
    echo [INFO] Extrayendo PHP...
    powershell -Command "Expand-Archive -Path '%TEMP%\php.zip' -DestinationPath '%INSTALL_DIR%\tools\php' -Force"

    :: Configurar PHP
    echo [INFO] Configurando PHP...
    copy "%INSTALL_DIR%\tools\php\php.ini-development" "%INSTALL_DIR%\tools\php\php.ini" >nul

    :: Habilitar extensiones necesarias
    (
        echo ; Configuracion POS FADI
        echo extension=curl
        echo extension=fileinfo
        echo extension=gd
        echo extension=mbstring
        echo extension=openssl
        echo extension=pdo_mysql
        echo extension=zip
        echo memory_limit = 256M
        echo max_execution_time = 60
        echo post_max_size = 50M
        echo upload_max_filesize = 50M
        echo date.timezone = America/La_Paz
    ) >> "%INSTALL_DIR%\tools\php\php.ini"

    :: Limpiar archivo ZIP
    del "%TEMP%\php.zip" >nul 2>&1
)

:: Agregar PHP al PATH temporal
set "PATH=%INSTALL_DIR%\tools\php;%PATH%"
echo [OK] PHP %PHP_VERSION% configurado.

:: Verificar/Instalar Composer
echo [4/8] Configurando Composer...
if not exist "%INSTALL_DIR%\tools\composer.phar" (
    echo [INFO] Descargando Composer...
    powershell -Command "& {Invoke-WebRequest -Uri '%COMPOSER_URL%' -OutFile '%INSTALL_DIR%\tools\composer.phar'}"

    :: Crear wrapper de Composer
    (
        echo @echo off
        echo php "%INSTALL_DIR%\tools\composer.phar" %%*
    ) > "%INSTALL_DIR%\tools\composer.bat"
)
echo [OK] Composer disponible.

:: Clonar repositorio
echo [5/8] Descargando codigo fuente desde GitHub...
if exist "%INSTALL_DIR%\app" (
    echo [INFO] Actualizando repositorio existente...
    cd /d "%INSTALL_DIR%\app"
    git pull origin master
) else (
    echo [INFO] Clonando repositorio...
    if not exist "%INSTALL_DIR%" mkdir "%INSTALL_DIR%"
    cd /d "%INSTALL_DIR%"
    git clone "%REPO_URL%" app
)

if %errorLevel% neq 0 (
    echo [ERROR] Error clonando el repositorio.
    echo [INFO]  Verifique la conexion a internet y vuelva a intentar.
    pause
    exit /b 1
)
echo [OK] Codigo fuente descargado.

:: Instalar dependencias PHP
echo [6/8] Instalando dependencias PHP...
cd /d "%INSTALL_DIR%\app"
"%INSTALL_DIR%\tools\composer.bat" install --no-dev --optimize-autoloader

if %errorLevel% neq 0 (
    echo [ERROR] Error instalando dependencias PHP.
    pause
    exit /b 1
)
echo [OK] Dependencias PHP instaladas.

:: Configurar aplicación
echo [7/8] Configurando aplicacion...

:: Crear archivo .env
if not exist ".env" (
    echo [INFO] Creando archivo de configuracion...
    (
        echo APP_NAME="POS FADI Local"
        echo APP_ENV=production
        echo APP_KEY=base64:SKVTXHcp9atDuO+1HXNhNMOWD12WyM/Gh/+35Fq/gMo=
        echo APP_DEBUG=false
        echo APP_URL=http://localhost:8080
        echo.
        echo LOG_CHANNEL=single
        echo LOG_LEVEL=error
        echo.
        echo # Base de datos remota FADI
        echo DB_CONNECTION=mysql
        echo DB_HOST=fadi.com.bo
        echo DB_PORT=3306
        echo DB_DATABASE=paybol_fadi
        echo DB_USERNAME=paybol_admin
        echo DB_PASSWORD=Nagato5421
        echo.
        echo # Cache y sesion local
        echo CACHE_DRIVER=file
        echo SESSION_DRIVER=file
        echo QUEUE_CONNECTION=sync
        echo.
        echo # Configuracion de Impresora
        echo PRINTER_NAME=POS80
        echo PAPER_WIDTH=37
        echo PRINTER_SHOW_LOGO=true
        echo PRINTER_SHOW_QR=false
        echo PRINTER_AUTO_CUT=true
        echo PRINTER_SOUND_ALERT=false
        echo PRINTER_COMPANY_NAME="DISTRIBUIDORA"
        echo PRINTER_BRAND="¤ FADI ¤"
        echo PRINTER_FOOTER_MESSAGE="___GRACIAS POR SU COMPRA___"
        echo PRINTER_CONTACT="CEL: 73010688"
    ) > .env
)

:: Crear directorios de storage
echo [INFO] Creando directorios necesarios...
if not exist "storage\logs" mkdir "storage\logs"
if not exist "storage\framework\cache\data" mkdir "storage\framework\cache\data"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"

:: Generar key si no existe
echo [INFO] Configurando clave de aplicacion...
php artisan key:generate --force

echo [OK] Aplicacion configurada.

:: Crear accesos directos
echo [8/8] Creando accesos directos...

:: Script para iniciar el servidor
(
    echo @echo off
    echo title POS FADI - Servidor Local
    echo color 0A
    echo echo.
    echo echo  ================================================
    echo echo               POS FADI - SERVIDOR LOCAL
    echo echo  ================================================
    echo echo.
    echo echo [INFO] Iniciando servidor en puerto 8080...
    echo echo [INFO] Acceder desde: http://localhost:8080
    echo echo [INFO] Presione Ctrl+C para detener el servidor
    echo echo.
    echo cd /d "%INSTALL_DIR%\app"
    echo set "PATH=%INSTALL_DIR%\tools\php;%%PATH%%"
    echo php artisan serve --host=0.0.0.0 --port=8080
    echo pause
) > "%INSTALL_DIR%\Iniciar POS FADI.bat"

:: Script para abrir en navegador
(
    echo @echo off
    echo start http://localhost:8080
) > "%INSTALL_DIR%\Abrir POS FADI.bat"

:: Script de actualización
(
    echo @echo off
    echo title POS FADI - Actualizador
    echo color 0A
    echo echo.
    echo echo  ================================================
    echo echo              POS FADI - ACTUALIZADOR
    echo echo  ================================================
    echo echo.
    echo echo [INFO] Actualizando desde GitHub...
    echo cd /d "%INSTALL_DIR%\app"
    echo set "PATH=%INSTALL_DIR%\tools\git\bin;%%PATH%%"
    echo git pull origin master
    echo echo.
    echo echo [INFO] Actualizando dependencias...
    echo "%INSTALL_DIR%\tools\composer.bat" install --no-dev --optimize-autoloader
    echo echo.
    echo echo [OK] Actualizacion completada.
    echo pause
) > "%INSTALL_DIR%\Actualizar POS FADI.bat"

:: Crear acceso directo en el escritorio
powershell -Command "& {$WshShell = New-Object -comObject WScript.Shell; $Shortcut = $WshShell.CreateShortcut('%DESKTOP_SHORTCUT%'); $Shortcut.TargetPath = '%INSTALL_DIR%\Iniciar POS FADI.bat'; $Shortcut.WorkingDirectory = '%INSTALL_DIR%'; $Shortcut.IconLocation = '%INSTALL_DIR%\tools\php\php.exe,0'; $Shortcut.Description = 'POS FADI - Sistema de Punto de Venta'; $Shortcut.Save()}"

:: Crear acceso en menú inicio
if not exist "%APPDATA%\Microsoft\Windows\Start Menu\Programs" mkdir "%APPDATA%\Microsoft\Windows\Start Menu\Programs"
powershell -Command "& {$WshShell = New-Object -comObject WScript.Shell; $Shortcut = $WshShell.CreateShortcut('%START_MENU%'); $Shortcut.TargetPath = '%INSTALL_DIR%\Iniciar POS FADI.bat'; $Shortcut.WorkingDirectory = '%INSTALL_DIR%'; $Shortcut.IconLocation = '%INSTALL_DIR%\tools\php\php.exe,0'; $Shortcut.Description = 'POS FADI - Sistema de Punto de Venta'; $Shortcut.Save()}"

echo [OK] Accesos directos creados.

:: Limpiar archivos temporales
echo [INFO] Limpiando archivos temporales...
del "%TEMP%\PortableGit.exe" >nul 2>&1

echo.
echo  ================================================================
echo                        ¡INSTALACION COMPLETADA!
echo  ================================================================
echo.
echo  ✅ POS FADI se ha instalado correctamente en:
echo     %INSTALL_DIR%
echo.
echo  🚀 Para iniciar la aplicacion:
echo     • Usar acceso directo del escritorio "POS FADI"
echo     • O ejecutar: %INSTALL_DIR%\Iniciar POS FADI.bat
echo.
echo  🌐 La aplicacion estara disponible en:
echo     http://localhost:8080
echo.
echo  🔄 Para actualizar ejecutar:
echo     %INSTALL_DIR%\Actualizar POS FADI.bat
echo.
echo  📁 Archivos instalados:
echo     • Aplicacion:     %INSTALL_DIR%\app\
echo     • PHP %PHP_VERSION%:       %INSTALL_DIR%\tools\php\
echo     • Git:            %INSTALL_DIR%\tools\git\
echo     • Composer:       %INSTALL_DIR%\tools\composer.phar
echo.
echo  💡 La aplicacion se conecta automaticamente a la base de datos
echo     de fadi.com.bo, no requiere configuracion adicional.
echo.

:: Preguntar si iniciar ahora
set /p "start_now=¿Desea iniciar POS FADI ahora? (S/N): "
if /i "%start_now%"=="S" (
    echo.
    echo [INFO] Iniciando POS FADI...
    start "" "%INSTALL_DIR%\Iniciar POS FADI.bat"
    timeout /t 3 >nul
    start "" "http://localhost:8080"
)

echo.
echo ¡Gracias por usar POS FADI!
echo Soporte: https://wa.me/59173010688
echo.
pause
