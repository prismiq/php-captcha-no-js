# CAPTCHA (NO-JS) System Documentation

This project implements a CAPTCHA (Completely Automated Public Turing test to tell Computers and Humans Apart) system designed to verify that the user interacting with the system is a human and not an automated bot. This implementation is entirely server-side and does not rely on JavaScript, ensuring compatibility with environments where JavaScript is disabled.

![Example](https://github.com/user-attachments/assets/015a5b9d-bf63-495e-906e-554436de0214)

## Key Features

- **Word and Shape-Based Challenges**: Users are instructed to click on a specific word enclosed within a shape (e.g., circle, square, or triangle) displayed on an image.
- **No JavaScript Dependency**: The CAPTCHA system is fully functional without requiring JavaScript, making it accessible for users with JavaScript disabled.
- **Enhanced Image Generation**: The CAPTCHA image is dynamically generated with randomized words, shapes, colors, and background patterns to prevent automated recognition.
- **Session-Based Validation**: The system uses PHP sessions to securely store the target word and shape for validation.
- **Customizable Difficulty**: Administrators can configure the complexity of the CAPTCHA by adjusting the number of words, shapes, and noise elements.
- **Multiple Background Effects**: Includes noise patterns, random geometric shapes, dots, wavy lines, and random characters to enhance security.

## Installation

1. **Requirements**:

   - PHP 7.2 or higher
   - GD Library enabled
   - Session support

2. **Setup**:
   - Copy all files to your web server directory
   - Ensure the web server has write permissions for session storage
   - No database configuration required

## How It Works

1. **Image Generation**:

   - The `captcha.php` script generates an image containing random words enclosed in various shapes (circle, square, triangle).
   - Background patterns, geometric shapes, and random characters are added to make the CAPTCHA harder for bots to solve.
   - A specific word and shape combination is selected as the target, and the user is instructed to click on it.

2. **User Interaction**:

   - The user clicks on the CAPTCHA image, and the click coordinates are sent to the `validate.php` script via a form submission.

3. **Validation**:
   - The `validate.php` script checks if the click coordinates match the target word's text or shape area.
   - If the click is valid, the user is redirected with a success message. Otherwise, an error message is displayed.

## Core Functions

### Captcha Class

The main class provides the following key functions:

1. **\_\_construct($config = [])**

   - Initializes the CAPTCHA with optional custom configuration
   - Starts a PHP session if not already active
   - Validates font file existence

2. **create($width, $height, $bgColor, $numShapes, $spaceBetween, $fontSize, $footerText)**

   - Sets up a customized CAPTCHA with specific dimensions and parameters
   - Parameters:
     - `$width`: Width of the CAPTCHA image in pixels
     - `$height`: Height of the CAPTCHA image in pixels
     - `$bgColor`: Background color in hexadecimal format (e.g., 0xFFFFFF for white)
     - `$numShapes`: Number of word-shape combinations to generate
     - `$spaceBetween`: Minimum space between objects in pixels
     - `$fontSize`: Font size for the words
     - `$footerText`: Text to display at the bottom of the image

3. **generate()**

   - Creates the actual CAPTCHA image with all effects and elements
   - Adds background effects, word-shape combinations, instructions, and footer text

4. **addBackgroundEffects()**

   - Adds visual noise and complexity to the CAPTCHA image
   - Includes multiple subtypes of effects (patterns, shapes, dots, lines, characters)

5. **createWordShapeCombinations()**

   - Generates random word-shape pairs and places them on the image
   - Selects one combination as the target that users need to identify

6. **outputImage()**

   - Outputs the generated CAPTCHA image as a PNG
   - Sets appropriate HTTP headers and sends the image data

7. **outputBase64()**

   - Returns the CAPTCHA image encoded as a base64 string
   - Useful for embedding directly in HTML without a separate HTTP request

8. **validateClick($x, $y)**
   - Checks if the provided coordinates match the target word-shape area
   - Returns true for successful validation, false otherwise

## Example Usage

### Basic Implementation

To implement the CAPTCHA in your form:

```php
// In your form page (e.g., index.php)
<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>CAPTCHA Example</title>
    <style>
        .captcha-container {
            margin: 20px 0;
        }
        .captcha-image {
            cursor: pointer;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <?php
    // Display error or success messages if they exist
    if (isset($_GET['error'])) {
        echo '<div style="color: red;">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    if (isset($_GET['success'])) {
        echo '<div style="color: green;">' . htmlspecialchars($_GET['success']) . '</div>';
    }
    ?>

    <h2>Contact Form with CAPTCHA</h2>
    <form action="validate.php" method="POST">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>
        </div>

        <div class="captcha-container">
            <p>Please complete the CAPTCHA by clicking on the image as instructed:</p>
            <input type="image" src="captcha.php" alt="CAPTCHA Image" name="captcha_click" class="captcha-image">
        </div>

        <div>
            <button type="submit">Submit</button>
        </div>
    </form>
</body>
</html>
```

### Customizing the CAPTCHA

You can customize the CAPTCHA by modifying the parameters:

```php
// In captcha.php
<?php
require_once 'captcha.php';

// Create a custom configuration array
$config = [
    'imageWidth' => 700,
    'imageHeight' => 300,
    'backgroundColor' => 0xF5F5F5,  // Light gray
    'font' => __DIR__ . '/Verdana.ttf',  // Using Verdana font
    'fontSize' => 24,
    'numShapes' => 8,  // More shapes for increased difficulty
    'spaceBetweenObjects' => 40,
    'footerText' => 'www.yourwebsite.com',
    // Custom colors (RGB format with 0x prefix)
    'shapeColors' => [0x4682B4, 0x228B22, 0xB22222, 0x4B0082, 0xFF8C00],
    // Custom words
    'words' => ['Apple', 'Banana', 'Cherry', 'Grape', 'Kiwi', 'Lemon', 'Mango'],
    // Available shape types
    'shapeTypes' => ['circle', 'square', 'triangle', 'star']
];

// Initialize and generate the CAPTCHA
$captcha = new Captcha($config);
$captcha->generate();
$captcha->outputImage();
```

### Validating User Clicks

The validation process checks if the user clicked on the correct word-shape combination:

```php
// In validate.php
<?php
session_start();
require_once 'captcha.php';

// Get click coordinates from form submission
$x = isset($_POST['captcha_click_x']) ? intval($_POST['captcha_click_x']) : null;
$y = isset($_POST['captcha_click_y']) ? intval($_POST['captcha_click_y']) : null;

if ($x === null || $y === null) {
    header('Location: index.php?error=' . urlencode("Error: Click coordinates not received."));
    exit;
}

// Create CAPTCHA instance
$captcha = new Captcha();

// Validate the click
$isValid = $captcha->validateClick($x, $y);

if ($isValid) {
    // CAPTCHA validation successful, process the form
    // ... your form processing code here ...

    header('Location: index.php?success=' . urlencode("Form submitted successfully!"));
} else {
    // CAPTCHA validation failed
    header('Location: index.php?error=' . urlencode("CAPTCHA validation failed. Please try again."));
}
```

## Accessibility

- **No JavaScript Required**: The system works entirely on server-side logic, ensuring compatibility with browsers that have JavaScript disabled.
- **User-Friendly Design**: The CAPTCHA image includes clear instructions and a visually distinct target to minimize user frustration.
- **High Contrast Mode**: The system supports high-contrast images for improved accessibility.

## Security Considerations

- **Randomized Challenges**: Each CAPTCHA image is unique, making it difficult for bots to predict or reuse solutions.
- **Click Validation**: The system validates clicks against both the text and shape areas to ensure accuracy.
- **Session Security**: Target data is stored in PHP sessions to prevent tampering.
- **Rate Limiting**: The system can be configured to limit repeated attempts from the same user or IP address.

## Example Scenarios

1. **Successful Validation**:

   - The user clicks on the correct word and shape.
   - The system redirects to the `index.php` page with a success message: `CAPTCHA validation successful!`.

2. **Failed Validation**:

   - The user clicks on an incorrect area.
   - The system redirects to the `index.php` page with an error message: `CAPTCHA validation failed. You didn't click on the correct word or shape.`

3. **Error Handling**:
   - If the click coordinates are not received, the system displays an error: `Error: Click coordinates not received.`

## Notes

License: https://opensource.org/license/GPL-3.0

Contact me for commercial usage on any application.
