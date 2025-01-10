<?php
session_start();
include 'db_connection.php';

$flowerId = intval($_GET['F_ID']);

//izvēlas ziedu pēc F_ID
$sql = "SELECT NAME, LATIN_NAME, STOCK_LEFT, PRICE, IMAGE FROM flowers WHERE F_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $flowerId);
$stmt->execute();
$result = $stmt->get_result();

$flower = $result->fetch_assoc();

//pirkšana
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['buy']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $amount = intval($_POST['amount']);

    if ($amount > 0 && $amount <= $flower['STOCK_LEFT']) {
        //stock daudzuma maiņa
        $newStock = $flower['STOCK_LEFT'] - $amount;
        $updateStockSql = "UPDATE flowers SET STOCK_LEFT = ? WHERE F_ID = ?";
        $updateStockStmt = $conn->prepare($updateStockSql);
        $updateStockStmt->bind_param("ii", $newStock, $flowerId);
        $updateStockStmt->execute();

        //ieraksta pasūtījumu orders tabulā
        $addOrderSql = "INSERT INTO orders (U_ID, F_ID, AMOUNT) VALUES (?, ?, ?)";
        $addOrderStmt = $conn->prepare($addOrderSql);
        $addOrderStmt->bind_param("iii", $userId, $flowerId, $amount);
        $addOrderStmt->execute();

        
        echo "<script>alert('Nopirkts $amount zieds/ziedi: {$flower['NAME']}.');</script>";
        header("Location: flower.php?F_ID=" . $flowerId);
        exit;

    } else {
        echo "<script>alert('Nav iespējams iegādāties tik daudz ziedus.');</script>";
    }
}

//komentāri
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $commentText = trim($_POST['comment_text']);
    $pictureSrc = trim($_POST['picture_src']);
    $hasPicture = !empty($pictureSrc) ? 1 : 0;

    if (!empty($commentText)) {
        //ieraksta komentāru comment tabulā
        $addCommentSql = "INSERT INTO comment (U_ID, F_ID, TEXT, HAS_PICTURE, PICTURE_SRC) VALUES (?, ?, ?, ?, ?)";
        $addCommentStmt = $conn->prepare($addCommentSql);
        $addCommentStmt->bind_param("iisis", $userId, $flowerId, $commentText, $hasPicture, $pictureSrc);
        $addCommentStmt->execute();

        echo "<script>alert('Komentārs nosūtīts.');</script>";
        header("Location: flower.php?F_ID=" . $flowerId);
        exit;

    } else {
        echo "<script>alert('Kļūda (Komentārs nevar būt tukšs).');</script>";
    }
}

//izvēlēties komentārus, kuriem F_ID sakrīt ar atvērto lapu
$commentsSql = "SELECT c.TEXT, c.PICTURE_SRC, c.COMMENT_TIME, u.NAME, u.LASTNAME 
                FROM comment c 
                JOIN user u ON c.U_ID = u.U_ID 
                WHERE c.F_ID = ? 
                ORDER BY c.COMMENT_TIME DESC";
$commentsStmt = $conn->prepare($commentsSql);
$commentsStmt->bind_param("i", $flowerId);
$commentsStmt->execute();
$commentsResult = $commentsStmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($flower['NAME']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="flower-container">
    <h1><?php echo htmlspecialchars($flower['NAME']); ?></h1>
    <div class="flower-info">
        <img src="<?php echo htmlspecialchars($flower['IMAGE']); ?>" alt="<?php echo htmlspecialchars($flower['NAME']); ?>" width="300" height="300">
        <h2>Nosaukums: <?php echo htmlspecialchars($flower['NAME']); ?></h2>
        <p>Latīņu nosaukums: <?php echo htmlspecialchars($flower['LATIN_NAME']); ?></p>
        <p>Pieejamo ziedu daudzums: <?php echo htmlspecialchars($flower['STOCK_LEFT']); ?></p>
        <p>Cena: €<?php echo number_format($flower['PRICE'], 2); ?></p>

    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        
        <form method="POST" action="">
            <label for="amount">Pirkšanas daudzums:</label>
            <input type="number" id="amount" name="amount" min="1" max="<?php echo htmlspecialchars($flower['STOCK_LEFT']); ?>" required>
            <button type="submit" name="buy" id="buy-button" >Pirkt</button>
        </form>
        </div>
        <hr>
        <div class="comment-submit">
        <form method="POST" action="">
            <label for="comment_text">Pievienot komentāru:</label>
            <textarea id="comment_text" name="comment_text" rows="4" required></textarea>
            <label for="picture_src">Attēla links (neobligāts):</label>
            <input type="text" id="picture_src" name="picture_src">
            <button type="submit" name="comment" id="comment-button">Komentēt</button>
        </form>
        </div>
    <?php else: ?>
        <p>Lūdzu, <a href="login.php">ienāciet</a>, lai atstātu komentāru.</p>
    <?php endif; ?>
    

    <h2>Komentāri:</h2>
    <div class="comments">
        <?php while ($comment = $commentsResult->fetch_assoc()): ?>
            <div class="comment">
                <p><strong><?php echo htmlspecialchars($comment['NAME'] . " " . $comment['LASTNAME']); ?></strong> @ <?php echo htmlspecialchars($comment['COMMENT_TIME']); ?>:</p>
                <p><?php echo htmlspecialchars($comment['TEXT']); ?></p>
                <?php if (!empty($comment['PICTURE_SRC'])): ?>
                    <img src="<?php echo htmlspecialchars($comment['PICTURE_SRC']); ?>" alt="Comment Picture" width="200">
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
