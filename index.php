<?php
require_once __DIR__ . '/vendor/autoload.php';

use IDAnalyzer2\Client;
use IDAnalyzer2\Api\Scanner\StandardScan;
use IDAnalyzer2\SDKException;

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    if (isset($env['IDANALYZER_API_KEY'])) {
        putenv('IDANALYZER_API_KEY=' . $env['IDANALYZER_API_KEY']);
    }
}

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['license_image'])) {
    if ($_FILES['license_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['license_image']['tmp_name'];

        try {
            $client = new Client(getenv('IDANALYZER_API_KEY'));

            $scan = new StandardScan();
            $scan->document = base64_encode(file_get_contents($tmpName));
            $scan->profile = "security_medium";

            list($result, $err) = $client->Do($scan);

            if ($err != null) {
                $error = "API Error: " . htmlspecialchars($err->message);
                $result = null;
            }

        } catch (SDKException $e) {
            $error = "SDK Error: " . $e->getMessage();
        } catch (Exception $e) {
            $error = "Exception: " . $e->getMessage();
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-8">
<div class="container mx-auto max-w-lg w-full bg-white p-8 rounded-2xl shadow-lg">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">US Driving License Verification</h1>
    
    <form method="post" enctype="multipart/form-data" class="mb-6">
        <div class="flex flex-col">
            <label for="license_image" class="mb-2 font-semibold text-gray-700 sr-only">Upload License Image:</label>
            <input type="file" name="license_image" id="license_image" required class="block w-full text-sm text-slate-500
              file:mr-4 file:py-3 file:px-6
              file:rounded-full file:border-0
              file:text-sm file:font-semibold
              file:bg-violet-50 file:text-violet-700
              hover:file:bg-violet-100
              cursor-pointer
            "/>
        </div>
        <button type="submit" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
            Verify License
        </button>
    </form>

    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
            <p class="font-bold">Error</p>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($result && !$error): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md">
             <h2 class="text-2xl font-bold mb-4 text-gray-800">Verification Result:</h2>
             
             <?php if (isset($result->data->documentNumber[0]->value)): ?>
                <h3 class="text-xl font-semibold mt-4 mb-2 text-gray-800">Extracted Data</h3>
                <ul class="list-none bg-gray-100 p-4 rounded-lg text-gray-800">
                    <li class="mb-2 flex justify-between"><strong>Name:</strong> <span><?php echo htmlspecialchars(($result->data->firstName[0]->value ?? '') . ' ' . ($result->data->lastName[0]->value ?? '')); ?></span></li>
                    <li class="mb-2 flex justify-between"><strong>Date of Birth:</strong> <span><?php echo htmlspecialchars($result->data->dob[0]->value ?? 'N/A'); ?></span></li>
                    <li class="mb-2 flex justify-between"><strong>Document Number:</strong> <span><?php echo htmlspecialchars($result->data->documentNumber[0]->value ?? 'N/A'); ?></span></li>
                    <li class="flex justify-between"><strong>Expiry Date:</strong> <span><?php echo htmlspecialchars($result->data->expiry[0]->value ?? 'N/A'); ?></span></li>
                </ul>
             <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($result && !$error): ?>
<div class="container mx-auto w-[90%] max-w-4xl mt-8">
    <div class="bg-white p-8 rounded-2xl shadow-lg">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Full API Response</h3>
        <div class="bg-gray-800 text-white p-4 rounded-lg">
            <pre class="text-sm overflow-x-auto whitespace-pre-wrap"><?php echo htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)); ?></pre>
        </div>
    </div>
</div>
<?php endif; ?>
</body>
</html> 