
<?php
require_once 'db.php';

$result = null;
$error = '';
$code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['voucher_code'] ?? ''));

    if ($code === '') {
        $error = 'Tafadhali ingiza code ya vocha.';
    } else {
        try {
            $sql = "SELECT voucher_code, package_name, price_tzs,
                           status, started_at, expires_at
                    FROM wifi_vouchers
                    WHERE voucher_code = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$code]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $error = 'Vocha haijapatikana. Hakikisha code umeiandika vizuri.';
            } else {
                // Angalia kama muda wa vocha umeisha
                if (
                    $result['status'] === 'active' &&
                    !empty($result['expires_at']) &&
                    strtotime($result['expires_at']) <= time()
                ) {
                    $update = $pdo->prepare(
                        "UPDATE wifi_vouchers
                         SET status = 'expired'
                         WHERE voucher_code = ? AND status = 'active'"
                    );
                    $update->execute([$code]);

                    $result['status'] = 'expired';
                }
            }
        } catch (PDOException $e) {
            $error = 'Kuna tatizo la database. Hakikisha database na table vipo.';
        }
    }
}

function h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Angalia Vocha | MSPOT WiFi</title>

    <link rel="stylesheet" href="check_voucher.css">
</head>

<body class="check-voucher-page">

    <main class="cv-container">

        <header class="cv-header">
            <h1 class="cv-brand">KENDRI <span>WIFI</span></h1>
            <p class="cv-subtitle">
                Angalia hali ya vocha yako ya WiFi
            </p>
        </header>

        <section class="cv-card">

            <h2 class="cv-card-title">Angalia Vocha</h2>

            <p class="cv-description">
                Ingiza code ya vocha ili kuona hali yake na muda uliobaki.
            </p>

            <form class="cv-form" method="POST" action="check_voucher.php">

                <label for="voucher_code">Code ya vocha</label>

                <input
                    class="cv-input"
                    type="text"
                    id="voucher_code"
                    name="voucher_code"
                    placeholder="MSP-XXXXXXXXXX"
                    value="<?= h($code) ?>"
                    autocomplete="off"
                    required
                >

                <button class="cv-button" type="submit">
                    Angalia Vocha
                </button>

            </form>

            <?php if ($error !== ''): ?>
                <div class="cv-message cv-message-error">
                    <?= h($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($result): ?>

                <div class="cv-details">

                    <h3>Maelezo ya Vocha</h3>

                    <div class="cv-detail-row">
                        <span class="cv-detail-label">Code</span>
                        <span class="cv-detail-value"><?= h($result['voucher_code']) ?></span>
                    </div>

                    <div class="cv-detail-row">
                        <span class="cv-detail-label">Kifurushi</span>
                        <span class="cv-detail-value"><?= h($result['package_name']) ?></span>
                    </div>

                    <div class="cv-detail-row">
                        <span class="cv-detail-label">Bei</span>
                        <span class="cv-detail-value">
                            TZS <?= number_format((float)$result['price_tzs']) ?>
                        </span>
                    </div>

                    <div class="cv-detail-row">
                        <span class="cv-detail-label">Hali</span>
                        <span class="cv-detail-value">

                            <?php if ($result['status'] === 'active'): ?>
                                <span class="cv-status cv-status-active">ACTIVE</span>

                            <?php elseif ($result['status'] === 'unused'): ?>
                                <span class="cv-status cv-status-unused">HAIJAANZA</span>

                            <?php elseif ($result['status'] === 'expired'): ?>
                                <span class="cv-status cv-status-expired">IMEISHA</span>

                            <?php else: ?>
                                <?= h($result['status']) ?>
                            <?php endif; ?>

                        </span>
                    </div>

                    <?php if ($result['status'] === 'unused'): ?>

                        <div class="cv-message cv-message-success">
                            Vocha yako bado haijaanza kutumika.
                            Activate vocha ili muda uanze kuhesabiwa.
                        </div>

                        <p>
                            <a href="activate.php">Activate vocha sasa</a>
                        </p>

                    <?php elseif ($result['status'] === 'expired'): ?>

                        <div class="cv-message cv-message-error">
                            Muda wa vocha hii umeisha.
                        </div>

                    <?php elseif (
                        $result['status'] === 'active' &&
                        !empty($result['expires_at'])
                    ): ?>

                        <div class="cv-detail-row">
                            <span class="cv-detail-label">Ilianza</span>
                            <span class="cv-detail-value"><?= h($result['started_at']) ?></span>
                        </div>

                        <div class="cv-detail-row">
                            <span class="cv-detail-label">Inaisha</span>
                            <span class="cv-detail-value"><?= h($result['expires_at']) ?></span>
                        </div>

                        <div class="cv-countdown-box">
                            <p class="cv-countdown-label">Muda uliobaki</p>
                            <p class="cv-countdown" id="countdown">Inahesabu...</p>
                        </div>

                        <script>
                            const expiryTime =
                                <?= (int) strtotime($result['expires_at']) ?> * 1000;

                            const countdownElement =
                                document.getElementById('countdown');

                            function updateCountdown() {
                                const remaining = expiryTime - Date.now();

                                if (remaining <= 0) {
                                    countdownElement.textContent = 'Muda umeisha';

                                    // Refresh ili PHP ikague na kusasisha status
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1500);

                                    return;
                                }

                                const totalSeconds = Math.floor(remaining / 1000);
                                const days = Math.floor(totalSeconds / 86400);
                                const hours = Math.floor((totalSeconds % 86400) / 3600);
                                const minutes = Math.floor((totalSeconds % 3600) / 60);
                                const seconds = totalSeconds % 60;

                                countdownElement.textContent =
                                    `${days} siku, ${hours} saa, ` +
                                    `${minutes} dakika, ${seconds} sekunde`;
                            }

                            updateCountdown();
                            setInterval(updateCountdown, 1000);
                        </script>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </section>

        <footer class="cv-footer">
            <a href="index.html">Vifurushi</a>
            <a href="activate.php">Activate vocha</a>
        </footer>

    </main>

</body>
</html>
