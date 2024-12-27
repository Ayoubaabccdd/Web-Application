<?php
// Connessione al database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'web_application';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Registrazione utente
        $username = $_POST['username'];
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];

        $stmt = $conn->prepare("INSERT INTO utenti (username, nome, cognome) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $nome, $cognome);

        if ($stmt->execute()) {
            echo "<p>Registrazione completata con successo!</p>";
            echo '<a href="login.php">Vai al login</a>';
        } else {
            echo "<p>Errore nella registrazione: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
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
        .chess-bg {
            background-image: url('chessboard.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body class="chess-bg">
    <div class="container">
        <h1>Registrazione</h1>
        <form method="POST">
            <label>Username: <input type="text" name="username" required></label><br>
            <label>Nome: <input type="text" name="nome" required></label><br>
            <label>Cognome: <input type="text" name="cognome" required></label><br>
            <button type="submit" name="register">Registrati</button>
        </form>
        <a href="login.php">Torna al login</a>
    </div>
</body>
</html>
