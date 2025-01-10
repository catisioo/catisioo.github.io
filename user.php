<?php
session_start();
include 'db_connection.php';

$userId = $_SESSION['user_id'];

//lietotāja dati no datubāzes
$sql = "SELECT NAME, LASTNAME FROM user WHERE U_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

//pasūtījumi
$ordersSql = "SELECT o.AMOUNT, f.NAME AS flower_name, f.PRICE 
              FROM orders o
              JOIN flowers f ON o.F_ID = f.F_ID
              WHERE o.U_ID = ?
              ORDER BY o.TIME DESC";
$ordersStmt = $conn->prepare($ordersSql);
$ordersStmt->bind_param("i", $userId);
$ordersStmt->execute();
$ordersResult = $ordersStmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lietotāja profils</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <h1>Jūsu profils</h1>

    <div class="user-info">
        <p><strong>Vārds:</strong> <?php echo htmlspecialchars($user['NAME']); ?></p>
        <p><strong>Uzvārds:</strong> <?php echo htmlspecialchars($user['LASTNAME']); ?></p>

    </div>


    <h2>Pasūtījumi:</h2>
    <?php if ($ordersResult->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nosaukums  </th>
                    <th>Skaits</th>
                    <th>Cena</th>
                    <th>Summa</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $ordersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['flower_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['AMOUNT']); ?></td>
                        <td>€ <?php echo number_format($order['PRICE'], 2); ?></td>
                        <td>€ <?php echo number_format($order['AMOUNT'] * $order['PRICE'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Jūs neesat veicis pasūtījumus.</p>
    <?php endif; ?>
</body>
</html>
