@component('mail::message')

# Hola {{ $nombre }}, Arjion te notifica. 
<br>
este es un listado de las solicitudes a las que se le requiere un descuento por nomina

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f4f4f4;">ID Solicitud</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f4f4f4;">Importe</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f4f4f4;">ver detalle</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($solicitudes as $solicitud)
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">🚫</td>
            <td style="border: 1px solid #ddd; padding: 8px;">${{ number_format($solicitud->importe, 2) }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">
                @component('mail::button', ['url' => "http://localhost:3000/gac-detalle-solicitud?id={$idUsuario}&id_solicitud={$solicitud->id}"])
                    Ver solicitud 
                @endcomponent
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<br/><br/><br/>

Gracias por su atención,<br>
{{ config('app.name') }}
@endcomponent
