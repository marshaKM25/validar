<?php
session_start();

//Headers de seguridad
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Configuraciones
define('ANCHO', 150);
define('ALTO', 50);
define('TAMANIO_FUENTE', 30);
define('CODIGO_LENGTH', rand(5, 8)); 
define('NUM_LINEAS', 6);
define('NUM_PUNTOS', 400);

$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$codigo = '';
for ($i = 0; $i < CODIGO_LENGTH; $i++) {
    $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
}

$fuente = realpath('../font/Consolas.ttf');

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
    $x = 15 + ($i * 20);
    $y = rand(30, 40);
    $angulo = rand(-20, 20);
    $colorLetra = imagecolorallocate($imagen, rand(0,150), rand(0,150), rand(0,150));
    imagettftext($imagen, TAMANIO_FUENTE, $angulo, $x, $y, $colorLetra, $fuente, $codigo[$i]);
}

// Enviar imagen
header('Content-Type: image/png');
imagepng($imagen);
imagedestroy($imagen);
