
<?php
session_start();

$voucher = $_SESSION['new_voucher'] ?? null;
unset($_SESSION['new_voucher']);
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vocha yako - KENDRI WiFi</title>
</head>
<body>
    <h2>KENDRI WiFi</h2>

    <?php if ($voucher): ?>
        <p>Vocha yako imetengenezwa:</p>
        <h3><?= htmlspecialchars($voucher) ?></h3>
        <p>Hifadhi code hii ili uitumie kuanza kifurushi chako.</p>
    <?php else: ?>
        <p>Hakuna vocha mpya ya kuonyesha.</p>
    <?php endif; ?>

    <a href="index.html">Rudi kwenye vifurushi</a>
</body>
</html>
