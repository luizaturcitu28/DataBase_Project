<!-- Fisierul corespunzator loginului, prin intermediul caruia vom sti drept cine ne logam -->

<?php
session_start(); // Începe sesiunea

$servename = 'localhost';
$db_name = 'events_db';
$user = 'root';
$password = '';

$data = mysqli_connect($servename, $user, $password, $db_name);
if (!$data) {
    die("MySQLi connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $data->prepare("SELECT * FROM login WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if ($row) {
        if ($row["usertype"] == "user") {
            // Salvează username-ul în sesiune
            $_SESSION['username'] = $username;
            $_SESSION['ClientID'] = $row['ClientID']; // Salvează ClientID în sesiune

            // Redirecționare către pagina principală a utilizatorului
            header("Location: /BD/components/userhome.php");
            exit();
        } elseif ($row["usertype"] == "admin") {
            $_SESSION['username'] = $username;
            header("Location: /BD/components/adminhome.php");
            exit();
        } else {
            display_error("Invalid user type!");
        }
    } else {
        display_error("Username or password is incorrect!");
    }
}

function display_error($message) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Error</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #f857a6, #ff5858);
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .error-container {
            text-align: center;
            background: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
        }
        .error-container h1 {
            color: #e74c3c;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .error-container p {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }
        .error-container a {
            text-decoration: none;
            background: #3498db;
            color: white;
            padding: 0.8rem 1.2rem;
            border-radius: 5px;
            font-size: 1rem;
            transition: background 0.3s ease;
        }
        .error-container a:hover {
            background: #2980b9;
        }
        .error-container .icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">⚠️</div>
        <h1>Login Failed</h1>
        <p>$message</p>
        <a href="/BD/index.php">Back to Login</a>
    </div>
</body>
</html>
HTML;
    exit;
}
?>
