<?php
session_start();
require_once 'db.php';

date_default_timezone_set('Africa/Dar_es_Salaam');

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['voucher_code'] ?? ''));

    if ($code === '') {
        $message = 'Ingiza code ya vocha kwanza.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'SELECT * FROM wifi_vouchers
                 WHERE voucher_code = ?
                 FOR UPDATE'
            );
            $stmt->execute([$code]);
            $voucher = $stmt->fetch();

            if (!$voucher) {
                $message = 'Vocha haijapatikana.';
            } elseif ($voucher['status'] === 'expired') {
                $message = 'Vocha hii muda wake umeisha.';
            } elseif (
                $voucher['status'] === 'active' &&
                strtotime($voucher['expires_at']) <= time()
            ) {
                $update = $pdo->prepare(
                    "UPDATE wifi_vouchers
                     SET status = 'expired'
                     WHERE voucher_code = ?"
                );
                $update->execute([$code]);

                $message = 'Vocha hii muda wake umeisha.';
            } elseif ($voucher['status'] === 'active') {
                $message = 'Vocha hii tayari imesha-activate.';
                $success = true;
            } elseif ($voucher['status'] === 'unused') {
                $start = new DateTimeImmutable(
                    'now',
                    new DateTimeZone('Africa/Dar_es_Salaam')
                );

                $expiry = $start->modify(
                    '+' . (int) $voucher['duration_seconds'] . ' seconds'
                );

                $update = $pdo->prepare(
                    "UPDATE wifi_vouchers
                     SET status = 'active',
                         started_at = ?,
                         expires_at = ?
                     WHERE voucher_code = ?"
                );

                $update->execute([
                    $start->format('Y-m-d H:i:s'),
                    $expiry->format('Y-m-d H:i:s'),
                    $code
                ]);

                $message = 'Vocha ime-activate kikamilifu!';
                $success = true;
            } else {
                $message = 'Hali ya vocha haitambuliki.';
            }

            $pdo->commit();

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message = 'Hitilafu imetokea. Kagua database na ujaribu tena.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activate Vocha - MSPOT WiFi</title>
	<link rel="stylesheet" href="activate.css">
</head>
<body>

    <h2>KENDRI WiFi</h2>
    <h3>Activate Vocha</h3>
	
   <main class="activate-container">
    <form method="POST" action="activate.php">
        <label for="voucher_code">Code ya vocha:</label>

        <input
            type="text"
            id="voucher_code"
            name="voucher_code"
            placeholder="MSP-XXXXXXXXXX"
            required
        >

        <button type="submit">Activate Vocha</button>
    </form>
	</main>

    <?php if ($message !== ''): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p>
            <a href="check_voucher.php">Angalia muda wa vocha</a>
        </p>
    <?php endif; ?>

    <p><a href="index.html">Rudi kwenye vifurushi</a></p>

</body>
</html>