<?php
// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web_application";

$conn = new mysqli($servername, $username, $password, $dbname);

// Controllo della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Creazione di una nuova partita
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = date('Y-m-d');
    $mosse = NULL; // Default per nuove partite

    $sql = "INSERT INTO `partita utente` (ID_UTENTE, ID_PARTITA, data, mosse) VALUES (1, 0, '$data', NULL)";
    if ($conn->query($sql) === TRUE) {
        echo "Nuova partita creata con successo.";
    } else {
        echo "Errore: " . $conn->error;
    }
}

// Recupero delle partite
$sql = "SELECT ID, data FROM `partita utente` ORDER BY data DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Partite</title>
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin: 10px 0;
            color: #555;
        }
        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            color: #333;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestione Partite</h1>

        <form method="POST">
            <button type="submit">Crea Nuova Partita</button>
        </form>

        <h2>Elenco Partite</h2>
        <ul>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <a href="partita.php?id=<?= $row['ID'] ?>">
                            Partita #<?= $row['ID'] ?> - <?= $row['data'] ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>Nessuna partita disponibile.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>

<?php
$conn->close();
?>


