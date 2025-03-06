<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoSolicitudGac extends Mailable
{
    use Queueable, SerializesModels;

    public $idUsuario;
    public $nombre;
    public $asunto;
    public $mensaje;
    public $id;

    public $solicitudimporte;
    public $solicitudsolicitante;
    public $solicitudbeneficiario;
    public $solicitudcon_documentos;

    public function __construct($idUsuario,$nombre, $asunto, $mensaje, $id, $solicitudimporte, $solicitudsolicitante, $solicitudbeneficiario)
    {
        $this->idUsuario = $idUsuario;
        $this->nombre = $nombre;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->id = $id;

        $this->solicitudimporte = $solicitudimporte;
        $this->solicitudsolicitante = $solicitudsolicitante;
        $this->solicitudbeneficiario = $solicitudbeneficiario;

    }

    public function build()
    {
        return $this->subject($this->asunto)
                    ->markdown('emails.solicita_autorizacion_jefes')
                    ->with([
                        'idUsuario' => $this->idUsuario,
                        'nombre' => $this->nombre,
                        'mensaje' => $this->mensaje,
                        'id' => $this->id,
                        'solicitudimporte' => $this->solicitudimporte,
                        'solicitudsolicitante' => $this->solicitudsolicitante,
                        'solicitudbeneficiario' => $this->solicitudbeneficiario
                    ]);
    }
}
