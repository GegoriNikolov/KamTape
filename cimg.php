<?php
// cimg (Captcha Image) version 2.0.0
// Created for KamTape, near exact recreation of cimg from YouTube in 2006
// It basically takes into account all of the possbilities shown on cimg archives and crams them all into one shitty PHP image generator, making it extremely accurate!
// This code is probably shit to work with; but ignore this. The important part is that if you want to use it, the variable to validate the captcha is $_SESSION['captcha'].
// $_GET['c'] = 64 character randomly generated ID followed by an ampersand for some reason

require "needed/scripts.php";

if (empty($_GET['c'])) {
    die();
}

// Generate random captcha text
$captchaText = generateCaptchaText(5);

// Save captcha text to session
$_SESSION['captcha'] = $captchaText;
$session['captcha'] = $captchaText;

// Set image dimensions
$imageWidth = 110;
$imageHeight = 44;
$captchaChars = str_split($captchaText);
$numChars = count($captchaChars);

// Create captcha image
$captchaImage = imagecreatetruecolor($imageWidth, $imageHeight);

// Generate random background colors
$backgroundColorTop = imagecolorallocate($captchaImage, rand(0, 253), rand(0, 253), rand(0, 252));
$backgroundColorBottom = imagecolorallocate($captchaImage, rand(0, 253), rand(0, 253), rand(0, 252));
$backgroundGradient = imagecreatetruecolor($imageWidth, $imageHeight);
for ($y = 0; $y < $imageHeight; $y++) {
    $ratio = $y / $imageHeight;
    $r = intval((1 - $ratio) * ($backgroundColorTop >> 16 & 0xFF) + $ratio * ($backgroundColorBottom >> 16 & 0xFF));
    $g = intval((1 - $ratio) * ($backgroundColorTop >> 8 & 0xFF) + $ratio * ($backgroundColorBottom >> 8 & 0xFF));
    $b = intval((1 - $ratio) * ($backgroundColorTop & 0xFF) + $ratio * ($backgroundColorBottom & 0xFF));
    imagefilledrectangle($backgroundGradient, 0, $y, $imageWidth - 1, $y, imagecolorallocate($backgroundGradient, $r, $g, $b));
}
imagecopy($captchaImage, $backgroundGradient, 0, 0, 0, 0, $imageWidth, $imageHeight);

// Fill the background with randomly sized rectangles
for ($i = 0; $i < 10; $i++) {
    $rectColor = imagecolorallocate($captchaImage, rand(0, 253), rand(0, 253), rand(0, 252));
    $rectWidth = rand(10, 50);
    $rectHeight = rand(10, 50);
    $rectX = rand(0, $imageWidth - $rectWidth);
    $rectY = rand(0, $imageHeight - $rectHeight);

    // Add rectangle outlines
    $outlineSize = 1;
    $outlineColor = imagecolorallocatealpha($captchaImage, 0, 0, 0, 80);
    imagerectangle(
        $captchaImage,
        $rectX - $outlineSize,
        $rectY - $outlineSize,
        $rectX + $rectWidth + $outlineSize,
        $rectY + $rectHeight + $outlineSize,
        $outlineColor
    );

    imagefilledrectangle($captchaImage, $rectX, $rectY, $rectX + $rectWidth, $rectY + $rectHeight, $rectColor);
}

// Randomly invert the colors of the captcha
if (rand(0, 1) === 1) {
    imagefilter($captchaImage, IMG_FILTER_NEGATE);
}

// Generate random text color (black or white)
$textColor = imagecolorallocate($captchaImage, rand(0, 1) === 1 ? 0 : 253, rand(0, 1) === 1 ? 0 : 253, rand(0, 1) === 1 ? 0 : 252);

// Add text outlines
$textSize = 32;
$outlineSize = 1;
$textBox = imagettfbbox($textSize, 0, '/img/ProFontWindows.ttf', $captchaText);
$textWidth = $textBox[2] - $textBox[0];
$textHeight = $textBox[3] - $textBox[5];
$textX = ($imageWidth - $textWidth) / 2;
$textY = ($imageHeight + $textHeight) / 2;

// Draw the outline layers (black or white)
$outlineColor = $textColor === 0 ? imagecolorallocatealpha($captchaImage, 255, 255, 255, rand(0, 80)) : imagecolorallocatealpha($captchaImage, 0, 0, 0, rand(0, 80));
for ($x = $textX - $outlineSize; $x <= $textX + $outlineSize; $x++) {
    for ($y = $textY - $outlineSize; $y <= $textY + $outlineSize; $y++) {
        imagettftext($captchaImage, $textSize, 0, $x, $y, $outlineColor, '/img/ProFontWindows.ttf', $captchaText);
    }
}

// Draw the actual text layer with aliased font
imageantialias($captchaImage, false);
imagettftext($captchaImage, $textSize, 0, $textX, $textY, $textColor, '/img/ProFontWindows.ttf', $captchaText);

// Draw the actual text layer with aliased font
$textSize = 32;
$textBox = imagettfbbox($textSize, 0, '/img/ProFontWindows.ttf', $captchaText);
$textWidth = $textBox[2] - $textBox[0];
$textHeight = $textBox[3] - $textBox[5];
$textX = ($imageWidth - $textWidth) / 2;
$textY = ($imageHeight + $textHeight) / 2;

imageantialias($captchaImage, false);
// Generate random text color gradient
$textColorTop = imagecolorallocatealpha($captchaImage, rand(0, 253), rand(0, 253), rand(0, 252), rand(10, 100));
$textColorBottom = imagecolorallocatealpha($captchaImage, rand(0, 253), rand(0, 253), rand(0, 252), rand(10, 100));

// Calculate text gradient colors
for ($y = 0; $y < $textHeight; $y++) {
    $ratio = $y / $textHeight;
    $r = intval((1 - $ratio) * ($textColorTop >> 16 & 0xFF) + $ratio * ($textColorBottom >> 16 & 0xFF));
    $g = intval((1 - $ratio) * ($textColorTop >> 8 & 0xFF) + $ratio * ($textColorBottom >> 8 & 0xFF));
    $b = intval((1 - $ratio) * ($textColorTop & 0xFF) + $ratio * ($textColorBottom & 0xFF));
    $gradientTextColor = imagecolorallocatealpha($captchaImage, $r, $g, $b, rand(10, 100));
    imagettftext($captchaImage, $textSize, 0, $textX, $textY, $gradientTextColor, '/img/ProFontWindows.ttf', $captchaText);
}
// Set the content type header and output the image
header('Content-type: image/jpeg');
imagejpeg($captchaImage, null, 46);
imagedestroy($captchaImage);

// Generate random captcha text
function generateCaptchaText($length)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $captchaText = '';
    $charactersLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $captchaText .= $characters[rand(0, $charactersLength - 1)];
    }
    return $captchaText;
}
?>
