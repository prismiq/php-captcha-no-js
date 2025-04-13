<?php

/**
 * Captcha Class
 *
 * A flexible CAPTCHA generation class that can output PNG images or base64 encoded images.
 * Supports customization of various parameters like dimensions, colours, font size, etc.
 *
 * @author Jason Parker (weareprismic.com)
 * @copyright 2025
 * @license MIT
 */
class Captcha
{
    // Default configuration
    private $config = [
        'imageWidth' => 600,
        'imageHeight' => 250,
        'backgroundColor' => 0xFFFFFF,  // White background
        'font' => null,  // Will be set to Arial.ttf by default
        'fontSize' => 20,
        'numShapes' => 5,
        'spaceBetweenObjects' => 30,
        'footerText' => 'http://www.example.com/captcha',
        'shapeColors' => [0xFF, 0x8000, 0xFF0000, 0x800080, 0xFF8C00],  // Blue, Green, Red, Purple, Orange
        'words' => [
            'Apple', 'Banana', 'Orange', 'Grape', 'Kiwi', 'Lemon', 'Melon', 'Cherry', 'Peach', 'Mango',
            'Strawberry', 'Pineapple', 'Blueberry', 'Watermelon', 'Raspberry', 'Blackberry', 'Plum',
            'Apricot', 'Coconut', 'Avocado', 'Pear', 'Papaya', 'Guava', 'Fig', 'Lime', 'Tangerine',
            'Pomegranate', 'Cantaloupe', 'Honeydew', 'Dragonfruit', 'Lychee', 'Passionfruit',
            'Jackfruit', 'Durian', 'Starfruit', 'Persimmon', 'Quince', 'Mulberry', 'Elderberry',
            'Gooseberry', 'Currant', 'Tamarind', 'Sapodilla', 'Longan', 'Soursop', 'Cherimoya',
            'Jujube', 'Salak', 'Rambutan', 'Mangosteen', 'Langsat', 'Duku', 'Bacuri', 'Cupuacu',
            'Açaí', 'Camu Camu', 'Cacoa', 'Bacaba', 'Buriti', 'Brazil Nut', 'Cocona', 'Guaraná',
            'Jabuticaba', 'Cabeludinha', 'Camu Camu', 'Pitangueira', 'Cabeludinha', 'Bacuri'
        ],
        'shapeTypes' => ['circle', 'square', 'star']
    ];

    // Generated image resource
    private $image;

    // Target object and all objects for validation
    private $targetObject;

    private $allObjects = [];

    private $instructionText;

    /**
     * Constructor
     *
     * @param array $config Optional configuration array to override defaults
     */
    public function __construct($config = [])
    {
        // Start session if not started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Set default font path if not provided
        if (!isset($config['font'])) {
            $config['font'] = __DIR__ . '/Arial.ttf';
        }

        // Merge provided config with defaults
        $this->config = array_merge($this->config, $config);

        // Validate font file
        if (!file_exists($this->config['font'])) {
            throw new Exception('Font file not found: ' . $this->config['font']);
        }
    }
    
    /**
     * Create CAPTCHA with custom parameters
     *
     * @param int $width Image width
     * @param int $height Image height
     * @param string $bgColor Background color (hex format with 0x prefix)
     * @param int $numShapes Number of shapes to generate
     * @param int $spaceBetween Space between objects
     * @param int $fontSize Font size for text
     * @param string $footerText Footer text (usually a website URL)
     * @return void
     */
    public function create($width, $height, $bgColor, $numShapes, $spaceBetween, $fontSize, $footerText)
    {
        // Update configuration with the provided parameters
        $this->config['imageWidth'] = $width;
        $this->config['imageHeight'] = $height;
        $this->config['backgroundColor'] = $bgColor;
        $this->config['numShapes'] = $numShapes;
        $this->config['spaceBetweenObjects'] = $spaceBetween;
        $this->config['fontSize'] = $fontSize;
        $this->config['footerText'] = $footerText;
        
        // Generate the CAPTCHA
        $this->generate();
    }

    /**
     * Generate the CAPTCHA image
     *
     * @return void
     */
    public function generate()
    {
        // Create the image
        $this->image = imagecreatetruecolor($this->config['imageWidth'], $this->config['imageHeight']);
        if (!$this->image) {
            throw new Exception('Failed to create image. GD library issue or memory problem?');
        }

        // Set background colour
        // Convert hex string to integer if needed
        $bgColorValue = is_numeric($this->config['backgroundColor']) ? 
            intval($this->config['backgroundColor']) : 
            hexdec(str_replace('0x', '', $this->config['backgroundColor']));
            
        $bgColor = imagecolorallocate($this->image,
            ($bgColorValue >> 16) & 0xFF,
            ($bgColorValue >> 8) & 0xFF,
            $bgColorValue & 0xFF);
        imagefill($this->image, 0, 0, $bgColor);

        // Add background effects
        $this->addBackgroundEffects();

        // Create word-shape combinations
        $this->createWordShapeCombinations();

        // Add instruction text
        $this->addInstructionText();

        // Add footer text
        $this->addFooterText();
    }

    /**
     * Add various background effects to the image
     *
     * @return void
     */
    private function addBackgroundEffects()
    {
        // Add noise/patterns
        for ($i = 0; $i < 100; $i++) {
            $noiseColor = imagecolorallocate($this->image, rand(180, 255), rand(180, 255), rand(180, 255));
            $x1 = rand(0, $this->config['imageWidth']);
            $y1 = rand(0, $this->config['imageHeight']);
            $x2 = $x1 + rand(-20, 20);
            $y2 = $y1 + rand(-20, 20);
            imageline($this->image, $x1, $y1, $x2, $y2, $noiseColor);
        }

        $this->drawRandomGeometricShapes();
        $this->addDotsAndSpeckles();
        $this->drawWavyLines();
        $this->addRandomCharacters();
    }

    /**
     * Draw random geometric shapes in the background
     *
     * @return void
     */
    private function drawRandomGeometricShapes()
    {
        for ($i = 0; $i < 15; $i++) {
            $color = imagecolorallocatealpha(
                $this->image,
                rand(200, 240),
                rand(200, 240),
                rand(200, 240),
                rand(70, 110)
            );

            $x = rand(0, $this->config['imageWidth']);
            $y = rand(0, $this->config['imageHeight']);
            $size = rand(20, 60);

            $shapeType = rand(0, 3);
            switch ($shapeType) {
                case 0:  // Circle
                    imagefilledellipse($this->image, $x, $y, $size, $size, $color);
                    break;
                case 1:  // Rectangle
                    imagefilledrectangle(
                        $this->image,
                        $x,
                        $y,
                        $x + $size,
                        $y + (int)($size * 0.7),
                        $color
                    );
                    break;
                case 2:  // Triangle
                    $points = [
                        (int)$x, (int)($y + $size),
                        (int)($x + $size), (int)($y + $size),
                        (int)($x + $size / 2), (int)$y
                    ];
                    imagefilledpolygon($this->image, $points, $color);
                    break;
                case 3:  // Diamond
                    $points = [
                        (int)$x, (int)($y + $size / 2),
                        (int)($x + $size / 2), (int)$y,
                        (int)($x + $size), (int)($y + $size / 2),
                        (int)($x + $size / 2), (int)($y + $size)
                    ];
                    imagefilledpolygon($this->image, $points, $color);
                    break;
            }
        }
    }

    /**
     * Add dots and speckles to the background
     *
     * @return void
     */
    private function addDotsAndSpeckles()
    {
        for ($i = 0; $i < 500; $i++) {
            $baseColor = rand(100, 200);
            $red = $this->clamp(rand($baseColor - 30, $baseColor + 30), 0, 255);
            $green = $this->clamp(rand($baseColor - 30, $baseColor + 30), 0, 255);
            $blue = $this->clamp(rand($baseColor - 30, $baseColor + 30), 0, 255);
            $alpha = rand(0, 127);
            $color = imagecolorallocatealpha($this->image, $red, $green, $blue, $alpha);

            $x = rand(0, $this->config['imageWidth']);
            $y = rand(0, $this->config['imageHeight']);
            $size = rand(1, 3);

            $shapeType = rand(0, 2);
            switch ($shapeType) {
                case 0:  // Pixel
                    imagesetpixel($this->image, $x, $y, $color);
                    break;
                case 1:  // Tiny line
                    imageline($this->image, $x, $y, $x + rand(-1, 1), $y + rand(-1, 1), $color);
                    break;
                case 2:  // Tiny rectangle
                    imagefilledrectangle($this->image, $x, $y, $x + 1, $y + 1, $color);
                    break;
            }
        }
    }

    /**
     * Draw wavy lines in the background
     *
     * @return void
     */
    private function drawWavyLines()
    {
        for ($i = 0; $i < 10; $i++) {
            $startColorIndex = imagecolorallocatealpha(
                $this->image,
                rand(80, 180),
                rand(80, 180),
                rand(80, 180),
                rand(40, 70)
            );
            $endColorIndex = imagecolorallocatealpha(
                $this->image,
                rand(120, 220),
                rand(120, 220),
                rand(120, 220),
                rand(60, 90)
            );

            $amplitudeY = rand(8, 18);
            $frequencyX = rand(5, 12) / 100;
            $phaseY = rand(0, 314) / 100;
            $startY = rand(0, $this->config['imageHeight']);
            $offsetY = rand(-10, 10);

            $prevX = 0;
            $prevY = $startY + $offsetY;

            for ($x = 0; $x < $this->config['imageWidth']; $x += 2) {
                $y = $startY + $offsetY + $amplitudeY * sin($frequencyX * $x + $phaseY);

                $ratio = $x / $this->config['imageWidth'];
                $startColor = imagecolorsforindex($this->image, $startColorIndex);
                $endColor = imagecolorsforindex($this->image, $endColorIndex);

                $r = (int) ($startColor['red'] + ($endColor['red'] - $startColor['red']) * $ratio);
                $g = (int) ($startColor['green'] + ($endColor['green'] - $startColor['green']) * $ratio);
                $b = (int) ($startColor['blue'] + ($endColor['blue'] - $startColor['blue']) * $ratio);
                $a = (int) ($startColor['alpha'] + ($endColor['alpha'] - $startColor['alpha']) * $ratio);

                $color = imagecolorallocatealpha($this->image, $r, $g, $b, $a);

                if ($x > 0) {
                    imageline($this->image, (int)$prevX, (int)$prevY, (int)$x, (int)$y, $color);
                }
                $prevX = $x;
                $prevY = $y;
            }
        }
    }

    /**
     * Add random characters to the background
     *
     * @return void
     */
    private function addRandomCharacters()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!@#$%^&*()_+=-`[]{};\':",./<>?';
        $charactersLength = strlen($characters);
        $minSize = 8;
        $maxSize = 16;

        for ($i = 0; $i < 25; $i++) {
            $color = imagecolorallocatealpha(
                $this->image,
                rand(100, 220),
                rand(100, 220),
                rand(100, 220),
                rand(50, 100)
            );

            $x = rand(0, $this->config['imageWidth']);
            $y = rand(0, $this->config['imageHeight']);
            $size = rand($minSize, $maxSize);
            $angle = rand(-45, 45);

            $char = $characters[rand(0, $charactersLength - 1)];

            imagettftext($this->image, $size, $angle, $x, $y, $color, $this->config['font'], $char);

            for ($j = 0; $j < rand(1, 3); $j++) {
                $dotColor = imagecolorallocate($this->image, rand(100, 200), rand(100, 200), rand(100, 200));
                $dotX = $x + rand(-5, 5);
                $dotY = $y + rand(-5, 5);
                imagesetpixel($this->image, $dotX, $dotY, $dotColor);
            }
        }
    }

    /**
     * Create word-shape combinations and place them on the image
     *
     * @return void
     */
    private function createWordShapeCombinations()
    {
        shuffle($this->config['words']);
        $selectedWords = array_slice($this->config['words'], 0, min(count($this->config['words']), ceil($this->config['numShapes'] / 2)));

        $wordShapeCombinations = [];
        foreach ($selectedWords as $word) {
            foreach ($this->config['shapeTypes'] as $shape) {
                $wordShapeCombinations[] = ['word' => $word, 'shape' => $shape];
            }
        }

        shuffle($wordShapeCombinations);
        $displayCombinations = array_slice($wordShapeCombinations, 0, $this->config['numShapes']);

        $positions = [];
        $this->allObjects = [];

        foreach ($displayCombinations as $combination) {
            $word = $combination['word'];
            $shapeType = $combination['shape'];

            $textDim = $this->getTextDimensions($word, $this->config['fontSize'], $this->config['font']);

            $attempts = 0;
            $maxAttempts = 50;
            do {
                $x = rand(30, $this->config['imageWidth'] - $textDim['width'] - 30);
                $y = rand(30 + $this->config['fontSize'], $this->config['imageHeight'] - 30);
                $position = [
                    'x' => $x,
                    'y' => $y,
                    'width' => $textDim['width'],
                    'height' => $textDim['height']
                ];
                $attempts++;
            } while ($attempts < $maxAttempts && $this->positionsOverlap($positions, $position, $this->config['spaceBetweenObjects']));

            if ($attempts >= $maxAttempts) {
                continue;
            }

            $colorVal = $this->config['shapeColors'][array_rand($this->config['shapeColors'])];
            $color = imagecolorallocate($this->image,
                ($colorVal >> 16) & 0xFF,
                ($colorVal >> 8) & 0xFF,
                $colorVal & 0xFF);

            $textColor = imagecolorallocate($this->image, 0, 0, 0);
            $textDimensions = $this->applyTextDistortion($this->image, $x, $y, $word, $this->config['fontSize'], $this->config['font'], $textColor);

            $shapeBounds = null;
            if ($shapeType === 'circle') {
                $shapeBounds = $this->drawCircle($this->image, $x, $y - $textDim['height'], $textDimensions['width'], $textDim['height'], $color);
            } elseif ($shapeType === 'star') {
                $shapeBounds = $this->drawStar($this->image, $x, $y - $textDim['height'], $textDimensions['width'], $textDim['height'], $color);
            } else {
                $shapeBounds = $this->drawSquare($this->image, $x, $y - $textDim['height'], $textDimensions['width'], $textDim['height'], $color);
            }

            $positions[] = [
                'x' => $x,
                'y' => $y,
                'width' => $textDim['width'],
                'height' => $textDim['height']
            ];

            $this->allObjects[] = [
                'word' => $word,
                'shape' => $shapeType,
                'bounds' => $shapeBounds,
                'textX' => $x,
                'textY' => $y,
                'textWidth' => $textDim['width'],
                'textHeight' => $textDim['height']
            ];
        }

        $targetIndex = array_rand($this->allObjects);
        $this->targetObject = $this->allObjects[$targetIndex];

        $_SESSION['captcha_target'] = $this->targetObject;
        $_SESSION['captcha_objects'] = $this->allObjects;
    }

    /**
     * Add instruction text to the image
     *
     * @return void
     */
    private function addInstructionText()
    {
        $instructionType = rand(0, 2);
        switch ($instructionType) {
            case 0:
                $this->instructionText = "Click on the word starting with '" . strtoupper(substr($this->targetObject['word'], 0, 1)) . "'";
                break;
            case 1:
                $this->instructionText = "Click on the word ending with '" . strtolower(substr($this->targetObject['word'], -1)) . "'";
                break;
            case 2:
                $wordLength = strlen($this->targetObject['word']);
                $this->instructionText = 'Click on the word with ' . $wordLength . ' letters';
                break;
        }
        $this->instructionText .= ' in a ' . $this->targetObject['shape'];

        $instructionColor = imagecolorallocate($this->image, 0, 0, 0);
        imagettftext($this->image, 16, 0, 20, 30, $instructionColor, $this->config['font'], $this->instructionText);
    }

    /**
     * Add footer text to the image
     *
     * @return void
     */
    private function addFooterText()
    {
        $footerColor = imagecolorallocate($this->image, 0, 0, 0);
        $footerFontSize = 14;
        $footerX = 20;
        $footerY = $this->config['imageHeight'] - 20;
        imagettftext($this->image, $footerFontSize, 0, $footerX, $footerY, $footerColor, $this->config['font'], $this->config['footerText']);
    }

    /**
     * Get text dimensions
     *
     * @param string $text The text to measure
     * @param int $fontSize The font size
     * @param string $font The font file path
     * @return array Width and height of the text
     */
    private function getTextDimensions($text, $fontSize, $font)
    {
        $box = imagettfbbox($fontSize, 0, $font, $text);
        $width = abs($box[4] - $box[0]);
        $height = abs($box[5] - $box[1]);
        return ['width' => $width, 'height' => $height];
    }

    /**
     * Check if positions overlap
     *
     * @param array $positions Existing positions
     * @param array $newPos New position to check
     * @param int $margin Margin to add around positions
     * @return bool True if positions overlap
     */
    private function positionsOverlap($positions, $newPos, $margin = 20)
    {
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

    /**
     * Draw a circle around text
     *
     * @param resource $image The image resource
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $width Width of the text
     * @param int $height Height of the text
     * @param int $color Colour resource
     * @return array Bounds of the circle
     */
    private function drawCircle($image, $x, $y, $width, $height, $color)
    {
        $centerX = (int)($x + $width / 2);
        $centerY = (int)($y + $height / 2);
        $radius = (int)(max($width, $height) / 2 + 10);
        imageellipse($image, $centerX, $centerY, (int)($radius * 2), (int)($radius * 2), $color);
        return [
            'x' => $centerX - $radius,
            'y' => $centerY - $radius,
            'width' => $radius * 2,
            'height' => $radius * 2
        ];
    }

    /**
     * Draw a square around text
     *
     * @param resource $image The image resource
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $width Width of the text
     * @param int $height Height of the text
     * @param int $color Colour resource
     * @return array Bounds of the square
     */
    private function drawSquare($image, $x, $y, $width, $height, $color)
    {
        $size = max($width, $height) + 20;
        $startX = (int)($x + $width / 2 - $size / 2);
        $startY = (int)($y + $height / 2 - $size / 2);
        imagerectangle($image, $startX, $startY, (int)($startX + $size), (int)($startY + $size), $color);
        return [
            'x' => $startX,
            'y' => $startY,
            'width' => $size,
            'height' => $size
        ];
    }

    /**
     * Draw a star around text
     *
     * @param resource $image The image resource
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $width Width of the text
     * @param int $height Height of the text
     * @param int $color Colour resource
     * @return array Bounds of the star
     */
    private function drawStar($image, $x, $y, $width, $height, $color)
    {
        $size = max($width, $height) + 20;
        $centerX = $x + $width / 2;
        $centerY = $y + $height / 2;
        $radiusOuter = $size / 2;
        $radiusInner = $radiusOuter * 0.5;
        $points = [];
        $numPoints = 10;

        for ($i = 0; $i < $numPoints; $i++) {
            $angle = deg2rad(36 * $i);
            $radius = ($i % 2 === 0) ? $radiusOuter : $radiusInner;
            $points[] = (int)($centerX + $radius * cos($angle));
            $points[] = (int)($centerY + $radius * sin($angle));
        }

        imagepolygon($image, $points, $color);

        return [
            'x' => $centerX - $radiusOuter,
            'y' => $centerY - $radiusOuter,
            'width' => $radiusOuter * 2,
            'height' => $radiusOuter * 2
        ];
    }

    /**
     * Apply text distortion
     *
     * @param resource $image The image resource
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param string $word The word to draw
     * @param int $fontSize The font size
     * @param string $font The font file path
     * @param int $color Colour resource
     * @return array Bounds of the distorted text
     */
    private function applyTextDistortion($image, $x, $y, $word, $fontSize, $font, $color)
    {
        $chars = str_split($word);
        $currentX = $x;
        $totalWidth = 0;
        $charData = [];
        $minSize = $fontSize - 2;
        $maxSize = $fontSize + 3;

        foreach ($chars as $char) {
            $charSize = rand($minSize, $maxSize);
            $angle = rand(-10, 10);
            $box = imagettfbbox($charSize, $angle, $font, $char);
            $charWidth = abs($box[4] - $box[0]);
            $charHeight = abs($box[5] - $box[1]);
            $charData[] = [
                'char' => $char,
                'size' => $charSize,
                'angle' => $angle,
                'width' => $charWidth,
                'height' => $charHeight,
                'originalX' => $currentX,
                'originalY' => $y + rand(-2, 2)
            ];
            $currentX += $charWidth + rand(-2, 3);
            $totalWidth = $currentX - $x;
        }

        $amplitudeY = rand(2, 4);
        $periodX = rand(10, 20);
        $phaseY = rand(0, 314) / 100;

        $distortedBounds = [
            'x' => $x,
            'y' => $y - $fontSize,
            'width' => $totalWidth,
            'height' => 0
        ];
        $maxHeight = 0;

        foreach ($charData as $data) {
            $offsetY = $amplitudeY * sin(2 * M_PI * $data['originalX'] / $periodX + $phaseY);
            $finalY = $data['originalY'] + $offsetY;

            imagettftext(
                $image,
                $data['size'],
                $data['angle'],
                (int)$data['originalX'],
                (int)$finalY,
                $color,
                $font,
                $data['char']
            );

            $distortedBounds['y'] = min($distortedBounds['y'], $finalY - $data['size']);
            $maxHeight = max($maxHeight, $finalY - $distortedBounds['y']);
        }

        $distortedBounds['height'] = $maxHeight * 1.2;

        return $distortedBounds;
    }

    /**
     * Helper function to clamp a value within a range
     *
     * @param int $value The value to clamp
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @return int Clamped value
     */
    private function clamp($value, $min, $max)
    {
        return max($min, min($max, $value));
    }

    /**
     * Output the image as PNG
     *
     * @return void
     */
    public function outputPng()
    {
        header('Content-Type: image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }

    /**
     * Get the image as a base64 encoded string
     *
     * @return string Base64 encoded image
     */
    public function getBase64Src()
    {
        // Check if image resource is valid
        if (!is_resource($this->image) && !($this->image instanceof \GdImage)) {
            // If image is not valid, regenerate it
            $this->generate();
        }
        
        ob_start();
        imagepng($this->image);
        $imageData = ob_get_clean();
        imagedestroy($this->image);
        $this->image = null; // Set to null after destroying to prevent reuse
        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * Get the instruction text
     *
     * @return string The instruction text
     */
    public function getInstructionText()
    {
        return $this->instructionText;
    }

    /**
     * Get the target object
     *
     * @return array The target object
     */
    public function getTargetObject()
    {
        return $this->targetObject;
    }

    /**
     * Get all objects
     *
     * @return array All objects
     */
    public function getAllObjects()
    {
        return $this->allObjects;
    }
}
?>