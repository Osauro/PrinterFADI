<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Transferencia;
use App\Models\Venta;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use App\PrintConnectors\BluetoothPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Support\Str;
use Mike42\Escpos\EscposImage;

class PosController extends Controller
{
    public $impresora;
    public $papel;
    public $mostrarLogo;
    public $mostrarQR;
    public $cortarAutomatico;
    public $alertaSonora;
    public $nombreEmpresa;
    public $marcaEmpresa;
    public $mensajePie;
    public $contacto;
    public $tipoConexion;
    public $ipImpresora;
    public $puertoImpresora;
    public $bluetoothAddress;
    public $bluetoothName;

    public function __construct()
    {
        // Configuración desde .env o valores por defecto
        $this->impresora = env('PRINTER_NAME', 'POS80');
        $this->papel = env('PAPER_WIDTH', 37);
        $this->mostrarLogo = env('PRINTER_SHOW_LOGO', true);
        $this->mostrarQR = env('PRINTER_SHOW_QR', false);
        $this->cortarAutomatico = env('PRINTER_AUTO_CUT', true);
        $this->alertaSonora = env('PRINTER_SOUND_ALERT', false);
        $this->nombreEmpresa = env('PRINTER_COMPANY_NAME', 'DISTRIBUIDORA');
        $this->marcaEmpresa = env('PRINTER_BRAND', '¤ FADI ¤');
        $this->mensajePie = env('PRINTER_FOOTER_MESSAGE', '___GRACIAS POR SU COMPRA___');
        $this->contacto = env('PRINTER_CONTACT', 'CEL: 73010688');
        
        // Nuevas configuraciones para cPanel/Web/Bluetooth
        $this->tipoConexion = env('PRINTER_CONNECTION_TYPE', 'network'); // network, windows, file, bluetooth
        $this->ipImpresora = env('PRINTER_IP', '192.168.1.100');
        $this->puertoImpresora = env('PRINTER_PORT', 9100);
        $this->bluetoothAddress = env('PRINTER_BLUETOOTH_ADDRESS', '00:11:22:33:44:55');
        $this->bluetoothName = env('PRINTER_BLUETOOTH_NAME', 'POS-Printer');
        $this->mensajePie = env('PRINTER_FOOTER_MESSAGE', '___GRACIAS POR SU COMPRA___');
        $this->contacto = env('PRINTER_CONTACT', 'CEL: 73010688');
    }

    /**
     * Obtener el conector de impresora según el tipo de conexión
     */
    private function obtenerConectorImpresora()
    {
        switch ($this->tipoConexion) {
            case 'network':
                // Para cPanel/servidores web - conexión por IP
                return new NetworkPrintConnector($this->ipImpresora, $this->puertoImpresora);
                
            case 'windows':
                // Para instalaciones locales Windows
                return new WindowsPrintConnector($this->impresora);
                
            case 'file':
                // Para servidores Linux/Unix - archivo de dispositivo
                return new FilePrintConnector($this->impresora);
                
            case 'bluetooth':
                // Para impresoras Bluetooth - multiplataforma
                return new BluetoothPrintConnector($this->bluetoothAddress, $this->bluetoothName);
                
            default:
                // Por defecto usar red para cPanel
                return new NetworkPrintConnector($this->ipImpresora, $this->puertoImpresora);
        }
    }

    /**
     * Obtener configuración completa de la impresora
     */
    public function obtenerConfiguracion()
    {
        return [
            'impresora' => $this->impresora,
            'papel' => $this->papel,
            'mostrar_logo' => $this->mostrarLogo,
            'mostrar_qr' => $this->mostrarQR,
            'cortar_automatico' => $this->cortarAutomatico,
            'alerta_sonora' => $this->alertaSonora,
            'nombre_empresa' => $this->nombreEmpresa,
            'marca_empresa' => $this->marcaEmpresa,
            'mensaje_pie' => $this->mensajePie,
            'contacto' => $this->contacto,
            'tipo_conexion' => $this->tipoConexion,
            'ip_impresora' => $this->ipImpresora,
            'puerto_impresora' => $this->puertoImpresora,
            'bluetooth_address' => $this->bluetoothAddress,
            'bluetooth_name' => $this->bluetoothName
        ];
    }

    /**
     * Guardar configuración en el archivo .env
     */
    public function guardarConfiguracion(Request $request)
    {
        try {
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            // Mapeo de campos del formulario a variables .env
            $configuraciones = [
                'PRINTER_NAME' => $request->input('printer_name', 'POS80'),
                'PAPER_WIDTH' => $request->input('paper_width', 37),
                'PRINTER_SHOW_LOGO' => $request->input('show_logo') ? 'true' : 'false',
                'PRINTER_SHOW_QR' => $request->input('show_qr') ? 'true' : 'false',
                'PRINTER_AUTO_CUT' => $request->input('auto_cut') ? 'true' : 'false',
                'PRINTER_SOUND_ALERT' => $request->input('sound_alert') ? 'true' : 'false',
                'PRINTER_COMPANY_NAME' => '"' . $request->input('company_name', 'DISTRIBUIDORA') . '"',
                'PRINTER_BRAND' => '"' . $request->input('brand', '¤ FADI ¤') . '"',
                'PRINTER_FOOTER_MESSAGE' => '"' . $request->input('footer_message', '___GRACIAS POR SU COMPRA___') . '"',
                'PRINTER_CONTACT' => '"' . $request->input('contact', 'CEL: 73010688') . '"',
                'PRINTER_CONNECTION_TYPE' => $request->input('connection_type', 'network'),
                'PRINTER_IP' => $request->input('printer_ip', '192.168.1.100'),
                'PRINTER_PORT' => $request->input('printer_port', 9100),
                'PRINTER_BLUETOOTH_ADDRESS' => $request->input('bluetooth_address', '00:11:22:33:44:55'),
                'PRINTER_BLUETOOTH_NAME' => $request->input('bluetooth_name', 'POS-Printer')
            ];

            // Actualizar cada configuración en el archivo .env
            foreach ($configuraciones as $key => $value) {
                $pattern = "/^{$key}=.*/m";
                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
                } else {
                    $envContent .= "\n{$key}={$value}";
                }
            }

            // Guardar el archivo .env actualizado
            file_put_contents($envPath, $envContent);

            // Limpiar cache de configuración para aplicar cambios
            Artisan::call('config:clear');

            return response()->json([
                'success' => true,
                'message' => 'Configuración guardada correctamente',
                'configuracion' => $configuraciones
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar configuración: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Imprimir encabezado con logo o texto según configuración
     */
    private function imprimirEncabezado($impresora)
    {
        $impresora->setJustification(Printer::JUSTIFY_CENTER);

        if ($this->mostrarLogo) {
            try {
                $logo = EscposImage::load("logo.png", false);
                $impresora->bitImage($logo);
            } catch (Exception $e) {
                // Si no se puede cargar el logo, usar texto
                $this->imprimirEncabezadoTexto($impresora);
            }
        } else {
            $this->imprimirEncabezadoTexto($impresora);
        }
    }

    /**
     * Imprimir encabezado con texto
     */
    private function imprimirEncabezadoTexto($impresora)
    {
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(2, 2);
        $impresora->text($this->nombreEmpresa . "\n");
        $impresora->setTextSize(3, 3);
        $impresora->text($this->marcaEmpresa);
    }

    /**
     * Imprimir pie de página con información de contacto
     */
    private function imprimirPie($impresora)
    {
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->feed(1);
        $impresora->setTextSize(1, 1);
        $impresora->setEmphasis(true);
        $impresora->text($this->mensajePie . "\n");
        $impresora->text($this->contacto);
        $impresora->feed(2);

        if ($this->cortarAutomatico) {
            $impresora->cut();
        }
    }

    /**
     * Mostrar la configuración actual de la impresora
     */
    public function mostrarConfiguracion()
    {
        return view('config-impresora', [
            'configuracion' => $this->obtenerConfiguracion(),
            'estado_conexion' => $this->verificarConexionImpresora()
        ]);
    }

    /**
     * Método de prueba para imprimir un recibo de test
     */
    public function imprimirPrueba()
    {
        try {
            $conector = $this->obtenerConectorImpresora();
            $impresora = new Printer($conector);

            // Imprimir encabezado
            $this->imprimirEncabezado($impresora);

            // Contenido de prueba
            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            $impresora->setTextSize(2, 2);
            $impresora->text("\n\nP R U E B A  D E  I M P R E S I O N\n\n");

            $impresora->setTextSize(1, 1);
            $impresora->setJustification(Printer::JUSTIFY_LEFT);
            $impresora->text("Fecha: " . date('d/m/Y H:i:s') . "\n");
            $impresora->text("Impresora: " . $this->impresora . "\n");
            $impresora->text("Ancho papel: " . $this->papel . " caracteres\n");
            $impresora->text("Logo: " . ($this->mostrarLogo ? 'Activado' : 'Desactivado') . "\n");
            $impresora->text("QR: " . ($this->mostrarQR ? 'Activado' : 'Desactivado') . "\n");
            $impresora->text("Corte auto: " . ($this->cortarAutomatico ? 'Activado' : 'Desactivado') . "\n");

            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            $impresora->feed(1);
            $impresora->text("=== CONFIGURACIÓN ACTUAL ===\n");
            $impresora->text("Empresa: " . $this->nombreEmpresa . "\n");
            $impresora->text("Marca: " . $this->marcaEmpresa . "\n");

            if ($this->mostrarQR) {
                $impresora->feed(1);
                $impresora->qrCode('https://example.com/test');
            }

            // Pie de página
            $this->imprimirPie($impresora);

            $impresora->close();

            return response()->json(['success' => true, 'message' => 'Impresión de prueba enviada correctamente']);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Verificar si la impresora está disponible
     */
    private function verificarConexionImpresora()
    {
        try {
            $conector = $this->obtenerConectorImpresora();
            $impresora = new Printer($conector);
            $impresora->close();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function imprimirVenta(Venta $venta)
    {
        $conector = $this->obtenerConectorImpresora();
        $impresora = new Printer($conector);

        // Imprimir encabezado usando configuración
        $this->imprimirEncabezado($impresora);

        // Título de la venta
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(2, 2);
        $impresora->text("\n\nVENTA  #" . $venta->id . "\n\n");

        // Información de la venta
        $impresora->setTextSize(1, 1);
        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text(Str::padRight("FECHA:", 9, " ") . Carbon::parse($venta->created_at)->format('d/m/Y H:i:s') . "\n");
        $impresora->text(Str::padRight("CLIENTE:", 9, " ") . $venta->cliente->nombre . "\n");
        $impresora->text(Str::padRight("CELULAR:", 9, " ") . $venta->cliente->celular . "\n");
        $impresora->text(Str::padRight("CAJERO:", 9, " ") . $venta->user->name . "\n");

        // Productos
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->feed(1);
        $impresora->text("====== P R O D U C T O S ======\n");
        $impresora->feed(1);
        $impresora->setJustification(Printer::JUSTIFY_RIGHT);

        foreach ($venta->items as $item) {
            $cantidad = '';
            if ($item->enteros) {
                $cantidad = $item->enteros . strtolower($item->producto->medida[0]) . " ";
            }
            if ($item->unidades) {
                $cantidad .= $item->unidades . "u ";
            }
            if ($item->producto_id) {
                $producto = Str::of(Str::padRight($cantidad . $item->producto->nombre, 50, '.'))->limit($this->papel);
            }
            if ($item->envase_id) {
                $producto = Str::of(Str::padRight($cantidad . $item->envase->nombre, 50, '.'))->limit($this->papel);
            }
            $precio = Str::padLeft($item->subtotal, 8, '.');
            $impresora->text($producto . $precio . "\n");
        }

        // Totales
        $impresora->setJustification(Printer::JUSTIFY_RIGHT);
        $impresora->text("\n");
        $impresora->setDoubleStrike(true);
        $impresora->text(Str::padRight("TOTAL:", 10, " "));
        $impresora->setDoubleStrike(false);
        $impresora->text(Str::padLeft(number_format($venta->total, 2, '.', ''), 7, ' ') . "\n");
        $impresora->setDoubleStrike(true);
        $impresora->text(Str::padRight("EFECTIVO:", 10, " "));
        $impresora->setDoubleStrike(false);
        $impresora->text(Str::padLeft(number_format($venta->efectivo, 2, '.', ''), 7, ' ') . "\n");

        if ($venta->online > 0) {
            $impresora->setDoubleStrike(true);
            $impresora->text(Str::padRight("ONLINE:", 10, " "));
            $impresora->setDoubleStrike(false);
            $impresora->text(Str::padLeft(number_format($venta->online, 2, '.', ''), 7, ' ') . "\n");
        }

        if ($venta->cambio > 0) {
            $impresora->setDoubleStrike(true);
            $impresora->text(Str::padRight("CAMBIO:", 10, " "));
            $impresora->setDoubleStrike(false);
            $impresora->text(Str::padLeft(number_format($venta->cambio, 2, '.', ''), 7, ' ') . "\n");
        }

        if ($venta->credito > 0) {
            $impresora->setDoubleStrike(true);
            $impresora->text(Str::padRight("CRÉDITO:", 10, " "));
            $impresora->setDoubleStrike(false);
            $impresora->text(Str::padLeft(number_format($venta->credito, 2, '.', ''), 7, ' ') . "\n");
        }

        // QR Code si está habilitado
        if ($this->mostrarQR) {
            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            $impresora->text("\nUnete al grupo de WhatsApp\nescaneando el QR y recibiras\ntodas nuestras ofertas\n\n");
            $impresora->qrCode('https://chat.whatsapp.com/HPVGxT2WaxqGlBUFJlIP5U?mode=ac_t');
        }

        // Pie de página usando configuración
        $this->imprimirPie($impresora);

        $impresora->close();
    }

    public function imprimirBoleta(Prestamo $prestamo)
    {
        $conector = $this->obtenerConectorImpresora();
        $impresora = new Printer($conector);

        // Imprimir encabezado usando configuración
        $this->imprimirEncabezado($impresora);

        // Título de boleta
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(1, 1);
        $impresora->feed(1);
        $impresora->text("**** BOLETA DE GARANTIA ****");
        $impresora->feed(1);

        // Información del préstamo
        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text(Str::padRight("FECHA:", 12, " ") . Carbon::parse($prestamo->created_at)->format('d/m/Y H:i:s') . "\n");
        $impresora->text(Str::padRight("USUARIO:", 12, " ") . $prestamo->user->name . "\n");
        $impresora->text(Str::padRight("CLIENTE:", 12, " ") . $prestamo->cliente->nombre . "\n");
        $impresora->text(Str::padRight("DEVOLUCION:", 12, " ") . Carbon::parse($prestamo->expired_at)->format('d/m/Y H:i:s') . "\n");

        // Detalle de productos
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->feed(1);
        $impresora->text("======== D E T A L L E ========\n");
        $impresora->feed(1);

        foreach ($prestamo->items as $item) {
            $cantidad = '';
            if ($item->cantidad) {
                $cantidad = $item->cantidad . strtolower($item->producto->medida[0]) . " ";
            }
            $producto = Str::of(Str::padRight($cantidad . $item->producto->nombre, 50, '.'))->limit($this->papel);
            $precio = Str::padLeft($item->subtotal, 8, '.');
            $impresora->text($producto . $precio . "\n");
        }

        // Totales
        $impresora->setJustification(Printer::JUSTIFY_RIGHT);
        $impresora->text("\n");
        $impresora->setDoubleStrike(true);
        $impresora->text(Str::padRight("TOTAL:", 10, " "));
        $impresora->setDoubleStrike(false);
        $impresora->text(Str::padLeft(number_format($prestamo->total, 2, '.', ''), 7, ' ') . "\n");
        $impresora->setDoubleStrike(true);
        $impresora->text(Str::padRight("EFECTIVO:", 10, " "));
        $impresora->setDoubleStrike(false);
        $impresora->text(Str::padLeft(number_format($prestamo->efectivo, 2, '.', ''), 7, ' ') . "\n");

        if ($prestamo->online > 0) {
            $impresora->setDoubleStrike(true);
            $impresora->text(Str::padRight("ONLINE:", 10, " "));
            $impresora->setDoubleStrike(false);
            $impresora->text(Str::padLeft(number_format($prestamo->online, 2, '.', ''), 7, ' ') . "\n");
        }

        // QR Code si está habilitado
        if ($this->mostrarQR) {
            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            $impresora->text("\nÚnete a nuestro grupo de WhatsApp\n");
            $impresora->qrCode('https://chat.whatsapp.com/HPVGxT2WaxqGlBUFJlIP5U?mode=ac_t');
        }

        // Mensaje especial para boletas
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->text("RECUERDE CUMPLIR CON EL PLAZO DE DEVOLUCIÓN, NO SE ACEPTAN RECLAMOS POSTERIORES\n\n");

        // Pie de página usando configuración
        $this->imprimirPie($impresora);

        $impresora->close();
    }

    public function imprimirTransferencia(Transferencia $transferencia)
    {
        $conector = $this->obtenerConectorImpresora();
        $impresora = new Printer($conector);

        // Imprimir encabezado usando configuración
        $this->imprimirEncabezado($impresora);

        // Título de transferencia
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(2, 2);
        $impresora->text("\n\nTRANSFERENCIA  #" . $transferencia->id . "\n\n");

        // Información de la transferencia
        $impresora->setTextSize(1, 1);
        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text(Str::padRight("FECHA:", 9, " ") . Carbon::parse($transferencia->created_at)->format('d/m/Y H:i:s') . "\n");
        $impresora->text(Str::padRight("ORIGEN:", 9, " ") . $transferencia->tiendaOrigen->nombre . "\n");
        $impresora->text(Str::padRight("DESTINO:", 9, " ") . $transferencia->tiendaDestino->nombre . "\n");
        $impresora->text(Str::padRight("USUARIO:", 9, " ") . $transferencia->user->name . "\n");

        // Productos
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->feed(1);
        $impresora->text("====== P R O D U C T O S ======\n");
        $impresora->feed(1);
        $impresora->setJustification(Printer::JUSTIFY_RIGHT);

        foreach ($transferencia->items as $item) {
            $cantidad = '';
            if ($item->enteros) {
                $cantidad = $item->enteros . strtolower($item->producto->medida[0]) . " ";
            }
            if ($item->unidades) {
                $cantidad .= $item->unidades . "u ";
            }
            $producto = Str::of(Str::padRight($cantidad . $item->producto->nombre, 50, '.'))->limit($this->papel);
            $impresora->text($producto . "\n");
        }
        $impresora->close();
    }
}
