<?php
session_start();

// Headers de seguridad
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Configuraciones de la imagen
define('ANCHO', 170);
define('ALTO', 50);
define('TAMANIO_FUENTE', 20);
define('CODIGO_LENGTH', rand(5, 7));
define('NUM_LINEAS', 6);
define('NUM_PUNTOS', 400);

// Caracteres y código aleatorio
$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$codigo = '';
for ($i = 0; $i < CODIGO_LENGTH; $i++) {
    $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
}

// ID único del formulario (por GET)
$formID = $_GET['form_id'] ?? 'default';

// Guardar captcha en sesión con hash y tiempo de expiración
$_SESSION['captcha'][$formID] = [
    'hash' => hash('sha256', $codigo),
    'time' => time()
];

// Fuente
$fuente = '/usr/share/fonts/dejavu/DejaVuSans.ttf';

// Crear imagen
$imagen = imagecreatetruecolor(ANCHO, ALTO);
$colorFondo = imagecolorallocate($imagen, 255, 255, 255);
imagefill($imagen, 0, 0, $colorFondo);

// Líneas
for ($i = 0; $i < NUM_LINEAS; $i++) {
    $color = imagecolorallocate($imagen, rand(0,255), rand(0,255), rand(0,255));
    imageline($imagen, 0, rand(0, ALTO), ANCHO, rand(0, ALTO), $color);
}

// Puntos
for ($i = 0; $i < NUM_PUNTOS; $i++) {
    $color = imagecolorallocate($imagen, rand(0,255), rand(0,255), rand(0,255));
    imagesetpixel($imagen, rand(0, ANCHO), rand(0, ALTO), $color);
}

// Escribir cada carácter
for ($i = 0; $i < strlen($codigo); $i++) {
    $x = 10 + ($i * 22);
    $y = rand(30, 40);
    $angulo = rand(-15, 15);
    $colorLetra = imagecolorallocate($imagen, rand(0,150), rand(0,150), rand(0,150));
    imagettftext($imagen, TAMANIO_FUENTE, $angulo, $x, $y, $colorLetra, $fuente, $codigo[$i]);
}

// Enviar imagen y liberar memoria
imagepng($imagen);
imagedestroy($imagen);
?>


