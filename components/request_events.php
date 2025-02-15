<!-- Fisierul prin care clientul solicita un eveniment -->

<?php
// Conectare la baza de date
include 'connect.php';

// Inițializăm sesiunea
session_start();

// Verificăm dacă utilizatorul este logat
if (!isset($_SESSION['username'])) {
    echo "You are not logged in. Please log in.";
    exit();
}

// Verificăm dacă ClientID este setat în sesiune
if (!isset($_SESSION['ClientID'])) {
    // Dacă ClientID nu este setat, îl extragem din baza de date
    $username = $_SESSION['username']; // Numele utilizatorului logat
    $query = $conn->prepare("SELECT ClientID FROM login WHERE username = ?");
    $query->bind_param('s', $username);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['ClientID'] = $row['ClientID']; // Setează ClientID în sesiune
    } else {
        echo "User not found!";
        exit();
    }
}

// Obținem listele de locații, organizatori, furnizori și servicii furnizor
$locations = $conn->query("SELECT LocatieID, NumeLocatie, CapacitateLocatie FROM locatie");
$organizers = $conn->query("SELECT OrganizatorID, NumeOrganizator, PrenumeOrganizator FROM organizator");
$suppliers = $conn->query("SELECT FurnizorID, NumeFurnizor FROM furnizor");
$supplier_services = $conn->query("SELECT ServiciiFurnizorID, NumeServiciu, CostServiciu FROM serviciifurnizor");

// Verificăm dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['ClientID']; // ID-ul clientului logat
    $locatie_id = $_POST['LocatieID'];
    $organizator_id = $_POST['OrganizatorID'];
    $furnizor_id = $_POST['FurnizorID'];
    $serviciu_id = $_POST['ServiciiFurnizorID'];
    $nume_eveniment = $_POST['NumeEveniment'];
    $data_eveniment = $_POST['DataEveniment'];
    $rol_furnizor = $_POST['RolFurnizor']; // Adăugăm rolul furnizorului
    $data_colaborare = $_POST['DataColaborare']; // Adăugăm data colaborării
    $numar_invitati = $_POST['NumarInvitati'];


    // Obținem prețul serviciului selectat
    $service_query = $conn->prepare("SELECT CostServiciu FROM serviciifurnizor WHERE ServiciiFurnizorID = ?");
    $service_query->bind_param('i', $serviciu_id);
    $service_query->execute();
    $service_result = $service_query->get_result();
    $service = $service_result->fetch_assoc();
    $cost_eveniment = $service['CostServiciu']; // Corectez pentru a folosi CostServiciu

    $location_query = $conn->prepare("SELECT CapacitateLocatie FROM locatie WHERE LocatieID = ?");
    $location_query->bind_param('i', $locatie_id);
    $location_query->execute();
    $location_result = $location_query->get_result();
    $location = $location_result->fetch_assoc();
    $capacitate_locatie = $location['CapacitateLocatie'];

    if($numar_invitati > $capacitate_locatie) {
        $message = "Numarul de invitati depaseste capacitatea locatiei. Alegeti o alta locatie.";
    }
    else {
    // Inserăm evenimentul în tabela eveniment
    $insert_event = $conn->prepare(
        "INSERT INTO eveniment (ClientID, LocatieID, OrganizatorID, NumeEveniment, DataEveniment, CostEveniment) 
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    $insert_event->bind_param('iiissd', $client_id, $locatie_id, $organizator_id, $nume_eveniment, $data_eveniment, $cost_eveniment);

    if ($insert_event->execute()) {
        $eveniment_id = $conn->insert_id;

        // Inserăm relația cu furnizorul în tabela evenimentfurnizor
        $insert_supplier_event = $conn->prepare(
            "INSERT INTO evenimentfurnizor (EvenimentID, FurnizorID, RolFurnizor, DataColaborare) 
            VALUES (?, ?, ?, ?)"
        );
        $insert_supplier_event->bind_param('iiss', $eveniment_id, $furnizor_id, $rol_furnizor, $data_colaborare);
        $insert_supplier_event->execute();

        $message = "Eveniment solicitat cu succes!";
    } else {
        $message = "Eroare la solicitarea evenimentului: " . $conn->error;
    }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicită Eveniment</title>
    <style>
           /* Stilizare generală */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            margin-top: 30px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form {
            display: grid;
            gap: 20px;
        }

        label {
            font-size: 1.1em;
            color: #555;
            font-weight: bold;
        }

        input[type="text"], input[type="date"], input[type="number"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            background-color: #f9f9f9;
        }

        input[type="text"]:focus, input[type="date"]:focus, input[type="number"]:focus, select:focus {
            border-color: #4CAF50;
            background-color: #fff;
        }

        button {
            padding: 12px 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .error {
            color: #f44336;
        }

        .form-section {
            margin-bottom: 20px;
        }

        select, input[type="text"], input[type="number"], input[type="date"] {
            font-size: 1em;
            padding: 12px;
        }

        /* Stilizare pentru titlu */
        h2 {
            text-align: center;
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
        }

        /* Adăugarea unui background pentru formular */
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Stilizare pentru inputuri și selecturi */
        input[type="text"], input[type="number"], input[type="date"], select {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
        }

        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus, select:focus {
            background-color: #fff;
            border-color: #4CAF50;
        }

        /* Stilizare pentru mesajele de succes și eroare */
        .message {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .message.success {
            color: #4CAF50;
        }

        .message.error {
            color: #f44336;
        }

        /* Stilizare pentru butonul de trimitere */
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1em;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Solicită Eveniment</h1>

    <?php if (isset($message)) { ?>
        <div class="message <?php echo (isset($message) && strpos($message, 'Eroare') !== false) ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <h2>Completează detaliile evenimentului</h2>

    <form method="POST">
        <div class="form-section">
            <label for="NumeEveniment">Nume Eveniment:</label>
            <input type="text" name="NumeEveniment" id="NumeEveniment" required>
        </div>

        <div class="form-section">
            <label for="DataEveniment">Data Eveniment:</label>
            <input type="date" name="DataEveniment" id="DataEveniment" required>
        </div>

        <div class="form-section">
            <label for="locatie">Alege locația:</label>
            <select name="LocatieID" id="locatie" required>
                <?php while ($row = $locations->fetch_assoc()) { ?>
                    <option value="<?php echo $row['LocatieID']; ?>"><?php echo $row['NumeLocatie']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-section">
            <label for="NumarInvitati">Număr Invitați:</label>
            <input type="number" name="NumarInvitati" id="NumarInvitati" required>
        </div>

        <div class="form-section">
            <label for="organizator">Alege organizatorul:</label>
            <select name="OrganizatorID" id="organizator" required>
                <?php while ($row = $organizers->fetch_assoc()) { ?>
                    <option value="<?php echo $row['OrganizatorID']; ?>"><?php echo $row['NumeOrganizator'] . " " . $row['PrenumeOrganizator']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-section">
            <label for="furnizor">Alege furnizorul:</label>
            <select name="FurnizorID" id="furnizor" required>
                <?php while ($row = $suppliers->fetch_assoc()) { ?>
                    <option value="<?php echo $row['FurnizorID']; ?>"><?php echo $row['NumeFurnizor']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-section">
            <label for="serviciu">Alege serviciul furnizor:</label>
            <select name="ServiciiFurnizorID" id="serviciu" required>
                <?php while ($row = $supplier_services->fetch_assoc()) { ?>
                    <option value="<?php echo $row['ServiciiFurnizorID']; ?>">
                        <?php echo $row['NumeServiciu'] . " - " . $row['CostServiciu'] . " RON"; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-section">
            <label for="RolFurnizor">Rol Furnizor:</label>
            <input type="text" name="RolFurnizor" id="RolFurnizor" required>
        </div>

        <div class="form-section">
            <label for="DataColaborare">Data Colaborării:</label>
            <input type="date" name="DataColaborare" id="DataColaborare" required>
        </div>

        <button type="submit">Solicită Eveniment</button>
    </form>
</div>
</body>
</html>