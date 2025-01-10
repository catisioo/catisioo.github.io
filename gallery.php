<?php
//folderis ar attēliem
$photosDir = 'photos';
$photos = array_diff(scandir($photosDir), array('.', '..'));
$photos = array_values($photos);

$currentIndex = isset($_GET['index']) ? intval($_GET['index']) : 0;

if ($currentIndex < 0) {
    $currentIndex = count($photos) - 1;
} elseif ($currentIndex >= count($photos)) {
    $currentIndex = 0;
}

$currentImage = $photos[$currentIndex];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerija</title>
    <script>
        //keyboard shortcuts
        document.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowRight') {
                document.getElementById('next').click();
            } else if (event.key === 'ArrowLeft') {
                document.getElementById('prev').click();
            }
        });
    </script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="gallery-container">
    <img src="<?php echo htmlspecialchars($photosDir . '/' . $currentImage); ?>" height="600">

    </div>
    <div class="gallery-controls">
        <a href="?index=<?php echo ($currentIndex - 1 + count($photos)) % count($photos); ?>">
            <button id="prev"><—</button>
        </a>
        <a href="?index=<?php echo ($currentIndex + 1) % count($photos); ?>">
            <button id="next">—></button>
        </a>
    </div>
</body>
</html>
