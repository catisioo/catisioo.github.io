<?php
session_start();
include 'db_connection.php';

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ziedu Veikals</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <form method="GET" action="" class="search-sort-form">
            <input type="text" name="search" placeholder="Meklēt pēc nosaukuma vai krāsas" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <select name="sort">
                <option value="">Kārtot</option>
                <option value="a-z" <?= (isset($_GET['sort']) && $_GET['sort'] == 'a-z') ? 'selected' : '' ?>>A-Z</option>
                <option value="z-a" <?= (isset($_GET['sort']) && $_GET['sort'] == 'z-a') ? 'selected' : '' ?>>Z-A</option>
                <option value="$-$$" <?= (isset($_GET['sort']) && $_GET['sort'] == '$-$$') ? 'selected' : '' ?>>$-$$</option>
                <option value="$$-$" <?= (isset($_GET['sort']) && $_GET['sort'] == '$$-$') ? 'selected' : '' ?>>$$-$</option>
            </select>
            <button type="submit">Meklēt</button>
        </form>
    <div class="content">
        
        <!--search and sort sadaļa-->
        

        <?php
        include 'db_connection.php';
        
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? '';

        $sql = "SELECT flowers.F_ID, flowers.NAME, flowers.LATIN_NAME, flowers.STOCK_LEFT, flowers.PRICE, flowers.IMAGE 
                FROM flowers";

        if (!empty($search)) {
            $search = "%$search%";
            $sql .= " WHERE flowers.NAME LIKE ? OR flowers.LATIN_NAME LIKE ? OR flowers.COLOR LIKE ?";
        }

        if ($sort == 'a-z') {
            $sql .= " ORDER BY flowers.NAME ASC";
        } elseif ($sort == 'z-a') {
            $sql .= " ORDER BY flowers.NAME DESC";
        } elseif ($sort == '$-$$') {
            $sql .= " ORDER BY flowers.PRICE ASC";
        } elseif ($sort == '$$-$') {
            $sql .= " ORDER BY flowers.PRICE DESC";
        }

        $stmt = $conn->prepare($sql);
        
        if (!empty($search)) {
            $stmt->bind_param("sss", $search, $search, $search);
        }

        $stmt->execute();
        $result = $stmt->get_result();
    
        //flowers card sadaļa
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='flower-card'>";
                echo "<img src='" . htmlspecialchars($row['IMAGE']) . "' alt='" . htmlspecialchars($row['NAME']) . "' class='flower-image' width='150' height='150'>";
                echo "<h2><a href='/flower.php?F_ID=" . htmlspecialchars($row['F_ID']) . "'>" . htmlspecialchars($row['NAME']) . "</a></h2>";
                echo "<p>" . htmlspecialchars($row['LATIN_NAME']) . "</p>";
                echo "<p>Cena: " . number_format($row['PRICE'], 2) . " eiro</p>";
                echo "</div>";
            }
        } else {
            echo "Nav pieejamas preces.";
        }


        $conn->close();
        ?>
    </div>
</body>
</html>
