<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connection.php';

//vai lietotājs ir ienācis
$isLoggedIn = isset($_SESSION['user_id']);
$userName = '';
$userLastName = '';

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $sql = "SELECT NAME, LASTNAME FROM user WHERE U_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userName = $user['NAME'];
        $userLastName = $user['LASTNAME'];
    }
}
?>

<div class="top-nav">
    <div class="left-links">
        <?php if (!$isLoggedIn): ?>
        
            <a href="registration.php">Reģistrēties</a>
            <a href="login.php">Ienākt</a>
        <?php endif; ?>
    </div>
    <div class="center-links">
        <a href="index.php">Sākums</a>
        <a href="gallery.php?index=0">Galerija</a>
    </div>
    
    <div class="login-status">
        <?php if ($isLoggedIn): ?>
            <a href="user.php">Jūs esat ienācis kā lietotājs <?= htmlspecialchars($userName) ?> <?= htmlspecialchars($userLastName) ?></a>
            <a href="logout.php" class="logout-link">Iziet</a>
        <?php else: ?>
            <span>Jūs neesat ienācis/reģistrējies</span>
        <?php endif; ?>
    </div>
</div>
