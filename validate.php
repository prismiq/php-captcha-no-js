<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clickX = $_POST['captcha_click_x'] ?? null;
    $clickY = $_POST['captcha_click_y'] ?? null;
    $targetObject = $_SESSION['captcha_target'] ?? null;
    $allObjects = $_SESSION['captcha_objects'] ?? [];
    
    // Debugging
    error_log('Click coordinates: X=' . $clickX . ', Y=' . $clickY);
    error_log('Target object: ' . print_r($targetObject, true));
    
    if ($clickX === null || $clickY === null) {
        header('Location: index.php?error=' . urlencode("Error: Click coordinates not received."));
        exit;
    }
    
    if (!$targetObject) {
        header('Location: index.php?error=' . urlencode("Error: Target object not found in session."));
        exit;
    }
    
    // Check if user clicked on the word (text area) rather than just the shape
    $textX = $targetObject['textX'];
    $textY = $targetObject['textY'];
    $textWidth = $targetObject['textWidth'];
    $textHeight = $targetObject['textHeight'];
    
    // Define a slightly larger area around the text for easier clicking
    $textClickArea = [
        'x' => $textX - 5,
        'y' => $textY - $textHeight - 5, // Text coordinates are at baseline, adjust upward
        'width' => $textWidth + 10,
        'height' => $textHeight + 10
    ];
    
    // Check if click is within the text area
    $clickedOnText = (
        $clickX >= $textClickArea['x'] && 
        $clickX <= ($textClickArea['x'] + $textClickArea['width']) && 
        $clickY >= $textClickArea['y'] && 
        $clickY <= ($textClickArea['y'] + $textClickArea['height'])
    );
    
    // Also check the shape bounds as a fallback
    $shapeBounds = $targetObject['bounds'];
    $clickedOnShape = (
        $clickX >= $shapeBounds['x'] && 
        $clickX <= ($shapeBounds['x'] + $shapeBounds['width']) && 
        $clickY >= $shapeBounds['y'] && 
        $clickY <= ($shapeBounds['y'] + $shapeBounds['height'])
    );
    
    // For circle shape, do additional radius check
    if ($targetObject['shape'] === 'circle' && $clickedOnShape) {
        $centerX = $shapeBounds['x'] + $shapeBounds['width'] / 2;
        $centerY = $shapeBounds['y'] + $shapeBounds['height'] / 2;
        $radius = $shapeBounds['width'] / 2;
        $distance = sqrt(pow($clickX - $centerX, 2) + pow($clickY - $centerY, 2));
        
        $clickedOnShape = ($distance <= $radius);
    }
    
    // If either the text or the surrounding shape was clicked, consider it successful
    if ($clickedOnText || $clickedOnShape) {
        header('Location: index.php?success=' . urlencode("CAPTCHA validation successful!"));
        exit;
    } else {
        header('Location: index.php?error=' . urlencode("CAPTCHA validation failed. You didn't click on the correct word."));
        exit;
    }
} else {
    header('Location: index.php?error=' . urlencode("Invalid request method."));
    exit;
}
?>