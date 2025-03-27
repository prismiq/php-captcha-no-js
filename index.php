<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>CAPTCHA Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .captcha-container {
            margin: 20px 0;
            text-align: center;
        }
        .debug-info {
            margin-top: 30px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        /* Prevent text selection and ensure consistent cursor */
        .captcha-image {
            user-select: none !important; /* Disable text selection */
            cursor: pointer !important;  /* Force pointer cursor */
        }
    </style>
</head>
<body>
    <h1>Word CAPTCHA Verification</h1>
    <p>Please click on the word as instructed in the image below:</p>
    
    <div class="captcha-container">
        <form action="validate.php" method="POST">
            <input type="image" src="captcha.php" alt="CAPTCHA Image" name="captcha_click" class="captcha-image">
        </form>
    </div>
    <?php if (isset($_GET['error'])): ?>
    <div style="color: red; margin-top: 10px;">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
    <div style="color: green; margin-top: 10px;">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
    <?php endif; ?>
</body>
</html>