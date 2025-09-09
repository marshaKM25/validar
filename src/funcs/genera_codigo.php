<?php
session_start();

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Configuraciones
define('ANCHO', 170);
define('ALTO', 50);
define('CODIGO_LENGTH', rand(5, 7));
define('NUM_LINEAS', 6);
define('NUM_PUNTOS', 400);

// ID del formulario
$formID = $_GET['form_id'] ?? 'default';

// Caracteres para el captcha
$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$codigo = '';
for ($i = 0; $i < CODIGO_LENGTH; $i++) {
    $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
}

// Fuentes disponibles
$fuentes = [
    '/usr/share/fonts/dejavu/DejaVuSans.ttf',
    '/usr/share/fonts/dejavu/DejaVuSerif.ttf',
    '/usr/share/fonts/dejavu/DejaVuSansMono.ttf'
];

// Guardar en sesión con hash seguro + tiempo
$_SESSION['captcha'][$formID] = [
    'hash' => hash('sha256', $codigo),
    'time' => time()
];

// Crear imagen
$imagen = imagecreatetruecolor(ANCHO, ALTO);
$colorFondo = imagecolorallocate($imagen, 255, 255, 255);
imagefill($imagen, 0, 0, $colorFondo);

// Líneas y curvas
for ($i = 0; $i < NUM_LINEAS; $i++) {
    $color = imagecolorallocate($imagen, rand(0,255), rand(0,255), rand(0,255));
    imageline($imagen, 0, rand(0, ALTO), ANCHO, rand(0, ALTO), $color);
    imagearc($imagen, rand(0, ANCHO), rand(0, ALTO), rand(20, ANCHO), rand(10, ALTO), rand(0, 360), rand(0, 360), $color);
}

// Puntos aleatorios
for ($i = 0; $i < NUM_PUNTOS; $i++) {
    $color = imagecolorallocate($imagen, rand(0,255), rand(0,255), rand(0,255));
    imagesetpixel($imagen, rand(0, ANCHO), rand(0, ALTO), $color);
}

// Escribir cada carácter con fuente y ángulo aleatorio
for ($i = 0; $i < strlen($codigo); $i++) {
    $fuente = $fuentes[array_rand($fuentes)];
    $tamanio = rand(18, 28);
    $x = 10 + ($i * 22);
    $y = rand(25, 45);
    $angulo = rand(-30, 30);
    $colorLetra = imagecolorallocate($imagen, rand(0,150), rand(0,150), rand(0,150));
    imagettftext($imagen, $tamanio, $angulo, $x, $y, $colorLetra, $fuente, $codigo[$i]);
}

// Enviar imagen y liberar memoria
imagepng($imagen);
imagedestroy($imagen);
?>




