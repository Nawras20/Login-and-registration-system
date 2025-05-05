<?php

session_start();

include 'db_connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password  = $_POST['password'];

    // Name validieren
    if (empty($_POST['username'])) {
        $message = "Name ist erforderlich";
    } else {
        $username = htmlspecialchars(trim($_POST['username']));

        if (!preg_match("/^[a-zA-Z-' ]*$/", $username)) {
            $message = "Nur Buchstaben und Leerzeichen erlaubt";
        } else {
            // Benutzer in der Datenbank suchen
            $stmt = $conn->prepare("SELECT * FROM benutzer WHERE benutzername = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($id, $dbUsername, $dbPassword);  // Bind results to variables
            $stmt->fetch();

            if ($dbUsername && password_verify($password, $dbPassword)) {
                // Login erfolgreich
                $_SESSION['loggedin'] = true;
                $_SESSION["username"] = $dbUsername;
            } else {
                $message = "Benutzername oder Passwort ist falsch.";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <title>Login-Formular</title>
    <link rel="stylesheet" type="text/css" href="resources/css/style.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-5.3.3/css/bootstrap.min.css">
</head>

<body>

    <div id="header" class="container-fluid">
        <header>
            <nav>
                <ul class="nav mb-2 justify-content-center mb-md-0">
                    <li><a href="login.php" class="nav-link px-2"> Anmelden </a></li>
                </ul>
            </nav>
        </header>
    </div>

    <div class="container">
        <main>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <div class="row justify-content-center mt-3">
                    <div class="col-lg-4">
                        <p><?php echo "Willkommen, " . htmlspecialchars($_SESSION['username']); ?></p>
                        <form method="post" action="logout.php">
                            <button class="btn btn-primary" type="submit">Abmelden</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="row justify-content-center mt-3">
                    <div class="col-lg-4">

                        <h2>Anmelden</h2>
                        <form method="post" action="">
                            <label class="form-label" for="username">Benutzername:</label><br>
                            <input class="form-control" type="text" id="username" name="username" required><br><br>
                            <label class="form-label" for="password">Passwort:</label><br>
                            <input class="form-control" type="password" id="password" name="password" required><br><br>
                            <button class="btn btn-primary mb-2" type="submit">Anmelden</button>
                        </form>
                        <div>
                            <p> Sie haben noch kein Online-Konto? </p>
                            <a href="register.php">Hier registrieren</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($message)): ?>
                <div class="row justify-content-center mt-3">
                    <div class="col-lg-4 text-danger">
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <div id="footer" class="container-fluid text-center">
        <footer class="py-3 my-4">
            <div class="col">
                <span class="mb-3 mb-md-0">© 2025 Registration and Login System </span>
            </div>
        </footer>
    </div>

    <script src="vendor/bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>