<!-- Pagina de suport, prin care utilizatorul poate completa un formular in care sa detalieze problema intampinata, problema ce va ajunge in baza de date -->

<?php
// Încarcam fisierul de conectare la baza de date
include 'connect.php';

// Verificam daca formularul de contact a fost trimis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Salvează mesajul în baza de date
    $contact_query = $conn->prepare("INSERT INTO support_messages (name, email, message) VALUES (?, ?, ?)");
    $contact_query->execute([$name, $email, $message]);

    $confirmation_message = "Your message has been sent successfully! We will get back to you shortly.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Stilizare generala */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
        }

        header {
            background-color: #3b5998;
            color: white;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 1.5em;
            letter-spacing: 1px;
        }

        header a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        header a:hover {
            text-decoration: underline;
        }

        main {
            padding: 50px 20px;
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        main:hover {
            transform: translateY(-5px);
        }

        h1, h2, h3 {
            color: #3b5998;
            font-weight: 600;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 1.1em;
            border: 1px solid #c3e6cb;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
            margin-top: 30px;
        }

        form label {
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        form input, form textarea {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        form input:focus, form textarea:focus {
            border-color: #3b5998;
            outline: none;
            box-shadow: 0 0 8px rgba(59, 89, 152, 0.2);
        }

        form button {
            padding: 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        form button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        #faq ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }

        #faq li {
            background-color: #f9f9f9;
            margin: 20px 0;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        #faq li:hover {
            transform: translateY(-5px);
        }

        #faq li strong {
            color: #3b5998;
            font-size: 1.1em;
        }

        #faq li p {
            margin-top: 5px;
            font-size: 1em;
        }

        a {
            color: #3b5998;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Support</h1>
        <a href="userhome.php">Back to Home</a>
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <h2>Need Help?</h2>
        <p>If you have any questions or need assistance, feel free to reach out to us. You can access our <a href="#faq">FAQs</a> or contact our support team below.</p>

        <!-- Sectiunea FAQ -->
        <section id="faq">
            <h3>Frequently Asked Questions (FAQs)</h3>
            <ul>
                <li>
                    <strong>How can I update my personal details?</strong>
                    <p>To update your personal information, go to the 'Settings' page after logging in and modify the desired details.</p>
                </li>
                <li>
                    <strong>How can I change my password?</strong>
                    <p>If you wish to change your password, please contact our support team using the form below or by email.</p>
                </li>
                <li>
                    <strong>How can I contact the support team?</strong>
                    <p>You can contact our support team by filling out the form below. We will get back to you as soon as possible.</p>
                </li>
                <li>
                    <strong>What should I do if I encounter an error on the site?</strong>
                    <p>If you encounter an error, please contact our support team through the form, providing details about the error so we can assist you faster.</p>
                </li>
                <li>
                    <strong>How can I delete my account?</strong>
                    <p>If you wish to delete your account, please contact our support team by email or through the form below. We will process your request as soon as possible.</p>
                </li>
                <li>
                    <strong>What should I do if I forgot my account details?</strong>
                    <p>If you have forgotten your account details (such as username or password), please contact our support team, and we will assist you in recovering your access.</p>
                </li>
            </ul>
        </section>

        <!-- Formular de contact -->
        <section>
            <h3>Contact Our Support Team</h3>
            <?php if (isset($confirmation_message)): ?>
                <p class="success-message"><?php echo $confirmation_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="support.php">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="message">Your Message:</label>
                <textarea id="message" name="message" rows="4" required></textarea>

                <button type="submit">Send Message</button>
            </form>
        </section>
    </main>
</body>
</html>
