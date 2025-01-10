<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT U_ID, PASSWORD, ADMIN FROM user WHERE EMAIL = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();


        if (password_verify($password, $user['PASSWORD'])) {
            $_SESSION['user_id'] = $user['U_ID'];
            $_SESSION['role'] = $user['ADMIN'];
            header("Location: index.php");
            echo "Veiksmīgi ienākts!";
        } else {
            echo "Nepareiza parole.";
        }
    } else {
        echo "Epasts nav reģistrēts.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ielogošanās</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="login">
    <h1>Ielogošanās</h1>
    <form method="POST" action="">

        <label for="email">Epasts:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Parole:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit" id="login-button">Ienākt</button>
    </form>
    </div>
</body>
</html>
