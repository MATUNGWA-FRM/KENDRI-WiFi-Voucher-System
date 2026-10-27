
<?php
session_start();
require_once 'db.php';

$packages = [
    'hours8'  => ['name' => 'MASAA 8',  'price' => 500,   'seconds' => 8 * 3600],
    'hours24' => ['name' => 'MASAA 24', 'price' => 1000,  'seconds' => 24 * 3600],
    'days3'   => ['name' => 'SIKU 3',   'price' => 3000,  'seconds' => 3 * 86400],
    'days7'   => ['name' => 'SIKU 7',   'price' => 5000,  'seconds' => 7 * 86400],
    'days30'  => ['name' => 'SIKU 30',  'price' => 19500, 'seconds' => 30 * 86400],
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Tafadhali chagua kifurushi kwanza.');
}

$selected = $_POST['package'] ?? '';

if (!isset($packages[$selected])) {
    exit('invalid voucher.');
}

$package = $packages[$selected];

// Generate code yenye randomness ya kutosha.
$voucher = 'MSP-' . strtoupper(bin2hex(random_bytes(5)));

$stmt = $pdo->prepare(
    'INSERT INTO wifi_vouchers
    (voucher_code, package_name, price_tzs, duration_seconds)
    VALUES (?, ?, ?, ?)'
);

$stmt->execute([
    $voucher,
    $package['name'],
    $package['price'],
    $package['seconds']
]);

$_SESSION['new_voucher'] = $voucher;

header('Location: voucher.php');
exit;
