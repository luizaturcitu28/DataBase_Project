<!-- Pagina de statistici, in care putem vedea toate statisticile din baza de date -->

<?php
// Conectarea la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "events_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifică ce formular a fost trimis
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'capacitate_form') {
        // Preia valoarea capacității minime introdusă de utilizator
        //join cu parametri variabili, afiseaza evenimentele care au loc in locatii cu o capacitate mai mare decat cea introdusa de la tastatura
        $capacitate_minima = $_POST['capacitate_minima'];

        // Verifică dacă valoarea introdusă este un număr valid
        if (is_numeric($capacitate_minima) && $capacitate_minima > 0) {
            $query_evenimente_locatii = "
                SELECT E.EvenimentID, E.NumeEveniment, L.NumeLocatie, L.CapacitateLocatie, E.DataEveniment
                FROM eveniment E
                INNER JOIN locatie L ON E.LocatieID = L.LocatieID
                WHERE L.CapacitateLocatie > ?
            ";

            $stmt = $conn->prepare($query_evenimente_locatii);
            $stmt->bind_param("i", $capacitate_minima);
            $stmt->execute();
            $result_evenimente_locatii = $stmt->get_result();
        } else {
            $error_message = "Please enter a valid capacity.";
        }
    }


    //subcerere cu parametri variabili pentru a afisa organizatorii evenimentelor care au loc inainte de o data specificata
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'data_form') {
        // Preia data introdusă de utilizator
        $data_maxima = $_POST['data_maxima'];

        // Verifică dacă data introdusă este validă
        if (DateTime::createFromFormat('Y-m-d', $data_maxima)) {
            $query_organizatori_evenimente = "
                SELECT O.NumeOrganizator, O.PrenumeOrganizator, Sub.DataEveniment
                FROM organizator O
                INNER JOIN (
                    SELECT OrganizatorID, DataEveniment
                    FROM eveniment
                    WHERE DataEveniment < ?
                ) AS Sub ON O.OrganizatorID = Sub.OrganizatorID
                ORDER BY Sub.DataEveniment ASC
            ";

            $stmt = $conn->prepare($query_organizatori_evenimente);
            $stmt->bind_param("s", $data_maxima);
            $stmt->execute();
            $result_organizatori_evenimente = $stmt->get_result();
        } else {
            $error_message = "Please enter a valid date.";
        }
    }
}


//2 join
//evenimentele care au avut loc intr-o locatie specifica, impreuna cu organizatorii
$query_location_organizer_events = "
    SELECT E.EvenimentID, E.NumeEveniment, L.NumeLocatie, O.NumeOrganizator, O.PrenumeOrganizator
    FROM eveniment E
    INNER JOIN locatie L ON E.LocatieID = L.LocatieID
    INNER JOIN organizator O ON E.OrganizatorID = O.OrganizatorID
";


$result_location_organizer_events = $conn->query($query_location_organizer_events);

//4 join
//evenimente care au avut furnizori, inclusiv serviciile oferite de acestia
$query_event_supplier_services = "
    SELECT E.EvenimentID, E.NumeEveniment, F.NumeFurnizor, SF.NumeServiciu
    FROM eveniment E
    INNER JOIN evenimentfurnizor EF ON E.EvenimentID = EF.EvenimentID
    INNER JOIN furnizor F ON EF.FurnizorID = F.FurnizorID
    INNER JOIN serviciifurnizor SF ON F.FurnizorID = SF.FurnizorID
";

$result_event_supplier_services = $conn->query($query_event_supplier_services);

//5 join
//evenimente care au avut loc intr-o locatie cu o capacitate mare, impreuna cu facturile lor
$query_large_capacity_events_invoices = "
    SELECT E.EvenimentID, E.NumeEveniment, L.NumeLocatie, L.CapacitateLocatie, FE.TotalPlata
    FROM eveniment E
    INNER JOIN locatie L ON E.LocatieID = L.LocatieID
    INNER JOIN facturaeveniment FE ON E.EvenimentID = FE.EvenimentID
    WHERE L.CapacitateLocatie > 3000
";

$result_large_capacity_events_invoices = $conn->query($query_large_capacity_events_invoices);

//6 join
//evenimente organizate de un anumit organizator, impreuna cu furnizorii lor
$query_organizer_supplier_events = "
    SELECT E.EvenimentID, E.NumeEveniment, O.NumeOrganizator, O.PrenumeOrganizator, F.NumeFurnizor
    FROM eveniment E
    INNER JOIN organizator O ON E.OrganizatorID = O.OrganizatorID
    INNER JOIN evenimentfurnizor EF ON E.EvenimentID = EF.EvenimentID
    INNER JOIN furnizor F ON EF.FurnizorID = F.FurnizorID
";

$result_organizer_supplier_events = $conn->query($query_organizer_supplier_events);

//1 subcereri
// Interogarea pentru evenimente cu prețul mai mare decât media
$query_events = "
    SELECT E.EvenimentID, E.NumeEveniment, E.DataEveniment, E.CostEveniment
    FROM eveniment E
    WHERE E.CostEveniment > (
        SELECT AVG(CostEveniment) 
        FROM eveniment
    )
";

$result_events = $conn->query($query_events);

//2 subcereri
// Interogarea pentru locații cu capacitate mai mare decât media locațiilor care găzduiesc evenimente în ultimele 6 luni
$query_locations = "
    SELECT L.LocatieID, L.NumeLocatie, L.AdresaLocatie, L.CapacitateLocatie
    FROM locatie L
    WHERE L.CapacitateLocatie > (
        SELECT AVG(L2.CapacitateLocatie)
        FROM locatie L2
        JOIN eveniment E ON L2.LocatieID = E.LocatieID
        WHERE E.DataEveniment >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    )
";

$result_locations = $conn->query($query_locations);

//3 subcereri
// Interogarea pentru locații cu cost total al evenimentelor mai mare decât media
$query_high_cost_locations = "
    SELECT L.LocatieID, L.NumeLocatie, SUM(E.CostEveniment) AS TotalCosturi
    FROM locatie L
    JOIN eveniment E ON L.LocatieID = E.LocatieID
    GROUP BY L.LocatieID, L.NumeLocatie
    HAVING SUM(E.CostEveniment) > (
        SELECT AVG(TotalCosturi)
        FROM (
            SELECT SUM(E2.CostEveniment) AS TotalCosturi
            FROM locatie L2
            JOIN eveniment E2 ON L2.LocatieID = E2.LocatieID
            GROUP BY L2.LocatieID
        ) AS Subquery
    )
";

$result_high_cost_locations = $conn->query($query_high_cost_locations);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6dd5fa, #ffffff);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            text-align: center;
            width: 100%;
        }

        main {
            margin: 2rem;
            width: 90%;
            max-width: 1200px;
        }

        section {
            margin-bottom: 3rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table thead {
            background-color: #3498db;
            color: white;
        }

        table th, table td {
            padding: 1rem;
            text-align: left;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            text-align: center;
            width: 100%;
            margin-top: auto;
        }

        footer a {
            color: #1abc9c;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>Event Statistics</h1>
    </header>
    <main>

    <section>
    <h2>Filter Events by Location Capacity</h2>
    <form method="POST" action="">
        <input type="hidden" name="form_type" value="capacitate_form">
        <label for="capacitate_minima">Minimum location capacity:</label>
        <input type="number" id="capacitate_minima" name="capacitate_minima" min="1" required>
        <button type="submit">Search</button>
    </form>

    <?php if (isset($result_evenimente_locatii) && $result_evenimente_locatii->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Location Name</th>
                    <th>Location Capacity</th>
                    <th>Event Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_evenimente_locatii->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['EvenimentID']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeEveniment']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeLocatie']); ?></td>
                        <td><?php echo htmlspecialchars($row['CapacitateLocatie']); ?></td>
                        <td><?php echo htmlspecialchars($row['DataEveniment']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (isset($result_evenimente_locatii)): ?>
        <p>No events were found that meet the criteria.</p>
    <?php endif; ?>
</section>

<section>
    <h2>Filter Organizers by Event Date</h2>
    <form method="POST" action="">
        <input type="hidden" name="form_type" value="data_form">
        <label for="data_maxima">Enter maximum event date (YYYY-MM-DD):</label>
        <input type="date" id="data_maxima" name="data_maxima" required>
        <button type="submit">Search</button>
    </form>

    <?php if (isset($result_organizatori_evenimente) && $result_organizatori_evenimente->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Organizer First Name</th>
                    <th>Organizer Last Name</th>
                    <th>Event Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_organizatori_evenimente->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['PrenumeOrganizator']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeOrganizator']); ?></td>
                        <td><?php echo htmlspecialchars($row['DataEveniment']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (isset($result_organizatori_evenimente)): ?>
        <p>No organizers were found for events before the specified date.</p>
    <?php endif; ?>
</section>

<section>
    <h2>Events Held at a Specific Location with Organizers</h2>
    <?php if ($result_location_organizer_events->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Location Name</th>
                    <th>Organizer Last Name</th>
                    <th>Organizer First Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_location_organizer_events->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['EvenimentID']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeEveniment']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeLocatie']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeOrganizator']); ?></td>
                        <td><?php echo htmlspecialchars($row['PrenumeOrganizator']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No events found at this location with organizers.</p>
    <?php endif; ?>
</section>

<section>
    <h2>Events with Suppliers and Their Services</h2>
    <?php if ($result_event_supplier_services->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Supplier Name</th>
                    <th>Service Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_event_supplier_services->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['EvenimentID']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeEveniment']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeFurnizor']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeServiciu']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No events found with suppliers and services.</p>
    <?php endif; ?>
</section>

<section>
    <h2>Events at Large Capacity Locations with Their Invoices</h2>
    <?php if ($result_large_capacity_events_invoices->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Location Name</th>
                    <th>Location Capacity</th>
                    <th>Invoice Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_large_capacity_events_invoices->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['EvenimentID']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeEveniment']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeLocatie']); ?></td>
                        <td><?php echo htmlspecialchars($row['CapacitateLocatie']); ?></td>
                        <td><?php echo number_format($row['TotalPlata'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No events found at large capacity locations with invoices.</p>
    <?php endif; ?>
</section>

<section>
    <h2>Events Organized by a Specific Organizer with Suppliers</h2>
    <?php if ($result_organizer_supplier_events->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Organizer Last Name</th>
                    <th>Organizer First Name</th>
                    <th>Supplier Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_organizer_supplier_events->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['EvenimentID']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeEveniment']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeOrganizator']); ?></td>
                        <td><?php echo htmlspecialchars($row['PrenumeOrganizator']); ?></td>
                        <td><?php echo htmlspecialchars($row['NumeFurnizor']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No events found for this organizer with suppliers.</p>
    <?php endif; ?>
</section>


        <section>
            <h2>Events with Price Greater than Average</h2>
            <?php if ($result_events->num_rows > 0): ?>
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
                        <?php while ($row = $result_events->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['EvenimentID']); ?></td>
                                <td><?php echo htmlspecialchars($row['NumeEveniment']); ?></td>
                                <td><?php echo htmlspecialchars($row['DataEveniment']); ?></td>
                                <td><?php echo number_format($row['CostEveniment'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No events found with a price greater than the average price.</p>
            <?php endif; ?>
        </section>
        <section>
            <h2>Locations with Capacity Greater than Average (Last 6 Months)</h2>
            <?php if ($result_locations->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Location Name</th>
                            <th>Address</th>
                            <th>Capacity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_locations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['LocatieID']); ?></td>
                                <td><?php echo htmlspecialchars($row['NumeLocatie']); ?></td>
                                <td><?php echo htmlspecialchars($row['AdresaLocatie']); ?></td>
                                <td><?php echo htmlspecialchars($row['CapacitateLocatie']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No locations found with capacity greater than the average capacity.</p>
            <?php endif; ?>
        </section>
        <section>
            <h2>Locations with Total Event Costs Greater than Average</h2>
            <?php if ($result_high_cost_locations->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Location Name</th>
                            <th>Total Costs (Lei)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_high_cost_locations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['LocatieID']); ?></td>
                                <td><?php echo htmlspecialchars($row['NumeLocatie']); ?></td>
                                <td><?php echo number_format($row['TotalCosturi'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No locations found with total event costs greater than the average.</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Events. All rights reserved. | <a href="adminhome.php">Back to Dashboard</a></p>
    </footer>
</body>
</html>

<?php
// Închidem conexiunea
$conn->close();
?>
