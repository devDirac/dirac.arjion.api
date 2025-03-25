@component('mail::message')

# Hola {{ $nombre }}, Arjion te notifica. 

{{ $mensaje }}

A continuación, te enviamos el resumen de la solicitud:

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f4f4f4;">ID Solicitud</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f4f4f4;">Importe</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f4f4f4;">Solicitante</th>
            <!-- <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f4f4f4;">Beneficiario</th> -->
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">🚫</td>
            <td style="border: 1px solid #ddd; padding: 8px;">${{ number_format($solicitudimporte, 2) }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $solicitudsolicitante }}</td>
            <!-- <td style="border: 1px solid #ddd; padding: 8px;">{{ $solicitudbeneficiario }}</td> -->
        </tr>
    </tbody>
</table>

<br/>

Descripción de la solicitud:  {{$solicitudbeneficiario}}

<br/><br/>

@component('mail::button', ['url' => "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsuario}&id_solicitud={$id}"])
Ver solicitud 
@endcomponent

Gracias por su atención,<br>
{{ config('app.name') }}
@endcomponent
