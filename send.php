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

// Envía el correo al taller con un archivo adjunto (multipart/mixed).
// $adjunto = ['nombre' => ..., 'tipo' => ..., 'datos' => contenido binario].
function enviar_correo_adjunto($subject, $cuerpo, $adjunto, $replyEmail = '', $replyNombre = '') {
    $destino = 'contacto@gleenautomotriz.com';
    $from    = 'no-reply@gleenautomotriz.com';
    $boundary = '=_gleen_' . md5(uniqid((string) mt_rand(), true));

    $headers  = "From: Automotriz Gleen <$from>\r\n";
    if ($replyEmail !== '') {
        $headers .= 'Reply-To: ' . sinSaltos($replyNombre) . ' <' . sinSaltos($replyEmail) . ">\r\n";
    }
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $subjectMime = '=?UTF-8?B?' . base64_encode(sinSaltos($subject)) . '?=';
    $nombreAdj   = sinSaltos($adjunto['nombre']);

    $msg  = "--$boundary\r\n";
    $msg .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $msg .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $msg .= $cuerpo . "\r\n\r\n";
    $msg .= "--$boundary\r\n";
    $msg .= 'Content-Type: ' . $adjunto['tipo'] . "; name=\"$nombreAdj\"\r\n";
    $msg .= "Content-Transfer-Encoding: base64\r\n";
    $msg .= "Content-Disposition: attachment; filename=\"$nombreAdj\"\r\n\r\n";
    $msg .= chunk_split(base64_encode($adjunto['datos'])) . "\r\n";
    $msg .= "--$boundary--";

    return mail($destino, $subjectMime, $msg, $headers);
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
} elseif ($tipo === 'empleo') {
    // ----- Solicitud de empleo (empleo.html) -----
    $nombre    = limpia($_POST['nombre']    ?? '');
    $apellidos = limpia($_POST['apellidos'] ?? '');
    $correo    = limpia($_POST['correo']    ?? '');
    $pais      = limpia($_POST['pais']      ?? '');
    $telefono  = limpia($_POST['telefono']  ?? '');
    $mensaje   = limpia($_POST['mensaje']   ?? '');

    $errores = [];
    if ($nombre === '')    $errores[] = 'nombre';
    if ($apellidos === '') $errores[] = 'apellidos';
    if ($telefono === '')  $errores[] = 'teléfono';
    if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'correo';
    if ($errores) responder_error('Revisa estos campos: ' . implode(', ', $errores) . '.');

    // Validación del CV adjunto (obligatorio, solo PDF, máx 5 MB)
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        responder_error('Adjunta tu CV en formato PDF.');
    }
    $cv = $_FILES['cv'];
    if ($cv['size'] > 5 * 1024 * 1024) {
        responder_error('El CV supera el tamaño máximo de 5 MB.');
    }
    $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
    $mime  = $finfo ? finfo_file($finfo, $cv['tmp_name']) : 'application/pdf';
    if ($finfo) finfo_close($finfo);
    $esPdf = $mime === 'application/pdf' || preg_match('/\.pdf$/i', $cv['name']);
    if (!$esPdf) {
        responder_error('El CV debe ser un archivo PDF.');
    }

    $datosCv = file_get_contents($cv['tmp_name']);
    if ($datosCv === false) {
        responder_error('No se pudo leer el CV. Inténtalo de nuevo.', 500);
    }

    // Nombre de archivo limpio para el adjunto
    $nombreArchivo = preg_replace('/[^A-Za-z0-9._-]/', '_', $cv['name']);
    if (!preg_match('/\.pdf$/i', $nombreArchivo)) $nombreArchivo .= '.pdf';

    $nombreCompleto = trim($nombre . ' ' . $apellidos);
    $subject = 'Solicitud de empleo: ' . $nombreCompleto;

    $cuerpo  = "Nueva solicitud de empleo desde gleenautomotriz.com\n\n";
    $cuerpo .= "Nombre: $nombreCompleto\n";
    $cuerpo .= "Correo: $correo\n";
    $cuerpo .= 'Teléfono: ' . $telefono . ($pais !== '' ? " ($pais)" : '') . "\n";
    if ($mensaje !== '') $cuerpo .= "\nMensaje:\n$mensaje\n";
    $cuerpo .= "\nEl CV se adjunta a este correo.\n";

    $ok = enviar_correo_adjunto($subject, $cuerpo, [
        'nombre' => $nombreArchivo,
        'tipo'   => 'application/pdf',
        'datos'  => $datosCv,
    ], $correo, $nombreCompleto);
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
