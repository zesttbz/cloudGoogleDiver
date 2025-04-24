<?php
require __DIR__ . '/../vendor/autoload.php';

function uploadToDrive($tmpPath, $fileName) {
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/../credentials.json');
    $client->addScope(Google_Service_Drive::DRIVE);

    $service = new Google_Service_Drive($client);

    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $fileName
    ]);

    $content = file_get_contents($tmpPath);

    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => mime_content_type($tmpPath),
        'uploadType' => 'multipart',
        'fields' => 'id'
    ]);

    $permission = new Google_Service_Drive_Permission([
        'type' => 'anyone',
        'role' => 'reader'
    ]);
    $service->permissions->create($file->id, $permission);

    return "https://drive.google.com/uc?id=" . $file->id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $url = uploadToDrive($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    echo "✅ File uploaded: <a href='$url' target='_blank'>$url</a>";
}
?>

<form method="POST" enctype="multipart/form-data">
  <input type="file" name="file" required>
  <button type="submit">Upload to Google Drive</button>
</form>
