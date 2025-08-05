; ================================================================
; POS FADI - Instalador NSIS
; Instalador profesional para Windows
; ================================================================

!define APP_NAME "POS FADI"
!define APP_VERSION "1.0.0"
!define APP_PUBLISHER "FADI"
!define APP_URL "https://fadi.com.bo"
!define APP_SUPPORT_URL "https://wa.me/59173010688"
!define APP_UPDATES_URL "https://github.com/Osauro/PrinterFADI"

!define INSTALLER_NAME "pos-fadi-installer.exe"
!define INSTALL_DIR "$LOCALAPPDATA\POS-FADI"
!define REPO_URL "https://github.com/Osauro/PrinterFADI.git"

; ================================================================
; Configuración del instalador
; ================================================================

Name "${APP_NAME}"
OutFile "${INSTALLER_NAME}"
InstallDir "${INSTALL_DIR}"
RequestExecutionLevel admin
SetCompressor /SOLID lzma

; Información del instalador
VIProductVersion "1.0.0.0"
VIAddVersionKey "ProductName" "${APP_NAME}"
VIAddVersionKey "CompanyName" "${APP_PUBLISHER}"
VIAddVersionKey "LegalCopyright" "© 2025 ${APP_PUBLISHER}"
VIAddVersionKey "FileDescription" "Instalador de ${APP_NAME}"
VIAddVersionKey "FileVersion" "${APP_VERSION}"
VIAddVersionKey "ProductVersion" "${APP_VERSION}"

; Interfaz moderna
!include "MUI2.nsh"
!include "nsDialogs.nsh"
!include "LogicLib.nsh"
!include "FileFunc.nsh"

; ================================================================
; Configuración de la interfaz
; ================================================================

!define MUI_ABORTWARNING
!define MUI_ICON "fadi-icon.ico"
!define MUI_UNICON "fadi-icon.ico"

; Páginas del instalador
!define MUI_WELCOMEPAGE_TITLE "Bienvenido al instalador de ${APP_NAME}"
!define MUI_WELCOMEPAGE_TEXT "Este asistente le guiará en la instalación de ${APP_NAME}.$\r$\n$\r$\n${APP_NAME} es un sistema de punto de venta profesional que se conecta automáticamente con la base de datos de fadi.com.bo.$\r$\n$\r$\nHaga clic en Siguiente para continuar."

!insertmacro MUI_PAGE_WELCOME
!insertmacro MUI_PAGE_LICENSE "license.txt"
!insertmacro MUI_PAGE_DIRECTORY
!insertmacro MUI_PAGE_COMPONENTS
!insertmacro MUI_PAGE_INSTFILES

!define MUI_FINISHPAGE_RUN "$INSTDIR\Iniciar POS FADI.bat"
!define MUI_FINISHPAGE_RUN_TEXT "Iniciar ${APP_NAME}"
!define MUI_FINISHPAGE_LINK "Visitar sitio web de FADI"
!define MUI_FINISHPAGE_LINK_LOCATION "${APP_URL}"
!insertmacro MUI_PAGE_FINISH

; Páginas del desinstalador
!insertmacro MUI_UNPAGE_CONFIRM
!insertmacro MUI_UNPAGE_INSTFILES

; Idiomas
!insertmacro MUI_LANGUAGE "Spanish"

; ================================================================
; Secciones del instalador
; ================================================================

Section "Aplicación Principal" SecMain
    SectionIn RO

    ; Crear directorio de instalación
    SetOutPath "$INSTDIR"

    ; Mostrar progreso
    DetailPrint "Descargando herramientas necesarias..."

    ; Descargar e instalar Git portable
    Call InstallGit

    ; Descargar e instalar PHP
    Call InstallPHP

    ; Descargar e instalar Composer
    Call InstallComposer

    ; Clonar repositorio desde GitHub
    DetailPrint "Descargando código fuente desde GitHub..."
    Call CloneRepository

    ; Instalar dependencias
    DetailPrint "Instalando dependencias..."
    Call InstallDependencies

    ; Configurar aplicación
    Call ConfigureApplication

    ; Crear scripts de inicio
    Call CreateScripts

    ; Registrar desinstalador
    WriteUninstaller "$INSTDIR\Uninstall.exe"

    ; Registro en Windows
    WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "DisplayName" "${APP_NAME}"
    WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "UninstallString" "$INSTDIR\Uninstall.exe"
    WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "InstallLocation" "$INSTDIR"
    WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "Publisher" "${APP_PUBLISHER}"
    WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "DisplayVersion" "${APP_VERSION}"
    WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "HelpLink" "${APP_SUPPORT_URL}"
    WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "URLInfoAbout" "${APP_URL}"
    WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "URLUpdateInfo" "${APP_UPDATES_URL}"
    WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "NoModify" 1
    WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}" "NoRepair" 1

SectionEnd

Section "Accesos Directos" SecShortcuts

    ; Acceso directo en el escritorio
    CreateShortCut "$DESKTOP\${APP_NAME}.lnk" "$INSTDIR\Iniciar POS FADI.bat" "" "$INSTDIR\tools\php\php.exe" 0

    ; Acceso directo en el menú inicio
    CreateDirectory "$SMPROGRAMS\${APP_NAME}"
    CreateShortCut "$SMPROGRAMS\${APP_NAME}\${APP_NAME}.lnk" "$INSTDIR\Iniciar POS FADI.bat" "" "$INSTDIR\tools\php\php.exe" 0
    CreateShortCut "$SMPROGRAMS\${APP_NAME}\Actualizar ${APP_NAME}.lnk" "$INSTDIR\Actualizar POS FADI.bat"
    CreateShortCut "$SMPROGRAMS\${APP_NAME}\Desinstalar ${APP_NAME}.lnk" "$INSTDIR\Uninstall.exe"

SectionEnd

Section "Configuración del Firewall" SecFirewall

    ; Agregar excepción al firewall para PHP
    DetailPrint "Configurando firewall..."
    nsExec::ExecToLog 'netsh advfirewall firewall add rule name="${APP_NAME} PHP Server" dir=in action=allow program="$INSTDIR\tools\php\php.exe" enable=yes'

SectionEnd

; ================================================================
; Funciones del instalador
; ================================================================

Function InstallGit
    DetailPrint "Instalando Git portable..."
    CreateDirectory "$INSTDIR\tools\git"

    ; Descargar Git portable
    NSISdl::download "https://github.com/git-for-windows/git/releases/download/v2.42.0.windows.2/PortableGit-2.42.0.2-64-bit.7z.exe" "$TEMP\PortableGit.exe"
    Pop $R0
    ${If} $R0 == "success"
        ; Extraer Git
        ExecWait '"$TEMP\PortableGit.exe" -o"$INSTDIR\tools\git" -y'
        Delete "$TEMP\PortableGit.exe"
    ${Else}
        MessageBox MB_OK "Error descargando Git: $R0"
        Abort
    ${EndIf}
FunctionEnd

Function InstallPHP
    DetailPrint "Instalando PHP 8.2..."
    CreateDirectory "$INSTDIR\tools\php"

    ; Descargar PHP
    NSISdl::download "https://windows.php.net/downloads/releases/archives/php-8.2.12-nts-Win32-vs16-x64.zip" "$TEMP\php.zip"
    Pop $R0
    ${If} $R0 == "success"
        ; Extraer PHP
        nsisunz::UnzipToLog "$TEMP\php.zip" "$INSTDIR\tools\php"
        Delete "$TEMP\php.zip"

        ; Configurar PHP
        CopyFiles "$INSTDIR\tools\php\php.ini-development" "$INSTDIR\tools\php\php.ini"

        ; Agregar configuración personalizada
        FileOpen $4 "$INSTDIR\tools\php\php.ini" a
        FileSeek $4 0 END
        FileWrite $4 "$\r$\n; Configuracion POS FADI$\r$\n"
        FileWrite $4 "extension=curl$\r$\n"
        FileWrite $4 "extension=fileinfo$\r$\n"
        FileWrite $4 "extension=gd$\r$\n"
        FileWrite $4 "extension=mbstring$\r$\n"
        FileWrite $4 "extension=openssl$\r$\n"
        FileWrite $4 "extension=pdo_mysql$\r$\n"
        FileWrite $4 "extension=zip$\r$\n"
        FileWrite $4 "memory_limit = 256M$\r$\n"
        FileWrite $4 "max_execution_time = 60$\r$\n"
        FileWrite $4 "post_max_size = 50M$\r$\n"
        FileWrite $4 "upload_max_filesize = 50M$\r$\n"
        FileWrite $4 "date.timezone = America/La_Paz$\r$\n"
        FileClose $4
    ${Else}
        MessageBox MB_OK "Error descargando PHP: $R0"
        Abort
    ${EndIf}
FunctionEnd

Function InstallComposer
    DetailPrint "Instalando Composer..."

    ; Descargar Composer
    NSISdl::download "https://getcomposer.org/composer-stable.phar" "$INSTDIR\tools\composer.phar"
    Pop $R0
    ${If} $R0 == "success"
        ; Crear wrapper para Composer
        FileOpen $4 "$INSTDIR\tools\composer.bat" w
        FileWrite $4 "@echo off$\r$\n"
        FileWrite $4 'php "$INSTDIR\tools\composer.phar" %*$\r$\n'
        FileClose $4
    ${Else}
        MessageBox MB_OK "Error descargando Composer: $R0"
        Abort
    ${EndIf}
FunctionEnd

Function CloneRepository
    ; Clonar repositorio desde GitHub
    SetOutPath "$INSTDIR"
    ExecWait '"$INSTDIR\tools\git\bin\git.exe" clone "${REPO_URL}" app' $0
    ${If} $0 != 0
        MessageBox MB_OK "Error clonando el repositorio. Verifique su conexión a internet."
        Abort
    ${EndIf}
FunctionEnd

Function InstallDependencies
    ; Instalar dependencias de Composer
    SetOutPath "$INSTDIR\app"
    ExecWait '"$INSTDIR\tools\php\php.exe" "$INSTDIR\tools\composer.phar" install --no-dev --optimize-autoloader' $0
    ${If} $0 != 0
        MessageBox MB_OK "Error instalando dependencias PHP."
        Abort
    ${EndIf}
FunctionEnd

Function ConfigureApplication
    DetailPrint "Configurando aplicación..."

    ; Crear archivo .env
    FileOpen $4 "$INSTDIR\app\.env" w
    FileWrite $4 'APP_NAME="POS FADI Local"$\r$\n'
    FileWrite $4 'APP_ENV=production$\r$\n'
    FileWrite $4 'APP_KEY=base64:SKVTXHcp9atDuO+1HXNhNMOWD12WyM/Gh/+35Fq/gMo=$\r$\n'
    FileWrite $4 'APP_DEBUG=false$\r$\n'
    FileWrite $4 'APP_URL=http://localhost:8080$\r$\n'
    FileWrite $4 '$\r$\n'
    FileWrite $4 'LOG_CHANNEL=single$\r$\n'
    FileWrite $4 'LOG_LEVEL=error$\r$\n'
    FileWrite $4 '$\r$\n'
    FileWrite $4 '# Base de datos remota FADI$\r$\n'
    FileWrite $4 'DB_CONNECTION=mysql$\r$\n'
    FileWrite $4 'DB_HOST=fadi.com.bo$\r$\n'
    FileWrite $4 'DB_PORT=3306$\r$\n'
    FileWrite $4 'DB_DATABASE=paybol_fadi$\r$\n'
    FileWrite $4 'DB_USERNAME=paybol_admin$\r$\n'
    FileWrite $4 'DB_PASSWORD=Nagato5421$\r$\n'
    FileWrite $4 '$\r$\n'
    FileWrite $4 'CACHE_DRIVER=file$\r$\n'
    FileWrite $4 'SESSION_DRIVER=file$\r$\n'
    FileWrite $4 'QUEUE_CONNECTION=sync$\r$\n'
    FileWrite $4 '$\r$\n'
    FileWrite $4 'PRINTER_NAME=POS80$\r$\n'
    FileWrite $4 'PAPER_WIDTH=37$\r$\n'
    FileWrite $4 'PRINTER_SHOW_LOGO=true$\r$\n'
    FileWrite $4 'PRINTER_SHOW_QR=false$\r$\n'
    FileWrite $4 'PRINTER_AUTO_CUT=true$\r$\n'
    FileWrite $4 'PRINTER_SOUND_ALERT=false$\r$\n'
    FileWrite $4 'PRINTER_COMPANY_NAME="DISTRIBUIDORA"$\r$\n'
    FileWrite $4 'PRINTER_BRAND="¤ FADI ¤"$\r$\n'
    FileWrite $4 'PRINTER_FOOTER_MESSAGE="___GRACIAS POR SU COMPRA___"$\r$\n'
    FileWrite $4 'PRINTER_CONTACT="CEL: 73010688"$\r$\n'
    FileClose $4

    ; Crear directorios necesarios
    CreateDirectory "$INSTDIR\app\storage\logs"
    CreateDirectory "$INSTDIR\app\storage\framework\cache\data"
    CreateDirectory "$INSTDIR\app\storage\framework\sessions"
    CreateDirectory "$INSTDIR\app\storage\framework\views"

    ; Generar clave de aplicación
    ExecWait '"$INSTDIR\tools\php\php.exe" artisan key:generate --force'
FunctionEnd

Function CreateScripts
    ; Script para iniciar servidor
    FileOpen $4 "$INSTDIR\Iniciar POS FADI.bat" w
    FileWrite $4 '@echo off$\r$\n'
    FileWrite $4 'title POS FADI - Servidor Local$\r$\n'
    FileWrite $4 'color 0A$\r$\n'
    FileWrite $4 'echo.$\r$\n'
    FileWrite $4 'echo  ================================================$\r$\n'
    FileWrite $4 'echo               POS FADI - SERVIDOR LOCAL$\r$\n'
    FileWrite $4 'echo  ================================================$\r$\n'
    FileWrite $4 'echo.$\r$\n'
    FileWrite $4 'echo [INFO] Iniciando servidor en puerto 8080...$\r$\n'
    FileWrite $4 'echo [INFO] Acceder desde: http://localhost:8080$\r$\n'
    FileWrite $4 'echo [INFO] Presione Ctrl+C para detener el servidor$\r$\n'
    FileWrite $4 'echo.$\r$\n'
    FileWrite $4 'cd /d "$INSTDIR\app"$\r$\n'
    FileWrite $4 'set "PATH=$INSTDIR\tools\php;%PATH%"$\r$\n'
    FileWrite $4 'php artisan serve --host=0.0.0.0 --port=8080$\r$\n'
    FileWrite $4 'pause$\r$\n'
    FileClose $4

    ; Script para actualizar
    FileOpen $4 "$INSTDIR\Actualizar POS FADI.bat" w
    FileWrite $4 '@echo off$\r$\n'
    FileWrite $4 'title POS FADI - Actualizador$\r$\n'
    FileWrite $4 'echo Actualizando desde GitHub...$\r$\n'
    FileWrite $4 'cd /d "$INSTDIR\app"$\r$\n'
    FileWrite $4 'set "PATH=$INSTDIR\tools\git\bin;%PATH%"$\r$\n'
    FileWrite $4 'git pull origin master$\r$\n'
    FileWrite $4 '"$INSTDIR\tools\composer.bat" install --no-dev --optimize-autoloader$\r$\n'
    FileWrite $4 'echo Actualización completada.$\r$\n'
    FileWrite $4 'pause$\r$\n'
    FileClose $4
FunctionEnd

; ================================================================
; Desinstalador
; ================================================================

Section "Uninstall"

    ; Detener servidor si está corriendo
    nsExec::ExecToLog 'taskkill /F /IM php.exe'

    ; Eliminar archivos
    RMDir /r "$INSTDIR\app"
    RMDir /r "$INSTDIR\tools"
    Delete "$INSTDIR\*.bat"
    Delete "$INSTDIR\Uninstall.exe"
    RMDir "$INSTDIR"

    ; Eliminar accesos directos
    Delete "$DESKTOP\${APP_NAME}.lnk"
    RMDir /r "$SMPROGRAMS\${APP_NAME}"

    ; Eliminar del registro
    DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_NAME}"

    ; Eliminar regla del firewall
    nsExec::ExecToLog 'netsh advfirewall firewall delete rule name="${APP_NAME} PHP Server"'

SectionEnd

; ================================================================
; Descripciones de las secciones
; ================================================================

LangString DESC_SecMain ${LANG_SPANISH} "Aplicación principal de POS FADI con todas las dependencias necesarias."
LangString DESC_SecShortcuts ${LANG_SPANISH} "Accesos directos en el escritorio y menú inicio."
LangString DESC_SecFirewall ${LANG_SPANISH} "Configurar excepción en el firewall de Windows para el servidor PHP."

!insertmacro MUI_FUNCTION_DESCRIPTION_BEGIN
    !insertmacro MUI_DESCRIPTION_TEXT ${SecMain} $(DESC_SecMain)
    !insertmacro MUI_DESCRIPTION_TEXT ${SecShortcuts} $(DESC_SecShortcuts)
    !insertmacro MUI_DESCRIPTION_TEXT ${SecFirewall} $(DESC_SecFirewall)
!insertmacro MUI_FUNCTION_DESCRIPTION_END
