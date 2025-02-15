<!-- Fisierul prin intermediul caruia ne ocupam de administrarea evenimentelor (adaugare, editare, stergere) -->

<?php
// Conectare la baza de date
include 'connect.php';  // Asigură-te că calea este corectă

// Verifică dacă conexiunea a fost stabilită
if (!$conn) {
    die("Conexiune eșuată: " . mysqli_connect_error());
}

// Dacă se trimite cererea de ștergere
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event_id'])) {
    $eveniment_id = mysqli_real_escape_string($conn, $_POST['delete_event_id']);

    // Șterge clientul din baza de date
    $delete_query = "DELETE FROM eveniment WHERE EvenimentID = '$eveniment_id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Eveniment șters cu succes!'); window.location.href = 'manage_events.php';</script>";
        exit;
    } else {
        echo "<script>alert('Eroare la ștergerea evenimentului: " . mysqli_error($conn) . "');</script>";
    }
}

// Dacă se trimite formularul pentru adăugare eveniment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['eveniment_id'])) {
    $client_id = mysqli_real_escape_string($conn, $_POST['client_id']);
    $organizator_id = mysqli_real_escape_string($conn, $_POST['organizator_id']);
    $locatie_id = mysqli_real_escape_string($conn, $_POST['locatie_id']);
    $nume_eveniment = mysqli_real_escape_string($conn, $_POST['nume_eveniment']);
    $data_eveniment = mysqli_real_escape_string($conn, $_POST['data_eveniment']);
    $cost_eveniment = mysqli_real_escape_string($conn, $_POST['cost_eveniment']);

    $insert_query = "INSERT INTO eveniment (ClientID, OrganizatorID, LocatieID, NumeEveniment, DataEveniment, CostEveniment) VALUES ('$client_id', '$organizator_id', '$locatie_id', '$nume_eveniment', '$data_eveniment', '$cost_eveniment')";
    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Eveniment adăugat cu succes!'); window.location.href = 'manage_events.php';</script>";
        exit;
    } else {
        echo "<script>alert('Eroare la adăugarea evenimentului: " . mysqli_error($conn) . "');</script>";
    }
}

// Dacă se trimite formularul pentru adăugare eveniment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eveniment_id'])) {
    $eveniment_id = mysqli_real_escape_string($conn, $_POST['eveniment_id']);
    $client_id = mysqli_real_escape_string($conn, $_POST['client_id']);
    $organizator_id = mysqli_real_escape_string($conn, $_POST['organizator_id']);
    $locatie_id = mysqli_real_escape_string($conn, $_POST['locatie_id']);
    $nume_eveniment = mysqli_real_escape_string($conn, $_POST['nume_eveniment']);
    $data_eveniment = mysqli_real_escape_string($conn, $_POST['data_eveniment']);
    $cost_eveniment = mysqli_real_escape_string($conn, $_POST['cost_eveniment']);

    // Actualizează evenimentul în baza de date
    $update_query = "UPDATE eveniment SET ClientID = '$client_id', OrganizatorID = '$organizator_id', LocatieID = '$locatie_id', NumeEveniment = '$nume_eveniment', DataEveniment = '$data_eveniment', CostEveniment = '$cost_eveniment' WHERE EvenimentID = '$eveniment_id'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Eveniment actualizat cu succes!'); window.location.href = 'manage_events.php';</script>";
        exit;
    } else {
        echo "<script>alert('Eroare la actualizarea evenimentului: " . mysqli_error($conn) . "');</script>";
    }
}

// Preluare utilizatori din baza de date
$query = "SELECT * FROM eveniment";
$result = mysqli_query($conn, $query);


//3 join
//clienti care au platit pentru evenimente, impreuna cu suma facturii
$query_client_payments = "
    SELECT C.NumeClient, C.PrenumeClient, FE.TotalPlata, Fe.Status
    FROM client C
    INNER JOIN eveniment E ON C.ClientID = E.ClientID
    INNER JOIN facturaeveniment FE ON E.EvenimentID = FE.EvenimentID
    WHERE FE.status = 'Platit'
";

$result_client_payments = $conn->query($query_client_payments);

//4 subcereri
// Interogarea pentru evenimentele organizate de organizatori ce colaborează cu furnizori care 
//furnizează servicii cu un cost total mai mare decât media costului serviciilor furnizate
$query_supplier_materials_cost = "
    SELECT E.EvenimentID, E.NumeEveniment, E.DataEveniment, O.NumeOrganizator, F.NumeFurnizor, SUM(SF.CostServiciu) AS TotalCostMateriale
    FROM eveniment E
    JOIN organizator O ON E.OrganizatorID = O.OrganizatorID
    JOIN evenimentfurnizor EF ON E.EvenimentID = EF.EvenimentID
    JOIN furnizor F ON EF.FurnizorID = F.FurnizorID
    JOIN serviciifurnizor SF ON F.FurnizorID = SF.FurnizorID
    WHERE F.FurnizorID IN (
        SELECT F2.FurnizorID
        FROM furnizor F2
        JOIN serviciifurnizor SF2 ON F2.FurnizorID = SF2.FurnizorID
        GROUP BY F2.FurnizorID
        HAVING SUM(SF2.CostServiciu) > (
            SELECT AVG(TotalCost)
            FROM (
                SELECT SUM(SF3.CostServiciu) AS TotalCost
                FROM serviciifurnizor SF3
                GROUP BY SF3.FurnizorID
            ) AS Subquery
        )
    )
    GROUP BY E.EvenimentID, E.NumeEveniment, E.DataEveniment, O.NumeOrganizator, F.NumeFurnizor
";

$result_supplier_materials_cost = $conn->query($query_supplier_materials_cost);

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
    <title>Manage Events</title>
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
        <h1><i class="fas fa-events"></i> Manage Events</h1>
        <nav class="menu">
            <a href="adminhome.php">Dashboard</a>
            <a href="view_stats.php">Statistics</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="welcome">
            <h2>Manage Events</h2>
            <p>View, add, edit, or delete event informations.</p>
            <button id="openModal" class="btn-add">Add New Event</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Client ID</th>
                    <th>Organizer ID</th>
                    <th>Location ID</th>
                    <th>Event Name</th>
                    <th>Event Data</th>
                    <th>Event Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($eveniment = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $eveniment['EvenimentID']; ?></td>
                    <td><?php echo $eveniment['ClientID']; ?></td>
                    <td><?php echo $eveniment['OrganizatorID']; ?></td>
                    <td><?php echo $eveniment['LocatieID']; ?></td>
                    <td><?php echo $eveniment['NumeEveniment']; ?></td>
                    <td><?php echo $eveniment['DataEveniment']; ?></td>
                    <td><?php echo $eveniment['CostEveniment']; ?></td>
                    <td>
                        <button class="btn-action btn-edit" onclick="openEditModal(<?php echo $eveniment['EvenimentID']; ?>, '<?php echo $eveniment['ClientID']; ?>', '<?php echo $eveniment['OrganizatorID']; ?>', '<?php echo $eveniment['LocatieID']; ?>', '<?php echo $eveniment['NumeEveniment']; ?>', '<?php echo $eveniment['DataEveniment']; ?>', '<?php echo $eveniment['CostEveniment']; ?>')">Edit</button>
                        <!-- Formular pentru ștergere client -->
                        <form method="POST" action="manage_events.php" style="display:inline;" onsubmit="return confirmDelete()">
                            <input type="hidden" name="delete_event_id" value="<?php echo $eveniment['EvenimentID']; ?>">
                            <button type="submit" class="btn-action btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <section>
    <h2>Clients Who Paid for Events with Invoice Amount</h2>
    <?php if ($result_client_payments->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Client Last Name</th>
                    <th>Client First Name</th>
                    <th>Status</th>
                    <th>Invoice Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_client_payments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['NumeClient']); ?></td>
                        <td><?php echo htmlspecialchars($row['PrenumeClient']); ?></td>
                        <td><?php echo htmlspecialchars($row['Status']); ?></td>
                        <td><?php echo number_format($row['TotalPlata'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No clients found who paid for events.</p>
    <?php endif; ?>
</section>

<section>
            <h2>Events Organized by Organizers with Suppliers Providing Materials with Costs Greater than Average</h2>
            <?php if ($result_supplier_materials_cost->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Price (Lei)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_supplier_materials_cost->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['EvenimentID']); ?></td>
                                <td><?php echo htmlspecialchars($row['NumeEveniment']); ?></td>
                                <td><?php echo htmlspecialchars($row['DataEveniment']); ?></td>
                                <td><?php echo number_format($row['TotalCostMateriale'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No events found with organizers collaborating with suppliers providing materials with costs greater than the average.</p>
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
                <h3>Add New Event</h3>
                <input type="text" name="client_id" placeholder="Client ID" required>
                <input type="text" name="organizator_id" placeholder="Organizer ID" required>
                <input type="text" name="locatie_id" placeholder="Location ID" required>
                <input type="text" name="nume_eveniment" placeholder="Event Name" required>
                <input type="text" name="data_eveniment" placeholder="Event Data" required>
                <input type="text" name="cost_eveniment" placeholder="Event Price" required>
                <button type="submit">Add Event</button>
            </form>
        </div>
    </div>

    <!-- Modal pentru editare -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <form method="POST" action="manage_events.php">
                <h3>Edit Event</h3>
                <input type="hidden" name="eveniment_id" id="eveniment_id">
                <input type="text" name="client_id" id="client_id" placeholder="Client ID" required>
                <input type="text" name="organizator_id" id="organizator_id" placeholder="Organizer ID" required>
                <input type="text" name="locatie_id" id="locatie_id" placeholder="Location ID" required>
                <input type="text" name="nume_eveniment" id="nume_eveniment" placeholder="Event Name" required>
                <input type="text" name="data_eveniment" id="data_eveniment" placeholder="Event Data" required>
                <input type="text" name="cost_eveniment" id="cost_eveniment" placeholder="Event Price" required>
                <button type="submit">Update Event</button>
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

        function openEditModal(eveniment_id, client_id, organizator_id, locatie_id, nume_eveniment, data_eveniment, cost_eveniment) {
            document.getElementById('eveniment_id').value = eveniment_id;
            document.getElementById('client_id').value = client_id;
            document.getElementById('organizator_id').value = organizator_id;
            document.getElementById('locatie_id').value = locatie_id;
            document.getElementById('nume_eveniment').value = nume_eveniment;
            document.getElementById('data_eveniment').value = data_eveniment;
            document.getElementById('cost_eveniment').value = cost_eveniment;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete() {
            return confirm('Ești sigur că vrei să ștergi acest eveniment?');
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
