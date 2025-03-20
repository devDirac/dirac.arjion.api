<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoSolicitudNotificaNominaGac extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitudes;
    public $nombre;
    public $idUsuario;

    public function __construct($solicitudes, $nombre, $idUsuario)
    {
        $this->solicitudes = $solicitudes;
        $this->nombre  = $nombre;
        $this->idUsuario = $idUsuario;

    }

    public function build()
    {
        return $this->subject('Aviso gasto a comprobar nomina')
                    ->markdown('emails.notifica_nomina')
                    ->with([
                        'solicitudes' => $this->solicitudes, 
                        'nombre' => $this->nombre
                    ]);
    }
}
