<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $unhashed_pw = $_POST['password'];
    $password = password_hash($unhashed_pw, PASSWORD_DEFAULT);


    //pārbauda vai epasts jau ir datubāzē
    $check_email_sql = "SELECT U_ID FROM user WHERE EMAIL = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // epasts eksistee
        echo "Šis epasts jau ir reģistrēts. Lūdzu, izmantojiet citu epastu.";
    } else {
        $sql = "INSERT INTO user (NAME, LASTNAME, EMAIL, PASSWORD) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $lastname, $email, $password);

        if ($stmt->execute()) {
            echo "Lietotājs reģistrēts";
            header("Location: login.php");
        } else {
            echo "Kļūda: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reģistrēšanās</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="registration">
    <h1>Reģistrācija</h1>
    <form method="POST" action="">
        <label for="name">Vārds:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="lastname">Uzvārds:</label>
        <input type="text" id="lastname" name="lastname" required><br>

        <label for="email">Epasts:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Parole:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit" id="register-button">Reģistrēties</button>
    </form>
    </div>
</body>
</html>