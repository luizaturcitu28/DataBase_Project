<!-- Profilul clientilor care au cont de utilizator -->

<?php
// Start session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['username'])) {
    echo "You are not logged in. Please log in.";
    exit();
}

// Connect to the database
include 'connect.php';

// Get the username from the session
$username = $_SESSION['username'];

// Query to get the client's information
$sql = "SELECT c.* FROM client c 
        JOIN login l ON c.ClientID = l.ClientID 
        WHERE l.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $client = $result->fetch_assoc();
} else {
    echo "Client not found.";
    exit();
}

// Handle form submissions for password and username change
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['change_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {

            // Update the password in the database
            $update_password_sql = "UPDATE login SET password = ? WHERE username = ?";
            $stmt = $conn->prepare($update_password_sql);
            $stmt->bind_param("ss", $new_password, $username);
            $stmt->execute();

            $success_message = "Password updated successfully!";
        } else {
            $error_message = "Passwords do not match. Please try again.";
        }
    }

    if (isset($_POST['change_username'])) {
        $new_username = $_POST['new_username'];

        // Check if the new username already exists
        $check_username_sql = "SELECT * FROM login WHERE username = ?";
        $stmt = $conn->prepare($check_username_sql);
        $stmt->bind_param("s", $new_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Update the username in the database
            $update_username_sql = "UPDATE login SET username = ? WHERE username = ?";
            $stmt = $conn->prepare($update_username_sql);
            $stmt->bind_param("ss", $new_username, $username);
            $stmt->execute();

            // Update session username
            $_SESSION['username'] = $new_username;

            $success_message = "Username updated successfully!";
        } else {
            $error_message = "Username already taken. Please choose another one.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f7c6ff, #b0e0e6);
            color: #fff;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
            overflow-y: auto; /* Make the entire page scrollable */
        }

        .container {
            max-width: 900px;
            width: 100%;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            color: #333;
            margin-top: 40px; /* Add space between the top of the page and the profile info */
        }

        h1 {
            text-align: center;
            font-size: 3rem;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .profile-info {
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-info h2 {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            font-weight: 600;
        }

        .profile-info p {
            font-size: 1.3rem;
            color: #555;
            margin: 15px 0;
            transition: all 0.3s ease;
            padding-left: 20px;
        }

        .profile-info p:hover {
            color: #3498db;
            transform: translateX(5px);
            padding-left: 25px;
        }

        .profile-info strong {
            color: #2c3e50;
            font-weight: bold;
        }

        .btn-back {
            display: inline-block;
            text-decoration: none;
            background-color: #3498db;
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            font-size: 1.2rem;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 30px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-back:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        footer {
            text-align: center;
            margin-top: 60px;
            font-size: 1rem;
            color: #7f8c8d;
            width: 100%;
            position: relative;
            bottom: 0;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1);
        }

        footer a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #3498db;
        }

        .form-group label {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        .form-group button {
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: #2980b9;
        }

        /* Stiluri pentru modale */
        /* Modal Background */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.3); /* Fundal mai subtil */
    overflow: auto;
    padding-top: 50px;
}

/* Modal Content */
.modal-content {
    background: #ffffff; /* Fundal alb pentru contrast */
    margin: 5% auto;
    padding: 40px;
    border-radius: 12px;
    width: 450px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1); /* Umbra mai subtilă */
    position: relative;
    animation: fadeIn 0.5s ease-out;
}

/* Close Button (X) */
.close {
    color: #333;
    font-size: 30px;
    font-weight: bold;
    position: absolute;
    top: 15px;
    right: 15px;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: #e74c3c; /* Culoare roșie la hover */
}

/* Form Fields */
.form-group {
    margin-bottom: 20px;
}

.form-group input {
    width: 100%;
    padding: 15px;
    font-size: 1.1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f4f4f9;
    transition: all 0.3s ease;
    box-sizing: border-box; /* Se asigură că padding-ul nu afectează lățimea */
    color: #333;
}

.form-group input:focus {
    border-color: #3498db;
    background-color: #fff;
    box-shadow: 0 0 8px rgba(52, 152, 219, 0.6);
    outline: none; /* Elimină conturul implicit */
}

/* Labels */
.form-group label {
    font-size: 1.1rem;
    color: #555;
    margin-bottom: 8px;
    display: block;
    font-weight: 600;
}

/* Submit Button */
.form-group button {
    background-color: #3498db;
    color: #fff;
    padding: 15px 25px;
    border: none;
    border-radius: 30px;
    font-size: 1.1rem;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease, transform 0.2s ease;
    margin-top: 10px;
}

.form-group button:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

.form-group button:active {
    background-color: #2980b9;
    transform: translateY(1px);
}

/* Modal fade-in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* For the background of the modal */
.modal-content {
    background-color: #f9f9f9; /* Ușor gri pentru un contrast mai plăcut */
    border-radius: 12px;
    padding: 30px;
    width: 400px;
}

/* Shadow for input fields */
.form-group input:focus {
    box-shadow: 0 0 8px rgba(52, 152, 219, 0.4);
}

/* Add a subtle border to the modal */
.modal-content {
    border: 1px solid #ddd;
}

        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }

            h1 {
                font-size: 2.5rem;
            }

            .profile-info h2 {
                font-size: 1.8rem;
            }

            .profile-info p {
                font-size: 1.1rem;
            }

            .btn-back {
                font-size: 1rem;
                padding: 12px 30px;
            }

            .modal-content {
                width: 80%;
            }
        }

        /* Success and error messages */
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 1.2rem;
            text-align: center;
        }

        .success {
            background-color: #28a745;
            color: white;
        }

        .error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>My Profile</h1>

        <!-- Success or error message -->
        <?php if ($success_message): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Client details -->
        <div class="profile-info">
            <h2>Your Information</h2>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($client['NumeClient']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($client['PrenumeClient']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($client['EmailClient']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($client['NrTelefonClient']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($client['AdresaClient']); ?></p>
        </div>

        <!-- Buttons for changing username and password -->
        <div class="form-group">
            <button id="openModalBtn">Change Username</button>
        </div>
        <div class="form-group">
            <button id="openModalBtn2">Change Password</button>
        </div>

        <!-- Modal for changing username -->
        <div id="changeUsernameModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Change Username</h2>
        <form method="POST">
            <div class="form-group">
                <label for="new_username">New Username</label>
                <input type="text" id="new_username" name="new_username" required>
            </div>
            <div class="form-group">
                <button type="submit" name="change_username">Change Username</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pentru schimbarea parolei -->
<div id="changePasswordModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Change Password</h2>
        <form method="POST">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="change_password">Change Password</button>
            </div>
        </form>
    </div>
</div>

        <a href="userhome.php" class="btn-back">Back to Home</a>
    </div>

    <footer>
        <p>&copy; 2024 My Profile. All rights reserved.</p>
    </footer>

    <script>
        // Get the modals
        var modalUsername = document.getElementById("changeUsernameModal");
        var modalPassword = document.getElementById("changePasswordModal");

        // Get the buttons that open the modals
        var btnUsername = document.getElementById("openModalBtn");
        var btnPassword = document.getElementById("openModalBtn2");

        // Get the <span> elements that close the modals
        var span = document.getElementsByClassName("close");

        // When the user clicks on the "Change Username" button, open the username modal
        btnUsername.onclick = function() {
            modalUsername.style.display = "block";
            modalPassword.style.display = "none"; // Close password modal if open
        }

        // When the user clicks on the "Change Password" button, open the password modal
        btnPassword.onclick = function() {
            modalPassword.style.display = "block";
            modalUsername.style.display = "none"; // Close username modal if open
        }

        // When the user clicks on <span> (x), close both modals
        for (var i = 0; i < span.length; i++) {
            span[i].onclick = function() {
                modalUsername.style.display = "none";
                modalPassword.style.display = "none";
            }
        }

        // When the user clicks anywhere outside of the modal, close both modals
        window.onclick = function(event) {
            if (event.target == modalUsername || event.target == modalPassword) {
                modalUsername.style.display = "none";
                modalPassword.style.display = "none";
            }
        }
    </script>
</body>
</html>
