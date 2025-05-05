<?php

include 'db_connection.php';

$message = '';

// Daten aus dem Formular holen
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Name validieren
    if (empty($username)) {
        $message = "Name ist erforderlich";
    } else {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $username)) {
            $message = "Nur Buchstaben und Leerzeichen erlaubt";
        } else {
            // Überprüfen, ob der Benutzername bereits existiert
            $stmt = $conn->prepare("SELECT id FROM benutzer WHERE benutzername = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $message = "Benutzername ist bereits vergeben.";
            } else {
                // Passwort hashen
                $passwortHash = password_hash($password, PASSWORD_DEFAULT);

                // Neuen Benutzer in die Datenbank einfügen
                $stmt = $conn->prepare("INSERT INTO benutzer (benutzername, passwort) VALUES (?, ?)");
                $stmt->bind_param("ss", $username, $passwortHash);

                if ($stmt->execute()) {
                    $message = "Registrierung erfolgreich! Du kannst dich jetzt anmelden.";
                } else {
                    $message = "Fehler bei der Registrierung.";
                }
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
    <title>Registrieren</title>
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
            <div class="row justify-content-center mt-3">
                <div class="col-lg-4">
                    <h2>Registrieren</h2>
                    <form method="post" action="">
                        <label class="form-label" for="username">Benutzername:</label><br>
                        <input class="form-control" type="text" id="username" name="username" required><br><br>
                        <label class="form-label" for="password">Passwort:</label><br>
                        <input class="form-control" type="password" id="password" name="password" required><br><br>
                        <button class="btn btn-primary" type="submit">Registrieren</button>
                    </form>
                </div>
            </div>
            <?php if (isset($message)): ?>
                <div class="row justify-content-center mt-3">
                    <div class="col-lg-4 text-secondary">
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