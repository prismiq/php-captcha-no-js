<?php
/* * CAPTCHA Generation Script
 * This script generates a CAPTCHA image with random words and shapes.
 * It uses the GD library to create the image and add various effects.
 * The generated image is served as a PNG file.
 * 
 * Created By: Jason Parker
 * Website: http://www.weareprismic.com
 * Copyright (c) 2025
 * https://github.com/prismiq/php-captcha-no-js
 * 
 * License: MIT
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * Commercial use is not allowed without permission.
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Ensure session is started

// Configuration
$imageWidth = 600;
$imageHeight = 250;
$backgroundColor = 0xFFFFFF;
$font = __DIR__ . '/Arial.ttf';
$fontSize = 20;
$words = ['Apple', 'Banana', 'Orange', 'Grape', 'Kiwi', 'Lemon', 'Melon', 'Cherry', 'Peach', 'Mango', 
          'Strawberry', 'Pineapple', 'Blueberry', 'Watermelon', 'Raspberry', 'Blackberry', 'Plum', 
          'Apricot', 'Coconut', 'Avocado', 'Pear', 'Papaya', 'Guava', 'Fig', 'Lime', 'Tangerine',
          'Pomegranate', 'Cantaloupe', 'Honeydew', 'Dragonfruit', 'Lychee', 'Passionfruit',
          'Jackfruit', 'Durian', 'Starfruit', 'Persimmon', 'Quince', 'Mulberry', 'Elderberry',
          'Gooseberry', 'Currant', 'Tamarind', 'Sapodilla', 'Longan', 'Soursop', 'Cherimoya',
          'Jujube', 'Salak', 'Rambutan', 'Mangosteen', 'Langsat', 'Duku', 'Bacuri', 'Cupuacu',
          'Açaí', 'Camu Camu', 'Cacoa', 'Bacaba', 'Buriti', 'Brazil Nut', 'Cocona', 'Guaraná',
          'Jabuticaba', 'Cabeludinha', 'Camu Camu', 'Pitangueira', 'Cabeludinha', 'Bacuri'];
$shapeTypes = ['circle', 'square', 'star'];
$shapeColors = [0x0000FF, 0x008000, 0xFF0000, 0x800080, 0xFF8C00];

// Create the image
$image = imagecreatetruecolor($imageWidth, $imageHeight);
if (!$image) {
    die("Error: Failed to create image. GD library issue or memory problem?");
}

// Check if font file exists
if (!file_exists($font)) {
    die("Error: Font file not found at: " . $font);
}

// Set background color
$backgroundColor = imagecolorallocate($image, 
    ($backgroundColor >> 16) & 0xFF,
    ($backgroundColor >> 8) & 0xFF,
    $backgroundColor & 0xFF
);
imagefill($image, 0, 0, $backgroundColor);

// Add some noise/patterns to background for added security
for ($i = 0; $i < 100; $i++) {
    $noiseColor = imagecolorallocate($image, rand(180, 255), rand(180, 255), rand(180, 255));
    $x1 = rand(0, $imageWidth);
    $y1 = rand(0, $imageHeight);
    $x2 = $x1 + rand(-20, 20);
    $y2 = $y1 + rand(-20, 20);
    imageline($image, $x1, $y1, $x2, $y2, $noiseColor);
}

// Function to draw random geometric shapes in the background
function drawRandomGeometricShapes1($image, $width, $height, $count = 15) {
    for ($i = 0; $i < $count; $i++) {
        // Use very light colors for the background shapes
        $color = imagecolorallocatealpha(
            $image, 
            rand(200, 240), 
            rand(200, 240), 
            rand(200, 240),
            rand(70, 110) // Higher alpha for transparency
        );
        
        $x = rand(0, $width);
        $y = rand(0, $height);
        $size = rand(20, 60);
        
        // Select a random shape type
        $shapeType = rand(0, 3);
        switch ($shapeType) {
            case 0: // Circle
                imagefilledellipse($image, $x, $y, $size, $size, $color);
                break;
            case 1: // Rectangle
                imagefilledrectangle(
                    $image, 
                    $x, 
                    $y, 
                    $x + $size, 
                    $y + $size * 0.7, 
                    $color
                );
                break;
            case 2: // Triangle
                $points = [
                    $x, $y + $size,
                    $x + $size, $y + $size,
                    $x + $size/2, $y
                ];
                imagefilledpolygon($image, $points, 3, $color);
                break;
            case 3: // Diamond
                $points = [
                    $x, $y + $size/2,
                    $x + $size/2, $y,
                    $x + $size, $y + $size/2,
                    $x + $size/2, $y + $size
                ];
                imagefilledpolygon($image, $points, 4, $color);
                break;
        }
    }
}

function drawRandomGeometricShapes($image, $width, $height, $count = 20) { // Increased count
    for ($i = 0; $i < $count; $i++) {
        $color = imagecolorallocatealpha(
            $image,
            rand(200, 240),
            rand(200, 240),
            rand(200, 240),
            rand(50, 100) // More transparency
        );

        $x = rand(0, $width);
        $y = rand(0, $height);
        $size = rand(10, 50); // Smaller sizes

        $shapeType = rand(0, 3);
        switch ($shapeType) {
            case 0: // Circle
                imagefilledellipse($image, $x, $y, $size, $size, $color);
                break;
            case 1: // Rectangle
                imagefilledrectangle(
                    $image,
                    $x,
                    $y,
                    $x + $size,
                    $y + rand($size * 0.5, $size * 0.8), // Vary height
                    $color
                );
                break;
            case 2: // Triangle
                $points = [
                    $x, $y + $size,
                    $x + $size, $y + $size,
                    $x + $size/2, $y
                ];
                imagefilledpolygon($image, $points, 3, $color);
                break;
            case 3: // Diamond
                $points = [
                    $x, $y + $size/2,
                    $x + $size/2, $y,
                    $x + $size, $y + $size/2,
                    $x + $size/2, $y + $size
                ];
                imagefilledpolygon($image, $points, 4, $color);
                break;
        }
    }
}

function addDotsAndSpeckles($image, $width, $height, $count = 500) {
    for ($i = 0; $i < $count; $i++) {
        // Vary color based on position
        $baseColor = rand(100, 200);
        $red = clamp(rand($baseColor - 30, $baseColor + 30), 0, 255);
        $green = clamp(rand($baseColor - 30, $baseColor + 30), 0, 255);
        $blue = clamp(rand($baseColor - 30, $baseColor + 30), 0, 255);
        $alpha = rand(0, 127); // Ensure alpha is within the valid range
        $color = imagecolorallocatealpha($image, $red, $green, $blue, $alpha); // Add transparency variation

        $x = rand(0, $width);
        $y = rand(0, $height);
        $size = rand(1, 3); // Slightly larger dots sometimes

        // Draw small shapes instead of just pixels
        $shapeType = rand(0, 2);
        switch ($shapeType) {
            case 0: // Pixel
                imagesetpixel($image, $x, $y, $color);
                break;
            case 1: // Tiny line
                imageline($image, $x, $y, $x + rand(-1, 1), $y + rand(-1, 1), $color);
                break;
            case 2: // Tiny rectangle
                imagefilledrectangle($image, $x, $y, $x + 1, $y + 1, $color);
                break;
        }
    }
}

// More advanced drawWavyLines function (Corrected)
function drawWavyLines($image, $width, $height, $count = 10) {
    for ($i = 0; $i < $count; $i++) {
        // Vary color along the line
        $startColorIndex = imagecolorallocatealpha(
            $image,
            rand(80, 180),
            rand(80, 180),
            rand(80, 180),
            rand(40, 70)
        );
        $endColorIndex = imagecolorallocatealpha(
            $image,
            rand(120, 220),
            rand(120, 220),
            rand(120, 220),
            rand(60, 90)
        );

        $amplitudeY = rand(8, 18);
        $frequencyX = rand(5, 12) / 100;
        $phaseY = rand(0, 314) / 100;
        $startY = rand(0, $height);
        $offsetY = rand(-10, 10); // Introduce vertical offset for each line

        $prevX = 0;
        $prevY = $startY + $offsetY;

        for ($x = 0; $x < $width; $x += 2) {
            $y = $startY + $offsetY + $amplitudeY * sin($frequencyX * $x + $phaseY);

            // Interpolate color along the line
            $ratio = $x / $width;
            $startColor = imagecolorsforindex($image, $startColorIndex);
            $endColor = imagecolorsforindex($image, $endColorIndex);

            $r = (int) ($startColor['red'] + ($endColor['red'] - $startColor['red']) * $ratio);
            $g = (int) ($startColor['green'] + ($endColor['green'] - $startColor['green']) * $ratio);
            $b = (int) ($startColor['blue'] + ($endColor['blue'] - $startColor['blue']) * $ratio);
            $a = (int) ($startColor['alpha'] + ($endColor['alpha'] - $startColor['alpha']) * $ratio);

            $color = imagecolorallocatealpha($image, $r, $g, $b, $a);

            if ($x > 0) {
                imageline($image, $prevX, $prevY, $x, $y, $color);
            }
            $prevX = $x;
            $prevY = $y;
        }
    }
}

// Helper function to keep values within 0-255
function clamp($value, $min, $max) {
    return max($min, min($max, $value));
}

// Function to add random characters in the background
// Enhanced addRandomCharacters function
function addRandomCharacters($image, $width, $height, $font, $count = 25) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!@#$%^&*()_+=-`[]{};\':",./<>?';
    $charactersLength = strlen($characters);
    $possibleFonts = [__DIR__ . '/Arial.ttf', __DIR__ . '/Verdana.ttf', __DIR__ . '/Tahoma.ttf']; // Add more fonts
    $minSize = 8;
    $maxSize = 16;

    for ($i = 0; $i < $count; $i++) {
        $color = imagecolorallocatealpha(
            $image,
            rand(100, 220),
            rand(100, 220),
            rand(100, 220),
            rand(50, 100) // More variation in transparency
        );

        $x = rand(0, $width);
        $y = rand(0, $height);
        $size = rand($minSize, $maxSize);
        $angle = rand(-45, 45); // Wider angle range

        // Randomly select a font
        $selectedFont = $possibleFonts[array_rand($possibleFonts)];
        if (!file_exists($selectedFont)) {
            $selectedFont = $font; // Fallback to default if not found
        }

        $char = $characters[rand(0, $charactersLength - 1)];

        imagettftext($image, $size, $angle, $x, $y, $color, $selectedFont, $char);

        // Add small, randomly placed dots near the characters
        for ($j = 0; $j < rand(1, 3); $j++) {
            $dotColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
            $dotX = $x + rand(-5, 5);
            $dotY = $y + rand(-5, 5);
            imagesetpixel($image, $dotX, $dotY, $dotColor);
        }
    }
}

// Enhanced applyAdvancedTextDistortion function
function applyTextDistortion($image, $x, $y, $word, $fontSize, $font, $color) {
    $chars = str_split($word);
    $currentX = $x;
    $totalWidth = 0;
    $charData = [];
    $possibleFonts = [__DIR__ . '/Arial.ttf', __DIR__ . '/Verdana.ttf', __DIR__ . '/Tahoma.ttf'];
    $minSize = $fontSize - 2;
    $maxSize = $fontSize + 3;

    // First pass: Calculate original positions and dimensions
    foreach ($chars as $char) {
        $charFont = $possibleFonts[array_rand($possibleFonts)];
        if (!file_exists($charFont)) {
            $charFont = $font;
        }
        $charSize = rand($minSize, $maxSize);
        $angle = rand(-10, 10);
        $box = imagettfbbox($charSize, $angle, $charFont, $char);
        $charWidth = abs($box[4] - $box[0]);
        $charHeight = abs($box[5] - $box[1]);
        $charData[] = [
            'char' => $char,
            'font' => $charFont,
            'size' => $charSize,
            'angle' => $angle,
            'width' => $charWidth,
            'height' => $charHeight,
            'originalX' => $currentX,
            'originalY' => $y + rand(-2, 2) // Initial vertical variation
        ];
        $currentX += $charWidth + rand(-2, 3);
        $totalWidth = $currentX - $x;
    }

    // Apply distortion based on total width and character positions
    $amplitudeY = rand(2, 4);
    $periodX = rand(10, 20);
    $phaseY = rand(0, 314) / 100;

    $distortedBounds = [
        'x' => $x,
        'y' => $y - $fontSize,
        'width' => $totalWidth,
        'height' => 0 // Will be updated
    ];
    $maxHeight = 0;

    // Second pass: Draw with distortion
    foreach ($charData as $data) {
        $offsetY = $amplitudeY * sin(2 * M_PI * $data['originalX'] / $periodX + $phaseY);
        $finalY = $data['originalY'] + $offsetY;

        imagettftext(
            $image,
            $data['size'],
            $data['angle'],
            $data['originalX'],
            $finalY,
            $color,
            $data['font'],
            $data['char']
        );

        $distortedBounds['y'] = min($distortedBounds['y'], $finalY - $data['size']);
        $maxHeight = max($maxHeight, $finalY - $distortedBounds['y']);
    }

    $distortedBounds['height'] = $maxHeight * 1.2; // Add some padding

    return $distortedBounds;
}

// Create word-shape combinations with duplicates
$wordShapeCombinations = [];
$numShapes = 5; // Total number of shapes/words to display

// Shuffle words first
shuffle($words);
$selectedWords = array_slice($words, 0, min(count($words), ceil($numShapes/2)));

// Create duplicate entries with different shapes
foreach ($selectedWords as $word) {
    // Add this word in both shapes (circle and square)
    $wordShapeCombinations[] = ['word' => $word, 'shape' => 'circle'];
    $wordShapeCombinations[] = ['word' => $word, 'shape' => 'square'];
    $wordShapeCombinations[] = ['word' => $word, 'shape' => 'star'];
}

// Shuffle the combinations and pick the first $numShapes
shuffle($wordShapeCombinations);
$displayCombinations = array_slice($wordShapeCombinations, 0, $numShapes);

// Function to get text dimensions
function getTextDimensions($text, $fontSize, $font) {
    $box = imagettfbbox($fontSize, 0, $font, $text);
    $width = abs($box[4] - $box[0]);
    $height = abs($box[5] - $box[1]);
    return ['width' => $width, 'height' => $height];
}

// Function to check if positions overlap
function positionsOverlap($positions, $newPos, $margin = 20) {
    foreach ($positions as $pos) {
        if (
            $newPos['x'] < $pos['x'] + $pos['width'] + $margin && 
            $newPos['x'] + $newPos['width'] + $margin > $pos['x'] && 
            $newPos['y'] < $pos['y'] + $pos['height'] + $margin && 
            $newPos['y'] + $newPos['height'] + $margin > $pos['y']
        ) {
            return true;
        }
    }
    return false;
}

// Function to draw a circle around text
function drawCircle($image, $x, $y, $width, $height, $color) {
    $centerX = $x + $width / 2;
    $centerY = $y + $height / 2;
    $radius = max($width, $height) / 2 + 10; // Add some padding
    imageellipse($image, $centerX, $centerY, $radius * 2, $radius * 2, $color);
    return [
        'x' => $centerX - $radius,
        'y' => $centerY - $radius,
        'width' => $radius * 2,
        'height' => $radius * 2
    ];
}

// Function to draw a square around text
function drawSquare($image, $x, $y, $width, $height, $color) {
    $size = max($width, $height) + 20; // Add some padding
    $startX = $x + $width/2 - $size/2;
    $startY = $y + $height/2 - $size/2;
    imagerectangle($image, $startX, $startY, $startX + $size, $startY + $size, $color);
    return [
        'x' => $startX,
        'y' => $startY,
        'width' => $size,
        'height' => $size
    ];
}

function drawStar($image, $x, $y, $width, $height, $color) {
    $size = max($width, $height) + 20; // Add some padding
    $centerX = $x + $width / 2;
    $centerY = $y + $height / 2;
    $radiusOuter = $size / 2;
    $radiusInner = $radiusOuter * 0.5; // Inner radius for the star points
    $points = [];
    $numPoints = 10; // 5 outer points and 5 inner points

    for ($i = 0; $i < $numPoints; $i++) {
        $angle = deg2rad(36 * $i); // 360 degrees divided by 10 points
        $radius = ($i % 2 === 0) ? $radiusOuter : $radiusInner;
        $points[] = $centerX + $radius * cos($angle);
        $points[] = $centerY + $radius * sin($angle);
    }

    imagepolygon($image, $points, $numPoints, $color); // Use imagepolygon for outline only

    return [
        'x' => $centerX - $radiusOuter,
        'y' => $centerY - $radiusOuter,
        'width' => $radiusOuter * 2,
        'height' => $radiusOuter * 2
    ];
}

// Apply background effects before drawing the words
drawRandomGeometricShapes($image, $imageWidth, $imageHeight);
addDotsAndSpeckles($image, $imageWidth, $imageHeight);
drawWavyLines($image, $imageWidth, $imageHeight);
addRandomCharacters($image, $imageWidth, $imageHeight, $font);

// Add smaller words and objects to confuse bots
function addSmallerWordsAndObjects($image, $width, $height, $font, $fontSize, $count = 10) {
    $extraWords = ['Cat', 'Dog', 'Sun', 'Moon', 'Star', 'Tree', 'Fish', 'Bird', 'Car', 'Boat'];
    for ($i = 0; $i < $count; $i++) {
        $color = imagecolorallocate(
            $image, 
            rand(100, 200), 
            rand(100, 200), 
            rand(100, 200)
        );
        $x = rand(20, $width - 20);
        $y = rand(20, $height - 20);
        $size = rand(8, 14);
        $angle = rand(-30, 30);
        $word = $extraWords[array_rand($extraWords)];
        imagettftext($image, $size, $angle, $x, $y, $color, $font, $word);
    }
}

// Call the function to add smaller words and objects
addSmallerWordsAndObjects($image, $imageWidth, $imageHeight, $font, $fontSize);

// Place words with shapes on the image
$positions = [];
$wordObjects = [];

foreach ($displayCombinations as $combination) {
    $word = $combination['word'];
    $shapeType = $combination['shape'];

    // Get text dimensions
    $textDim = getTextDimensions($word, $fontSize, $font);

    // Find a non-overlapping position
    $attempts = 0;
    $maxAttempts = 50;
    do {
        $x = rand(30, $imageWidth - $textDim['width'] - 30);
        $y = rand(30 + $fontSize, $imageHeight - 30);
        $position = [
            'x' => $x,
            'y' => $y,
            'width' => $textDim['width'],
            'height' => $textDim['height']
        ];
        $attempts++;
    } while ($attempts < $maxAttempts && positionsOverlap($positions, $position));

    if ($attempts >= $maxAttempts) {
        continue; // Skip this word if we can't find a good position
    }

    // Choose a random color for the shape
    $colorVal = $shapeColors[array_rand($shapeColors)];
    $color = imagecolorallocate($image, 
        ($colorVal >> 16) & 0xFF,
        ($colorVal >> 8) & 0xFF,
        $colorVal & 0xFF
    );

    // Draw the text with distortion instead of simple text rendering
    $textColor = imagecolorallocate($image, 0, 0, 0);
    $textDimensions = applyTextDistortion($image, $x, $y, $word, $fontSize, $font, $textColor);

    // Draw the shape around the text
    $shapeBounds = null;
    if ($shapeType === 'circle') {
        $shapeBounds = drawCircle($image, $x, $y - $textDim['height'], $textDimensions['width'], $textDim['height'], $color);
    } elseif ($shapeType === 'star') {
        $shapeBounds = drawStar($image, $x, $y - $textDim['height'], $textDimensions['width'], $textDim['height'], $color);
    } else { // square
        $shapeBounds = drawSquare($image, $x, $y - $textDim['height'], $textDimensions['width'], $textDim['height'], $color);
    }

    // Store the position
    $positions[] = [
        'x' => $x,
        'y' => $y,
        'width' => $textDim['width'],
        'height' => $textDim['height']
    ];

    // Store the word object
    $wordObjects[] = [
        'word' => $word,
        'shape' => $shapeType,
        'bounds' => $shapeBounds,
        'textX' => $x,
        'textY' => $y,
        'textWidth' => $textDim['width'],
        'textHeight' => $textDim['height']
    ];
}

// Select a random word object as the target
$targetIndex = array_rand($wordObjects);
$targetObject = $wordObjects[$targetIndex];

// Store the target and all objects in the session
$_SESSION['captcha_target'] = $targetObject;
$_SESSION['captcha_objects'] = $wordObjects;

// Create instruction text
$instructionType = rand(0, 2);
switch ($instructionType) {
    case 0:
        $instructionText = "Click on the word starting with '" . strtoupper(substr($targetObject['word'], 0, 1)) . "'";
        break;
    case 1:
        $instructionText = "Click on the word ending with '" . strtolower(substr($targetObject['word'], -1)) . "'";
        break;
    case 2:
        $wordLength = strlen($targetObject['word']);
        $instructionText = "Click on the word with " . $wordLength . " letters";
        break;
}
$instructionText .= " in a " . $targetObject['shape'];
$instructionColor = imagecolorallocate($image, 0, 0, 0);
imagettftext($image, 16, 0, 20, 30, $instructionColor, $font, $instructionText);

// Add text at the bottom of the image to inform the user
$footerText = "http://www.example.com/captcha";
$footerColor = imagecolorallocate($image, 0, 0, 0);
$footerFontSize = 14;
$footerX = 20;
$footerY = $imageHeight - 20;
imagettftext($image, $footerFontSize, 0, $footerX, $footerY, $footerColor, $font, $footerText);

// Output the image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>