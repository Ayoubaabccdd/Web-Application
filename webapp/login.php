<?php
// Connessione al database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'web_application';

$conn = new mysqli($host, $user, $password, $dbname);

session_start(); 

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password']; // Recupera la password inserita

        // Prepara e esegui la query per trovare l'utente
        $stmt = $conn->prepare("SELECT * FROM utenti WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifica la password
            if (password_verify($password, $user['password'])) {
                // Login riuscito, salva i dati nella sessione
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['ID']; // Salva l'ID utente nella sessione
                header("Location: index.php");
                exit();
            } else {
                echo "<script>alert('Password errata');</script>";
            }
        } else {
            echo "<script>alert('Username non trovato');</script>";
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
    <title>Login</title>
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
        input[type="text"], input[type="password"] {
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
        <h1>Login</h1>
        <form method="POST">
            <label>Username: <input type="text" name="username" required></label><br>
            <label>Password: <input type="password" name="password" required></label><br>
            <button type="submit" name="login">Accedi</button>
        </form>
        <a href="registro.php">Registrati</a>
    </div>
</body>
</html>
