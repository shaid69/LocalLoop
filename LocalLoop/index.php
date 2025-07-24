<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to LocalLoop</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/main.js"></script>
  <style>
    body {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(135deg, #232526 0%, #414345 100%);
    }
    .welcome-box {
      background: linear-gradient(120deg, #232526 60%, #414345 100%);
      padding: 40px 32px;
      border-radius: 18px;
      box-shadow: 0 8px 24px rgba(44,62,80,0.20);
      text-align: center;
      color: #e0e0e0;
      max-width: 520px;
      width: 100%;
      margin-bottom: 32px;
    }
    .welcome-box h1 {
      color: #00c6ff;
      margin-bottom: 16px;
      animation: fadeInUp 1s;
      font-size: 2.2rem;
      font-weight: 700;
      letter-spacing: 2px;
    }
    .welcome-box p {
      color: #e0e0e0;
      margin-bottom: 24px;
      font-size: 1.2rem;
      animation: fadeInUp 1.2s;
    }
    .button-link {
      display: inline-block;
      padding: 14px 28px;
      background: linear-gradient(90deg, #00c6ff 0%, #0072ff 100%);
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1.1rem;
      box-shadow: 0 2px 8px rgba(0,198,255,0.15);
      transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
      margin: 6px 4px;
    }
    .button-link:hover {
      background: linear-gradient(90deg, #ffb347 0%, #232526 100%);
      box-shadow: 0 4px 16px rgba(0,198,255,0.25);
      transform: scale(1.05);
    }
    .loader {
      border: 8px solid #414345;
      border-radius: 50%;
      border-top: 8px solid #00c6ff;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translate3d(0, 30px, 0);
      }
      to {
        opacity: 1;
        transform: none;
      }
    }
    .features {
      background: linear-gradient(120deg, #232526 60%, #414345 100%);
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(44,62,80,0.15);
      padding: 24px;
      max-width: 520px;
      width: 100%;
      margin: 0 auto 32px auto;
      color: #e0e0e0;
      text-align: left;
      animation: fadeInUp 1.5s;
    }
    .features h2 {
      color: #00c6ff;
      font-size: 1.3rem;
      margin-bottom: 12px;
    }
    .features ul {
      list-style: none;
      padding: 0;
      margin: 0 0 12px 0;
    }
    .features ul li {
      margin-bottom: 10px;
      padding-left: 18px;
      position: relative;
    }
    .features ul li:before {
      content: "★";
      color: #ffb347;
      position: absolute;
      left: 0;
      font-size: 1.1em;
    }
    .contact-info {
      color: #e0e0e0;
      font-size: 0.98rem;
      margin-top: 18px;
      text-align: center;
      opacity: 0.8;
    }
  </style>
</head>
<body>
  <div class="welcome-box">
    <h1>Welcome to LocalLoop</h1>
    <p>Discover and book local services with ease.<br>Join our vibrant community!</p>
    <a class="button-link" href="pages/home.php">Enter Site</a>
    <a class="button-link" href="pages/login.php">Login</a>
    <a class="button-link" href="pages/register.php">Register</a>
    <button class="button-link" type="button" onclick="window.history.back();" style="background:linear-gradient(90deg,#ffb347 0%,#232526 100%);color:#232526;">Back</button>
    <div class="loader" style="display:none;"></div>
  </div>
  <div class="features">
    <h2>Why LocalLoop?</h2>
    <ul>
      <li>Find trusted local services quickly</li>
      <li>Book appointments with instant confirmation</li>
      <li>Read and write reviews for every service</li>
      <li>Easy registration for buyers and sellers</li>
      <li>Modern, secure, and mobile-friendly design</li>
    </ul>
    <div class="contact-info">
      <strong>Contact us:</strong> support@localloop.com<br>
      <strong>Follow us:</strong> <a href="#" style="color:#00c6ff;text-decoration:underline;">Instagram</a> | <a href="#" style="color:#00c6ff;text-decoration:underline;">Twitter</a>
    </div>
  </div>
</body>
</html>