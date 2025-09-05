<?php
/**
 * Este archivo genera y muestra la imagen CAPTCHA.
 * Utiliza la librería GD de PHP para crear una imagen con texto distorsionado
 * y ruido visual para dificultar su lectura por bots.
 **/
session_start();



header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Configuraciones
define('ANCHO', 170);
define('ALTO', 50);
define('TAMANIO_FUENTE', 20);
define('CODIGO_LENGTH', rand(5, 7));
define('NUM_LINEAS', 6);
define('NUM_PUNTOS', 400);

$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$codigo = '';
for ($i = 0; $i < CODIGO_LENGTH; $i++) {
    $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
}


// Esto elimina problemas de permisos
$fuente = '/usr/share/fonts/dejavu/DejaVuSans.ttf';

// Guardar en sesión con hash seguro + tiempo de expiración 
$_SESSION['codigo_verificacion'] = hash('sha256', $codigo);
$_SESSION['captcha_time'] = time();
$_SESSION['captcha_expires'] = 120; // 2 minutos

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

// Escribir cada carácter con distorsión
for ($i = 0; $i < strlen($codigo); $i++) {
    $x = 10 + ($i * 22); // espaciado para lafuente
    $y = rand(30, 40);
    $angulo = rand(-15, 15);
    $colorLetra = imagecolorallocate($imagen, rand(0,150), rand(0,150), rand(0,150));
    imagettftext($imagen, TAMANIO_FUENTE, $angulo, $x, $y, $colorLetra, $fuente, $codigo[$i]);
}

//enviamos los datos de la imagen y liberamos la memoria.
imagepng($imagen);
imagedestroy($imagen);

?>

