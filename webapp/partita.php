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
    if (empty($partita['mosse'])) {
        $sql = "UPDATE `partita utente` SET mosse = '$mosse' WHERE ID = $id";
    } else {
        $sql = "UPDATE `partita utente` SET mosse = CONCAT(mosse, ',', '$mosse') WHERE ID = $id";
    }
    if ($conn->query($sql) === TRUE) {
        echo "Mossa aggiunta con successo.";
        // Aggiorna i dettagli della partita
        $sql = "SELECT * FROM `partita utente` WHERE ID = $id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $partita = $result->fetch_assoc();
        }
    } else {
        echo "Errore durante l'aggiornamento delle mosse: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Partita</title>
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        .chessboard {
            display: grid;
            grid-template-columns: repeat(8, 50px);
            grid-template-rows: repeat(8, 50px);
            gap: 1px;
            margin: 20px auto;
            width: 416px;
            height: 416px;
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
        .chessboard .dark {
            background-color: #769656;
        }
        .chessboard .light {
            background-color: #eeeed2;
        }
        .piece {
            cursor: pointer;
            user-select: none;
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
        <h1>Dettagli Partita</h1>

        <p><strong>ID:</strong> <?= $partita['ID'] ?></p>
        <p><strong>ID Utente:</strong> <?= $partita['ID_UTENTE'] ?></p>
        <p><strong>ID Partita:</strong> <?= $partita['ID_PARTITA'] ?></p>
        <p><strong>Data:</strong> <?= $partita['data'] ?></p>
        <p><strong>Mosse:</strong> <?= $partita['mosse'] ?: 'Nessuna mossa inserita' ?></p>

        <div class="chessboard" id="chessboard">
            <!-- La scacchiera verrà generata dinamicamente in JavaScript -->
        </div>

        
        <form method="POST">
            <label for="mosse">Inserisci mossa:</label>
            <input type="text" id="mosse" name="mosse" required pattern="[a-h][1-8]-[a-h][1-8]" title="Formato mosse scacchi es. e2-e4">
            <button type="submit">Aggiungi Mossa</button>
        </form>

        <a href="index.php">Torna all'elenco delle partite</a>
    </div>

    <script>
    const chessboard = document.getElementById('chessboard');

    // Funzione per generare la scacchiera
    function generateBoard() {
        const pieces = {
            'a1': '♜', 'b1': '♘', 'c1': '♗', 'd1': '♕', 'e1': '♔', 'f1': '♗', 'g1': '♘', 'h1': '♖',
            'a2': '♙', 'b2': '♙', 'c2': '♙', 'd2': '♙', 'e2': '♙', 'f2': '♙', 'g2': '♙', 'h2': '♙',
            'a7': '♟', 'b7': '♟', 'c7': '♟', 'd7': '♟', 'e7': '♟', 'f7': '♟', 'g7': '♟', 'h7': '♟',
            'a8': '♖', 'b8': '♘', 'c8': '♗', 'd8': '♕', 'e8': '♔', 'f8': '♗', 'g8': '♘', 'h8': '♖'
        };

        for (let row = 8; row >= 1; row--) {
            for (let col = 1; col <= 8; col++) {
                const square = document.createElement('div');
                const squareId = `${String.fromCharCode(96 + col)}${row}`;
                square.classList.add((row + col) % 2 === 0 ? 'light' : 'dark');
                square.id = squareId;

                const piece = pieces[squareId];
                if (piece) {
                    const pieceElement = document.createElement('span');
                    pieceElement.classList.add('piece');
                    pieceElement.textContent = piece;
                    square.appendChild(pieceElement);
                }

                chessboard.appendChild(square);
            }
        }
    }

    // Funzione per spostare il pezzo sulla scacchiera
    function movePiece(from, to) {
        const fromSquare = document.getElementById(from);
        const toSquare = document.getElementById(to);

        const piece = fromSquare.querySelector('.piece');
        if (piece) {
            toSquare.appendChild(piece); // Sposta il pezzo nella nuova casella
        }
    }

    // Gestione delle mosse
    function handleMove(mossa) {
        const regex = /^[a-h][1-8]-[a-h][1-8]$/; // Formato mossa: es. e2-e4
        if (regex.test(mossa)) {
            const [from, to] = mossa.split('-');
            movePiece(from, to); // Sposta il pezzo
        } else {
            alert('Formato mossa non valido');
        }
    }

    // Funzione che si attiva al caricamento della pagina
    window.onload = function () {
        generateBoard();

        // Aggiungi l'evento per inviare la mossa
        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const mossaInput = document.getElementById('mosse');
            const mossa = mossaInput.value;
            handleMove(mossa); // Esegui il movimento
            mossaInput.value = ''; // Pulisci il campo mossa
        });
    }
</script>
</body>
</html>

<?php
$conn->close();
?>
