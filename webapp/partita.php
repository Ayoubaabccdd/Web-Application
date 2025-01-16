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

// Recupero dei dettagli della partita
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM `partita utente` WHERE ID = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $partita = $result->fetch_assoc();
    } else {
        die("Partita non trovata.");
    }
} else {
    die("ID partita non specificato.");
}

// Gestione aggiornamento delle mosse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mosse'])) {
    $mosse = $conn->real_escape_string($_POST['mosse']);

    // Verifica il formato della mossa (esempio: e2-e4)
    if (preg_match('/^[a-h][1-8]-[a-h][1-8]$/', $mosse)) {
        if (empty($partita['mosse'])) {
            // Se non ci sono mosse precedenti, aggiungi la prima mossa
            $sql = "UPDATE `partita utente` SET mosse = '$mosse' WHERE ID = $id";
        } else {
            // Se ci sono mosse precedenti, aggiungi la nuova mossa
            $sql = "UPDATE `partita utente` SET mosse = CONCAT(mosse, ',', '$mosse') WHERE ID = $id";
        }

        if ($conn->query($sql) === TRUE) {
            // Aggiorna i dettagli della partita
            $sql = "SELECT * FROM `partita utente` WHERE ID = $id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $partita = $result->fetch_assoc();
            }
        } else {
            $error = "Errore durante l'aggiornamento delle mosse: " . $conn->error;
        }
    } else {
        $error = "Formato mossa non valido. Usa il formato: e2-e4.";
    }
}

// Funzione per generare la scacchiera in base alle mosse salvate
function generateChessboard($mosse) {
    $board = [
        'a1' => '♜', 'b1' => '♘', 'c1' => '♗', 'd1' => '♕', 'e1' => '♔', 'f1' => '♗', 'g1' => '♘', 'h1' => '♖',
        'a2' => '♙', 'b2' => '♙', 'c2' => '♙', 'd2' => '♙', 'e2' => '♙', 'f2' => '♙', 'g2' => '♙', 'h2' => '♙',
        'a7' => '♟', 'b7' => '♟', 'c7' => '♟', 'd7' => '♟', 'e7' => '♟', 'f7' => '♟', 'g7' => '♟', 'h7' => '♟',
        'a8' => '♖', 'b8' => '♘', 'c8' => '♗', 'd8' => '♕', 'e8' => '♔', 'f8' => '♗', 'g8' => '♘', 'h8' => '♖',
    ];

    // Verifica e applica le mosse
    if ($mosse) {
        $moves = explode(',', $mosse);
        foreach ($moves as $move) {
            $parts = explode('-', $move);
            if (count($parts) === 2) {
                list($from, $to) = $parts;
                // Se la casella di destinazione è già occupata da un pezzo, il pezzo catturato scompare
                if (isset($board[$to])) {
                    unset($board[$to]); // Rimuove il pezzo già presente
                }
                // Sposta il pezzo
                if (isset($board[$from])) {
                    $board[$to] = $board[$from];
                    unset($board[$from]);
                }
            }
        }
    }

    return $board;
}

$chessboard = generateChessboard($partita['mosse'] ?? '');
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dettagli Partita</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        text-align: center;
        padding: 20px;
    }
    .chessboard-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
    }
    .chessboard {
        display: grid;
        grid-template-columns: repeat(8, 50px);
        grid-template-rows: repeat(8, 50px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: 2px solid #333;
    }
    .chessboard div {
        width: 50px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
    }
    .light {
        background-color: #eeeed2;
    }
    .dark {
        background-color: #769656;
    }
</style>

</head>
<body>
    <h1>Dettagli Partita</h1>
    <p><strong>ID:</strong> <?= $partita['ID'] ?></p>
    <p><strong>ID Utente:</strong> <?= $partita['ID_UTENTE'] ?></p>
    <p><strong>Data:</strong> <?= $partita['data'] ?></p>
    <p><strong>Mosse:</strong> <?= $partita['mosse'] ?: 'Nessuna mossa' ?></p>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <div class="chessboard-container">
    <div class="chessboard">
        <?php
        for ($row = 8; $row >= 1; $row--) {
            for ($col = 1; $col <= 8; $col++) {
                $square = chr(96 + $col) . $row;
                $class = ($row + $col) % 2 === 0 ? 'light' : 'dark';
                echo "<div class='$class'>" . ($chessboard[$square] ?? '') . "</div>";
            }
        }
        ?>
    </div>
</div>

    <form method="POST">
        <label for="mosse">Inserisci mossa:</label>
        <input type="text" id="mosse" name="mosse" required pattern="[a-h][1-8]-[a-h][1-8]" title="Formato mosse es. e2-e4">
        <button type="submit">Aggiungi Mossa</button>
    </form>

    <a href="index.php">Torna all'elenco delle partite</a>
</body>
</html>

<?php
$conn->close();
?>
