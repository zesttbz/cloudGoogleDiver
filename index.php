<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Upload to Google Drive</title>
</head>
<body>
  <h2>Upload file lên Google Drive</h2>
  <form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="upload_file" required>
    <button type="submit">Upload</button>
  </form>
</body>
</html>
