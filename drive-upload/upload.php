<?php
require 'vendor/autoload.php';

session_start();


$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
$client->setAccessType('offline');

if (!isset($_SESSION['access_token']) && !isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    header('Location: upload.php');
    exit();
}

$client->setAccessToken($_SESSION['access_token']);

if (!isset($_FILES['upload_file']) || $_FILES['upload_file']['error'] != 0) {
    die("❌ Upload lỗi.");
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

echo "<h3>✅ Thành công!</h3>";
echo "🔗 Link: <a href='{$file->webViewLink}' target='_blank'>Xem file</a>";
file_put_contents('credentials.json', getenv('GOOGLE_CREDENTIALS'));
