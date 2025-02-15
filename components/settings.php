<!-- Pagina de setari -->

<?php
session_start(); // Asigură-te că sesiunea este începută

// Verifică dacă sesiunea este activă și dacă utilizatorul este autentificat
if (!isset($_SESSION['username'])) {
    echo "Utilizatorul nu este autentificat!";
    exit(); // Dacă nu este autentificat, oprește execuția
}

include 'connect.php'; // Include fișierul de conectare la baza de date

$username = $_SESSION['username']; // Obține username-ul din sesiune

// Conectare la baza de date
$servername = "localhost";
$username_db = "root"; // Folosește utilizatorul corect pentru XAMPP
$password_db = ""; // Parola implicită pentru MySQL pe XAMPP
$dbname = "events_db";

// Crează conexiunea
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verifică conexiunea
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obține datele utilizatorului din baza de date
$query = $conn->prepare("SELECT * FROM login WHERE username = ?");
$query->bind_param("s", $username); // Legăm parametrul
$query->execute();
$result = $query->get_result();
$user_data = $result->fetch_assoc(); // Obținem datele utilizatorului

// Dacă utilizatorul nu există în baza de date
if (!$user_data) {
    echo "Utilizatorul nu a fost găsit!";
    exit();
}

$usertype = $user_data['usertype']; // Tipul utilizatorului (admin sau user)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizează numele de utilizator și parola
    $new_username = $_POST['username'] ?? $user_data['username'];
    $new_password = $_POST['password'] ?? /*password_hash($_POST['password'], PASSWORD_DEFAULT)*/ $user_data['password'];

    $update_query = $conn->prepare("UPDATE login SET username = ?, password = ? WHERE username = ?");
    $update_query->bind_param("sss", $new_username, $new_password, $username); // Legăm parametrii
    $update_query->execute();

    $message = "Settings have been successfully updated!";

    // Dacă este administrator, permite actualizarea titlului site-ului
    if ($usertype === 'admin' && isset($_POST['site_title'])) {
        $site_title = $_POST['site_title'];
        $admin_update_query = $conn->prepare("UPDATE site_settings SET title = ?");
        $admin_update_query->bind_param("s", $site_title); // Legăm parametrul
        $admin_update_query->execute();
        $message .= " Site title updated!";
    }

    // Actualizează sesiunea dacă numele de utilizator s-a schimbat
    $_SESSION['username'] = $new_username;
}

// Dacă utilizatorul este administrator, adaugă setările suplimentare pentru admin
if ($usertype === 'admin') {

    // Procesarea formularului pentru schimbarea username-ului și parolei
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Preia datele din formular
        $new_username = $_POST['username'];
        $new_password = $_POST['password'];
        $confirm_password = $_POST['password'];

        // Validare
        if (empty($new_username) || empty($new_password) || empty($confirm_password)) {
            echo 'All fields are required.';
        } elseif ($new_password !== $confirm_password) {
            echo 'Passwords do not match.';
        } else {
            // Criptează parola nouă
            //$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Actualizează username-ul și parola în baza de date
            $update_query = $conn->prepare("UPDATE login SET username = ?, password = ? WHERE usertype = 'admin'");
            $update_query->bind_param("ss", $new_username, $new_password); // Legătura parametrilor (string, string)
            
            if ($update_query->execute()) {
                // Actualizează sesiunea cu noile valori
                $_SESSION['username'] = $new_username;
                //$_SESSION['password'] = $hashed_password;
                $_SESSION['password'] = $new_password;

                echo 'Settings updated successfully!';
            } else {
                echo 'Error updating settings: ' . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <style>
        /* Resetare stiluri implicite */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 1.2em;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        header a:hover {
            color: #f1f1f1;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: bold;
        }

        main {
            max-width: 900px;
            margin: 30px auto;
            padding: 40px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        main:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 1.1em;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1.1em;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .success-message {
            color: #4CAF50;
            font-weight: bold;
            margin-top: 20px;
            font-size: 1.2em;
        }

        section {
            margin-top: 30px;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        section h3 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
        }

        section p {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 20px;
        }

        .admin-settings-form {
            margin-top: 20px;
        }

        .admin-settings-form label {
            margin-bottom: 10px;
        }

        .admin-settings-form input {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Settings</h1>
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($usertype === 'admin' ? 'Administrator' : 'User'); ?>!</h2>

        <!-- Formularul pentru schimbarea datelor utilizatorului -->
        <form method="POST" class="settings-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>

            <label for="password">Password (leave blank to keep the current password):</label>
            <input type="password" id="password" name="password">

            <button type="submit">Save Changes</button>
        </form>

        <?php if (!empty($message)): ?>
            <p class="success-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <p>User Type: <?php echo htmlspecialchars($usertype); ?></p>

        <!-- Dacă este administrator, afișează opțiuni suplimentare pentru admin -->
        <?php if ($usertype === 'admin'): ?>
            <section>
    <h3>Change Username and Password</h3>
    <p>You can change your username and password here:</p>

    <!-- Formular pentru schimbarea username-ului și parolei -->
    <form method="POST" action="">
        <!-- Câmp pentru username -->
        <label for="new_username">New Username:</label>
        <input type="text" name="new_username" id="new_username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" required><br>

        <!-- Câmp pentru parola nouă -->
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required><br>

        <!-- Câmp pentru confirmarea parolei -->
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required><br>

        <!-- Buton pentru salvarea setărilor -->
        <button type="submit">Save Settings</button>
    </form>
</section>

        <?php endif; ?>
    </main>
</body>
</html>
