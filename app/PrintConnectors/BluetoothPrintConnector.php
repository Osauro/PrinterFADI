<?php

namespace App\PrintConnectors;

use Mike42\Escpos\PrintConnectors\PrintConnector;
use Exception;

/**
 * Conector Bluetooth personalizado para impresoras POS
 * Utiliza diferentes métodos según el sistema operativo
 */
class BluetoothPrintConnector implements PrintConnector
{
    private $bluetoothAddress;
    private $deviceName;
    private $port;
    private $handle;
    private $isConnected = false;

    public function __construct($bluetoothAddress, $deviceName = null, $port = 1)
    {
        $this->bluetoothAddress = $bluetoothAddress;
        $this->deviceName = $deviceName ?: 'POS-Printer';
        $this->port = $port;
        $this->connect();
    }

    private function connect()
    {
        try {
            // Detectar sistema operativo
            $os = PHP_OS_FAMILY;
            
            switch ($os) {
                case 'Windows':
                    $this->connectWindows();
                    break;
                case 'Linux':
                    $this->connectLinux();
                    break;
                case 'Darwin': // macOS
                    $this->connectMacOS();
                    break;
                default:
                    throw new Exception("Sistema operativo no soportado para Bluetooth: $os");
            }
        } catch (Exception $e) {
            throw new Exception("Error conectando vía Bluetooth: " . $e->getMessage());
        }
    }

    private function connectWindows()
    {
        // En Windows, intentamos conectar vía puerto serie o red
        $comPorts = ['COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8'];
        
        foreach ($comPorts as $port) {
            try {
                $this->handle = fopen($port . ':', 'r+b');
                if ($this->handle) {
                    $this->isConnected = true;
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        if (!$this->isConnected) {
            // Fallback: intentar como impresora de red
            try {
                // Algunos adaptadores Bluetooth crean una IP virtual
                $this->handle = fsockopen($this->bluetoothAddress, $this->port, $errno, $errstr, 5);
                if ($this->handle) {
                    $this->isConnected = true;
                }
            } catch (Exception $e) {
                throw new Exception("No se pudo conectar al dispositivo Bluetooth en Windows");
            }
        }
    }

    private function connectLinux()
    {
        // En Linux, usar rfcomm o sockets Bluetooth
        $rfcommDevice = "/dev/rfcomm0";
        
        // Primero intentar dispositivo rfcomm
        if (file_exists($rfcommDevice)) {
            try {
                $this->handle = fopen($rfcommDevice, 'r+b');
                if ($this->handle) {
                    $this->isConnected = true;
                    return;
                }
            } catch (Exception $e) {
                // Continuar con otros métodos
            }
        }
        
        // Fallback: comando bluetoothctl
        $cmd = "echo 'connect {$this->bluetoothAddress}' | bluetoothctl 2>/dev/null";
        exec($cmd, $output, $return_var);
        
        if ($return_var === 0) {
            // Buscar puerto serie creado
            $serialDevices = glob('/dev/ttyS*');
            foreach ($serialDevices as $device) {
                try {
                    $this->handle = fopen($device, 'r+b');
                    if ($this->handle) {
                        $this->isConnected = true;
                        break;
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
        }
        
        if (!$this->isConnected) {
            throw new Exception("No se pudo conectar al dispositivo Bluetooth en Linux");
        }
    }

    private function connectMacOS()
    {
        // En macOS, similar a Linux pero con rutas diferentes
        $serialDevices = glob('/dev/cu.usbserial*');
        
        foreach ($serialDevices as $device) {
            try {
                $this->handle = fopen($device, 'r+b');
                if ($this->handle) {
                    $this->isConnected = true;
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        if (!$this->isConnected) {
            throw new Exception("No se pudo conectar al dispositivo Bluetooth en macOS");
        }
    }

    public function write($data)
    {
        if (!$this->isConnected || !$this->handle) {
            throw new Exception("No hay conexión Bluetooth activa");
        }

        $bytesWritten = fwrite($this->handle, $data);
        if ($bytesWritten === false) {
            throw new Exception("Error escribiendo datos vía Bluetooth");
        }
        
        // Flush para asegurar envío inmediato
        fflush($this->handle);
        
        return $bytesWritten;
    }

    public function read($length)
    {
        if (!$this->isConnected || !$this->handle) {
            throw new Exception("No hay conexión Bluetooth activa");
        }

        return fread($this->handle, $length);
    }

    public function finalize()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
            $this->isConnected = false;
        }
    }

    public function __destruct()
    {
        $this->finalize();
    }

    /**
     * Obtener información del dispositivo conectado
     */
    public function getDeviceInfo()
    {
        return [
            'address' => $this->bluetoothAddress,
            'name' => $this->deviceName,
            'port' => $this->port,
            'connected' => $this->isConnected,
            'os' => PHP_OS_FAMILY
        ];
    }

    /**
     * Verificar si la conexión está activa
     */
    public function isConnected()
    {
        return $this->isConnected && $this->handle !== null;
    }
}
