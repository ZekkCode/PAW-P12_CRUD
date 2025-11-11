<?php
include 'db.php';
$errors = [];
if (isset($_POST['submit'])) {
    $nim = trim($_POST['nim']);
    $name = trim($_POST['name']);
    $major = trim($_POST['major']);
    $photo_name = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];
        $allowed = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) $errors[] = 'Only JPG, JPEG, PNG allowed.';
        if ($file['size'] > 2 * 1024 * 1024) $errors[] = 'File size must be <= 2MB.';
        if (empty($errors)) {
            if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0777, true);
            $photo_name = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $target = __DIR__ . '/uploads/' . $photo_name;
            if (!move_uploaded_file($file['tmp_name'], $target)) $errors[] = 'Failed to move uploaded file.';
        }
    }
    if (empty($errors)) {
        $sql = "INSERT INTO students (nim, name, major, photo) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $nim, $name, $major, $photo_name);
        if (mysqli_stmt_execute($stmt)) { header('Location: index.php'); exit; } 
        else $errors[] = 'Insert error: ' . mysqli_error($conn);
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Student</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col items-center py-10 min-h-screen">
  <div class="bg-white p-6 rounded-2xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Add Student</h2>
    <?php if (!empty($errors)) echo '<div class="bg-red-100 text-red-600 p-3 rounded mb-3 text-sm">'.implode('<br>', $errors).'</div>'; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block text-sm text-gray-600 mb-1">NIM</label>
        <input type="text" name="nim" required class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Name</label>
        <input type="text" name="name" required class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Major</label>
        <input type="text" name="major" required class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Photo</label>
        <input type="file" name="photo" accept="image/*" class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
      </div>
      <button type="submit" name="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">Save</button>
    </form>
    <a href="index.php" class="block text-center text-blue-500 mt-4 hover:underline">← Back to list</a>
  </div>
</body>
</html>
