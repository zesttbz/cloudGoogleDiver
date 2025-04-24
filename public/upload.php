<?php
require __DIR__ . '/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
$client->setAccessType('offline');

// Nếu chưa xác thực thì điều hướng sang link xác thực
if (!isset($_SESSION['access_token']) && !isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}

// Xử lý OAuth callback
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    header('Location: upload.php');
    exit();
}

// Gắn token
$client->setAccessToken($_SESSION['access_token']);

if (!isset($_FILES['upload_file']) || $_FILES['upload_file']['error'] != 0) {
    die("❌ Lỗi upload file.");
}

$service = new Google_Service_Drive($client);

$fileMetadata = new Google_Service_Drive_DriveFile([
    'name' => $_FILES['upload_file']['name']
]);

$content = file_get_contents($_FILES['upload_file']['tmp_name']);

$file = $service->files->create($fileMetadata, [
    'data' => $content,
    'mimeType' => mime_content_type($_FILES['upload_file']['tmp_name']),
    'uploadType' => 'multipart',
    'fields' => 'id, webViewLink'
]);

echo "<h3>✅ Upload thành công!</h3>";
echo "📄 Tên file: " . $_FILES['upload_file']['name'] . "<br>";
echo "🔗 Xem file: <a href='" . $file->webViewLink . "' target='_blank'>Tại đây</a>";
