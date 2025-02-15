<!-- Fisierul corespunzator inregistrarii -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <!-- Modal Register -->
<div id="register-modal" class="modal">
  <div class="modal-content">
    <span id="close-register-modal" class="close-btn">&times;</span>
    <h2>Register</h2>
    <form id="register-form" action="register.php"  method="POST">
      <label for="reg-username">Username:</label>
      <input type="text" id="reg-username" name="username" placeholder="username" required />
      
      <label for="reg-email">Email:</label>
      <input type="email" id="reg-email" name="email" placeholder="email" required />
      
      <label for="reg-password">Password:</label>
      <input type="password" id="reg-password" name="password" placeholder="password" required />
      
      <button type="submit" class="submit-btn">Register</button>
    </form>
    <p id="reg-error-message" class="error-message"></p>
    <p>Already have an account? <a href="index.php" id="open-login">Login</a></p>
  </div>
</div>
<script src="script.js"></script>
</body>
</html>