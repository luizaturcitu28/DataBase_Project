<!-- Fisierul prin intermediul caruia ne ocupam de gestionarea clientilor (adaugare, editare, stergere) -->

<?php
// Conectare la baza de date
include 'connect.php';  // Asigură-te că calea este corectă

// Verifică dacă conexiunea a fost stabilită
if (!$conn) {
    die("Conexiune eșuată: " . mysqli_connect_error());
}

// Dacă se trimite cererea de ștergere
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client_id'])) {
    $client_id = mysqli_real_escape_string($conn, $_POST['delete_client_id']);

    // Șterge clientul din baza de date
    $delete_query = "DELETE FROM client WHERE ClientID = '$client_id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Client șters cu succes!'); window.location.href = 'manage_users.php';</script>";
        exit;
    } else {
        echo "<script>alert('Eroare la ștergerea clientului: " . mysqli_error($conn) . "');</script>";
    }
}

// Dacă se trimite formularul pentru adăugare client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['client_id'])) {
    $nume = mysqli_real_escape_string($conn, $_POST['nume']);
    $prenume = mysqli_real_escape_string($conn, $_POST['prenume']);
    $telefon = mysqli_real_escape_string($conn, $_POST['telefon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);

    $insert_query = "INSERT INTO client (NumeClient, PrenumeClient, NrTelefonClient, EmailClient, AdresaClient) VALUES ('$nume', '$prenume', '$telefon', '$email', '$adresa')";
    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Client adăugat cu succes!'); window.location.href = 'manage_users.php';</script>";
        exit;
    } else {
        echo "<script>alert('Eroare la adăugarea clientului: " . mysqli_error($conn) . "');</script>";
    }
}

// Dacă se trimite formularul pentru adăugare client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_id'])) {
    $client_id = mysqli_real_escape_string($conn, $_POST['client_id']);
    $nume = mysqli_real_escape_string($conn, $_POST['nume']);
    $prenume = mysqli_real_escape_string($conn, $_POST['prenume']);
    $telefon = mysqli_real_escape_string($conn, $_POST['telefon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);

    // Actualizează clientul în baza de date
    $update_query = "UPDATE client SET NumeClient = '$nume', PrenumeClient = '$prenume', NrTelefonClient = '$telefon', EmailClient = '$email', AdresaClient = '$adresa' WHERE ClientID = '$client_id'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Client actualizat cu succes!'); window.location.href = 'manage_users.php';</script>";
        exit;
    } else {
        echo "<script>alert('Eroare la actualizarea clientului: " . mysqli_error($conn) . "');</script>";
    }
}

// Preluare utilizatori din baza de date
$query = "SELECT * FROM client";
$result = mysqli_query($conn, $query);

//1 join
//evenimentele organizate, alaturi de clientii corespunzatori, impreuna cu furnizorii si cu facturile asociate evenimentului
$query_client_supplier_invoices = "
    SELECT 
        E.EvenimentID, 
        E.NumeEveniment, 
        C.NumeClient, 
        C.PrenumeClient, 
        GROUP_CONCAT(DISTINCT F.NumeFurnizor SEPARATOR ', ') AS Furnizori,
        SUM(FE.TotalPlata) AS TotalPlata
    FROM eveniment E
    LEFT JOIN client C ON E.ClientID = C.ClientID
    LEFT JOIN evenimentfurnizor EF ON E.EvenimentID = EF.EvenimentID
    LEFT JOIN furnizor F ON EF.FurnizorID = F.FurnizorID
    LEFT JOIN facturaeveniment FE ON E.EvenimentID = FE.EvenimentID
    GROUP BY E.EvenimentID, E.NumeEveniment, C.NumeClient, C.PrenumeClient
";

$result_client_supplier_invoices = $conn->query($query_client_supplier_invoices);


// Verifică dacă interogarea a avut succes
if (!$result) {
    die("Eroare la interogare: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        .menu a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
        }
        .menu a:hover {
            text-decoration: underline;
        }
        main {
            padding: 20px;
        }
        .welcome h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        table th {
            background-color: #333;
            color: white;
        }
        table td {
            background-color: #f9f9f9;
        }
        table td a {
            color: #007BFF;
            text-decoration: none;
        }
        table td a:hover {
            text-decoration: underline;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #333;
            color: white;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 50%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .modal-content h3 {
            margin-top: 0;
        }
        .modal-content input[type="text"],
        .modal-content input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .modal-content button {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .modal-content button:hover {
            background-color: #0056b3;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Button styles */
        .btn-add {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-add:hover {
            background-color: #218838;
        }
        .btn-action {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
        }
        .btn-edit {
            background-color: #ffc107;
            color: white;
        }
        .btn-edit:hover {
            background-color: #e0a800;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <header>
        <h1><i class="fas fa-users"></i> Manage Clients</h1>
        <nav class="menu">
            <a href="adminhome.php">Dashboard</a>
            <a href="view_stats.php">Statistics</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="welcome">
            <h2>Manage Clients</h2>
            <p>View, add, edit, or delete client informations.</p>
            <button id="openModal" class="btn-add">Add New Client</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($client = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $client['ClientID']; ?></td>
                    <td><?php echo $client['NumeClient']; ?></td>
                    <td><?php echo $client['PrenumeClient']; ?></td>
                    <td><?php echo $client['NrTelefonClient']; ?></td>
                    <td><?php echo $client['EmailClient']; ?></td>
                    <td><?php echo $client['AdresaClient']; ?></td>
                    <td>
                        <button class="btn-action btn-edit" onclick="openEditModal(<?php echo $client['ClientID']; ?>, '<?php echo $client['NumeClient']; ?>', '<?php echo $client['PrenumeClient']; ?>', '<?php echo $client['NrTelefonClient']; ?>', '<?php echo $client['EmailClient']; ?>', '<?php echo $client['AdresaClient']; ?>')">Edit</button>
                        <!-- Formular pentru ștergere client -->
                        <form method="POST" action="manage_users.php" style="display:inline;" onsubmit="return confirmDelete()">
                            <input type="hidden" name="delete_client_id" value="<?php echo $client['ClientID']; ?>">
                            <button type="submit" class="btn-action btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <section>
    <h2>Events Organized by Client with Suppliers and Associated Invoices</h2>
    <?php if ($result_client_supplier_invoices->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Client Last Name</th>
                    <th>Client First Name</th>
                    <th>Supplier Name</th>
                    <th>Invoice Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_client_supplier_invoices->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['EvenimentID']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeEveniment']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeClient']); ?></td>
                        <td><?php echo htmlspecialchars($row['PrenumeClient']); ?></td>
                        <td><?php echo htmlspecialchars($row['Furnizori']); ?></td>
                        <td><?php echo number_format($row['TotalPlata'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No events found for this client with suppliers and invoices.</p>
    <?php endif; ?>
</section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Events. All rights reserved. | <a href="privacy_policy.php">Privacy Policy</a></p>
    </footer>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form method="POST" action="">
                <h3>Add New Client</h3>
                <input type="text" name="nume" placeholder="Last Name" required>
                <input type="text" name="prenume" placeholder="First Name" required>
                <input type="text" name="telefon" placeholder="Phone Number" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="adresa" placeholder="Address" required>
                <button type="submit">Add Client</button>
            </form>
        </div>
    </div>

    <!-- Modal pentru editare -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <form method="POST" action="manage_users.php">
                <h3>Edit Client</h3>
                <input type="hidden" name="client_id" id="client_id">
                <input type="text" name="nume" id="nume" placeholder="Last Name" required>
                <input type="text" name="prenume" id="prenume" placeholder="First Name" required>
                <input type="text" name="telefon" id="telefon" placeholder="Phone Number" required>
                <input type="email" name="email" id="email" placeholder="Email" required>
                <input type="text" name="adresa" id="adresa" placeholder="Address" required>
                <button type="submit">Update Client</button>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('modal');
        const openModalBtn = document.getElementById('openModal');
        const closeModalBtn = document.querySelector('.close');

        openModalBtn.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        function openEditModal(client_id, nume, prenume, telefon, email, adresa) {
            document.getElementById('client_id').value = client_id;
            document.getElementById('nume').value = nume;
            document.getElementById('prenume').value = prenume;
            document.getElementById('telefon').value = telefon;
            document.getElementById('email').value = email;
            document.getElementById('adresa').value = adresa;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete() {
            return confirm('Ești sigur că vrei să ștergi acest client?');
        }

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
