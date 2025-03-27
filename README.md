"""
# CAPTCHA System Documentation

This project implements a CAPTCHA (Completely Automated Public Turing test to tell Computers and Humans Apart) system designed to verify that the user interacting with the system is a human and not an automated bot. This implementation is entirely server-side and does not rely on JavaScript, ensuring compatibility with environments where JavaScript is disabled.

## Key Features

- **Word and Shape-Based Challenges**: Users are instructed to click on a specific word enclosed within a shape (e.g., circle, square, or star) displayed on an image.
- **No JavaScript Dependency**: The CAPTCHA system is fully functional without requiring JavaScript, making it accessible for users with JavaScript disabled.
- **Dynamic Image Generation**: The CAPTCHA image is dynamically generated with randomized words, shapes, and background noise to prevent automated recognition.
- **Session-Based Validation**: The system uses PHP sessions to securely store the target word and shape for validation.

## How It Works

1. **Image Generation**:
    - The `captcha.php` script generates an image containing random words enclosed in various shapes (circle, square, star).
    - Background noise, geometric shapes, and random characters are added to make the CAPTCHA harder for bots to solve.
    - A specific word and shape combination is selected as the target, and the user is instructed to click on it.

2. **User Interaction**:
    - The user clicks on the CAPTCHA image, and the click coordinates are sent to the `validate.php` script via a form submission.

3. **Validation**:
    - The `validate.php` script checks if the click coordinates match the target word's text or shape area.
    - If the click is valid, the user is redirected with a success message. Otherwise, an error message is displayed.

## Example Usage

### Displaying the CAPTCHA

The CAPTCHA is displayed on the `index.php` page:

```html
<form action="validate.php" method="POST">
     <input type="image" src="captcha.php" alt="CAPTCHA Image" name="captcha_click" class="captcha-image">
</form>
```

The user is instructed to click on the word and shape specified in the dynamically generated image.

### Validation Logic

The `validate.php` script processes the user's click:

```php
if ($clickedOnText || $clickedOnShape) {
     header('Location: index.php?success=' . urlencode("CAPTCHA validation successful!"));
} else {
     header('Location: index.php?error=' . urlencode("CAPTCHA validation failed. You didn't click on the correct word."));
}
```

It ensures that the click coordinates match either the text or the shape area of the target word.

## Accessibility

- **No JavaScript Required**: The system works entirely on server-side logic, ensuring compatibility with browsers that have JavaScript disabled.
- **User-Friendly Design**: The CAPTCHA image includes clear instructions and a visually distinct target to minimize user frustration.

## Security Considerations

- **Randomized Challenges**: Each CAPTCHA image is unique, making it difficult for bots to predict or reuse solutions.
- **Click Validation**: The system validates clicks against both the text and shape areas to ensure accuracy.
- **Session Security**: Target data is stored in PHP sessions to prevent tampering.

## Example Scenarios

1. **Successful Validation**:
    - The user clicks on the correct word and shape.
    - The system redirects to the `index.php` page with a success message: `CAPTCHA validation successful!`.

2. **Failed Validation**:
    - The user clicks on an incorrect area.
    - The system redirects to the `index.php` page with an error message: `CAPTCHA validation failed. You didn't click on the correct word.`

3. **Error Handling**:
    - If the click coordinates are not received, the system displays an error: `Error: Click coordinates not received.`

## Notes

Licence: https://opensource.org/license/GPL-3.0
Contact me for commercial usage on any application.