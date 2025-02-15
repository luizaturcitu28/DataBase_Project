<!-- Pagina utilizatorului -->

<?php
session_start(); // Începe sesiunea

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['username'])) {
    header("Location: /BD/index.php"); // Redirecționează la pagina de login dacă nu este autentificat
    exit();
}

$username = $_SESSION['username']; // Preia username-ul din sesiune
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home Page</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #2c3e50;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            font-size: 1.8rem;
            display: flex;
            align-items: center;
        }

        header h1 i {
            margin-right: 0.5rem;
            color: #1abc9c;
        }

        .menu {
            display: flex;
            gap: 1.5rem;
        }

        .menu a {
            text-decoration: none;
            color: white;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .menu a:hover {
            color: #1abc9c;
        }

        .hero {
            margin-top: 5rem;
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            background: #3498db;
            color: white;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: hero-gradient 10s infinite;
        }

        @keyframes hero-gradient {
            0%, 100% {
                background: linear-gradient(135deg, #3498db, #74ebd5);
            }
            50% {
                background: linear-gradient(135deg, #6c5ce7, #ff9ff3);
            }
        }

        .hero h2 {
            font-size: 2.5rem;
            z-index: 1;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 0;
        }

        main {
            margin: 2rem auto;
            max-width: 1200px;
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .welcome {
            grid-column: span 2;
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .welcome h2 {
            font-size: 2.5rem;
            color: #2c3e50;
        }

        .welcome p {
            font-size: 1rem;
            line-height: 1.6;
            color: #34495e;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .card i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #3498db;
        }

        .card h3 {
            font-size: 1.8rem;
            color: #34495e;
            margin-bottom: 0.5rem;
        }

        .card p {
            font-size: 1rem;
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .card a {
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .card a:hover {
            background-color: #2980b9;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto;
        }

        footer a {
            color: #1abc9c;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1><i class="fas fa-user"></i> User Dashboard</h1>
        <nav class="menu">
            <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
            <a href="request_events.php"><i class="fas fa-calendar-plus"></i> Request Events</a>
            <a href="settings.php"><i class="fas fa-cogs"></i> Settings</a>
            <a href="support.php"><i class="fas fa-life-ring"></i> Support</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>
    <section class="hero">
        <h2>Welcome to Your Dashboard, <?php echo htmlspecialchars($username); ?>!</h2>
    </section>
    <main>
        <div class="welcome">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>Explore your dashboard to access your profile, settings, and support options. Enjoy a personalized experience!</p>
        </div>
        <div class="card">
            <i class="fas fa-user-circle"></i>
            <h3>Profile</h3>
            <p>View and update your personal details and preferences.</p>
            <a href="profile.php">Go to Profile</a>
        </div>
        <div class="card">
            <i class="fas fa-calendar-plus"></i>
            <h3>Request Events</h3>
            <p>Here you can request a specific event!</p>
            <a href="request_events.php">Go to Request Events</a>
        </div>
        <div class="card">
            <i class="fas fa-cogs"></i>
            <h3>Settings</h3>
            <p>Customize your account settings and preferences.</p>
            <a href="settings.php">Go to Settings</a>
        </div>
        <div class="card">
            <i class="fas fa-life-ring"></i>
            <h3>Support</h3>
            <p>Need help? Access FAQs or contact our support team.</p>
            <a href="support.php">Get Support</a>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Events. All rights reserved. | <a href="privacy_policy.php">Privacy Policy</a></p>
    </footer>
</body>
</html>
