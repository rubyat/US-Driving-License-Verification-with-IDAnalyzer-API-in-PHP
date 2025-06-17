<?php
require_once __DIR__ . '/../src/IDAnalyzerClient.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    if (isset($env['IDANALYZER_API_KEY'])) {
        putenv('IDANALYZER_API_KEY=' . $env['IDANALYZER_API_KEY']);
    }
}

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['license_image'])) {
    if ($_FILES['license_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['license_image']['tmp_name'];
        $client = new IDAnalyzerClient(getenv('IDANALYZER_API_KEY'));
        $result = $client->verify($tmpName);

        if (isset($result['error'])) {
            $error = "API Error: " . htmlspecialchars($result['error']);
        }
    } else {
        $error = "File upload error.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>US Driving License Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 600px; margin: auto; }
        .error { color: red; }
        .result { background: #f9f9f9; padding: 20px; border-radius: 6px; }
    </style>
</head>
<body>
<div class="container">
    <h1>US Driving License Verification</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="license_image" required>
        <button type="submit">Verify</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($result && !$error): ?>
        <h2>Result:</h2>
        <div class="result">
            <pre><?php echo htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)); ?></pre>
        </div>
        <?php if (isset($result['result']['name'])): ?>
            <h3>Extracted Data:</h3>
            <ul>
                <li><strong>Name:</strong> <?php echo htmlspecialchars($result['result']['name']); ?></li>
                <li><strong>Date of Birth:</strong> <?php echo htmlspecialchars($result['result']['dob'] ?? 'N/A'); ?></li>
                <li><strong>Document Number:</strong> <?php echo htmlspecialchars($result['result']['number'] ?? 'N/A'); ?></li>
                <li><strong>Expiry Date:</strong> <?php echo htmlspecialchars($result['result']['expiry'] ?? 'N/A'); ?></li>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html> 