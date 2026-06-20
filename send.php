<?php
// Handler de formularios — Automotriz Gleen
// Recibe los formularios del sitio (contacto y solicitud de cotización) y los
// envía por correo a la dirección del taller usando la función mail() de PHP.
// El buzón destino vive en el mismo cPanel, así que la entrega es local (sin spam).

header('Content-Type: application/json; charset=utf-8');

// Solo se aceptan envíos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

// Honeypot anti-spam: campo oculto que los humanos dejan vacío.
// Si viene relleno, fingimos éxito y descartamos el envío.
if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true]);
    exit;
}

function limpia($v) { return trim($v ?? ''); }
function sinSaltos($v) { return str_replace(["\r", "\n"], '', $v); }

function responder_error($msg, $code = 422) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

// Envía el correo al taller. $replyEmail/$replyNombre son opcionales (Reply-To).
function enviar_correo($subject, $cuerpo, $replyEmail = '', $replyNombre = '') {
    $destino = 'contacto@gleenautomotriz.com';
    $from    = 'no-reply@gleenautomotriz.com';

    $headers  = "From: Automotriz Gleen <$from>\r\n";
    if ($replyEmail !== '') {
        $headers .= 'Reply-To: ' . sinSaltos($replyNombre) . ' <' . sinSaltos($replyEmail) . ">\r\n";
    }
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Asunto codificado en UTF-8 para que los acentos no se rompan
    $subjectMime = '=?UTF-8?B?' . base64_encode(sinSaltos($subject)) . '?=';

    return mail($destino, $subjectMime, $cuerpo, $headers);
}

$tipo = limpia($_POST['tipo'] ?? 'contacto');

if ($tipo === 'cotizacion') {
    // ----- Solicitud de cotización (modal) -----
    $nombre   = limpia($_POST['nombre']   ?? '');
    $telefono = limpia($_POST['telefono'] ?? '');
    $correo   = limpia($_POST['correo']   ?? '');
    $vehiculo = limpia($_POST['vehiculo'] ?? '');
    $anio     = limpia($_POST['anio']     ?? '');
    $servicio = limpia($_POST['servicio'] ?? '');
    $detalles = limpia($_POST['detalles'] ?? '');

    $errores = [];
    if ($nombre === '')   $errores[] = 'nombre';
    if ($telefono === '') $errores[] = 'teléfono';
    if ($vehiculo === '') $errores[] = 'vehículo';
    if ($servicio === '') $errores[] = 'servicio';
    // El correo es opcional, pero si lo ponen debe ser válido
    if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'correo';
    if ($errores) responder_error('Revisa estos campos: ' . implode(', ', $errores) . '.');

    $vehiculoCompleto = $vehiculo . ($anio !== '' ? " ($anio)" : '');
    $subject = 'Solicitud de cotización: ' . $servicio . ' – ' . $vehiculoCompleto;

    $cuerpo  = "Nueva solicitud de cotización desde gleenautomotriz.com\n\n";
    $cuerpo .= "Nombre: $nombre\n";
    $cuerpo .= "Teléfono: $telefono\n";
    $cuerpo .= 'Correo: ' . ($correo !== '' ? $correo : '(no proporcionado)') . "\n";
    $cuerpo .= "Vehículo: $vehiculoCompleto\n";
    $cuerpo .= "Servicio requerido: $servicio\n";
    if ($detalles !== '') $cuerpo .= "\nDetalles adicionales:\n$detalles\n";

    $ok = enviar_correo($subject, $cuerpo, $correo, $nombre);
} else {
    // ----- Formulario de contacto (#contacto) -----
    $nombre  = limpia($_POST['nombre']  ?? '');
    $correo  = limpia($_POST['correo']  ?? '');
    $asunto  = limpia($_POST['asunto']  ?? '');
    $mensaje = limpia($_POST['mensaje'] ?? '');

    $errores = [];
    if ($nombre === '')  $errores[] = 'nombre';
    if ($mensaje === '') $errores[] = 'mensaje';
    if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'correo';
    if ($errores) responder_error('Revisa estos campos: ' . implode(', ', $errores) . '.');

    $subject = 'Contacto web: ' . ($asunto !== '' ? $asunto : 'Mensaje desde el sitio web');

    $cuerpo  = "Nuevo mensaje desde el formulario de contacto de gleenautomotriz.com\n\n";
    $cuerpo .= "Nombre: $nombre\n";
    $cuerpo .= "Correo: $correo\n";
    $cuerpo .= 'Asunto: ' . ($asunto !== '' ? $asunto : '(sin asunto)') . "\n\n";
    $cuerpo .= "Mensaje:\n$mensaje\n";

    $ok = enviar_correo($subject, $cuerpo, $correo, $nombre);
}

if ($ok) {
    echo json_encode(['ok' => true]);
} else {
    responder_error('No se pudo enviar el mensaje. Inténtalo más tarde o llámanos.', 500);
}
