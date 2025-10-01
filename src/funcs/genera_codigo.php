<?php
session_start();
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Configuraciones
define('ANCHO', 175);
define('ALTO', 65);
define('CODIGO_LENGTH', rand(5, 7));
define('NUM_LINEAS', 22);
define('NUM_PUNTOS', 1005);
define('NUM_GRAFITI', 10);
define('NUM_MANCHAS', 30);

$formID = $_GET['form_id'] ?? 'default';

// Caracteres para captcha
$caracteres = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz123456789';
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

// Guardar en sesión
$_SESSION['captcha'][$formID] = [
    'hash' => hash('sha256', $codigo),
    'time' => time()
];

// Crear imagen
$imagen = imagecreatetruecolor(ANCHO, ALTO);
$colorFondo = imagecolorallocate($imagen, 255, 255, 255);
imagefill($imagen, 0, 0, $colorFondo);

// Líneas y arcos
for ($i = 0; $i < NUM_LINEAS; $i++) {
    $color = imagecolorallocate($imagen, rand(0,255), rand(0,255), rand(0,255));
    imageline($imagen, 0, rand(0, ALTO), ANCHO, rand(0, ALTO), $color);
    imagearc($imagen, rand(0, ANCHO), rand(0, ALTO), rand(20, ANCHO), rand(10, ALTO),
             rand(0, 360), rand(0, 360), $color);
}

// Puntos aleatorios base
for ($i = 0; $i < NUM_PUNTOS; $i++) {
    $color = imagecolorallocate($imagen, rand(0,255), rand(0,255), rand(0,255));
    imagesetpixel($imagen, rand(0, ANCHO), rand(0, ALTO), $color);
}

// Letras originales ligeramente deformadas
    for ($i = 0; $i < strlen($codigo); $i++) {
        $fuente = $fuentes[array_rand($fuentes)];
        $tamanio = rand(20, 28);

        // Posición ajustada con márgenes
        $x = 15 + ($i * 22) + rand(-3, 3);
        $y = rand(35, 55); // margen superior e inferior seguro

         // Ángulo reducido para evitar cortes
        $angulo = rand(-20, 20);

        // Color de la letra
        $colorLetra = imagecolorallocate($imagen, rand(0,120), rand(0,120), rand(0,120));

    // Dibujar letra real
    imagettftext($imagen, $tamanio, $angulo, $x, $y, $colorLetra, $fuente, $codigo[$i]);
}


// Letras tipo grafiti dispersas
for ($i = 0; $i < NUM_GRAFITI; $i++) {
    $fuente = $fuentes[array_rand($fuentes)];
    $tamanio = rand(10, 22);
    $x = rand(0, ANCHO);
    $y = rand(15, ALTO-5);
    $angulo = rand(-90, 90);
    $letra = $caracteres[random_int(0, strlen($caracteres)-1)];
    $colorGraffiti = imagecolorallocatealpha($imagen, rand(180, 230), rand(180, 230), rand(180, 230), 80);
    $offsetX = rand(-5,5);
    $offsetY = rand(-5,5);
    imagettftext($imagen, $tamanio, $angulo, $x + $offsetX, $y + $offsetY, $colorGraffiti, $fuente, $letra);
}

// Manchas semi-transparentes
for ($i = 0; $i < NUM_MANCHAS; $i++) {
    $color = imagecolorallocatealpha($imagen, rand(200, 255), rand(200, 255), rand(200, 255), rand(80, 120));
    $px = rand(0, ANCHO);
    $py = rand(0, ALTO);
    imagesetpixel($imagen, $px, $py, $color);
}

// Curvas tipo onda suaves
for ($i = 0; $i < 3; $i++) {
    $color = imagecolorallocatealpha($imagen, rand(100,200), rand(100,200), rand(100,200), 80);
    for ($x = 0; $x < ANCHO; $x+=3) {
        $y = (int)(ALTO/2 + sin($x/5 + rand(0,5))*rand(2,5));
        imagesetpixel($imagen, $x, $y, $color);
    }
}

imagepng($imagen);
imagedestroy($imagen);
?>


