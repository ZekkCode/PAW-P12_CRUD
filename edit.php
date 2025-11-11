<?php
include 'db.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($res);
if (!$student) { header('Location: index.php'); exit; }
$errors = [];
if (isset($_POST['submit'])) {
    $nim = trim($_POST['nim']);
    $name = trim($_POST['name']);
    $major = trim($_POST['major']);
    $photo_name = $student['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];
        $allowed = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) $errors[] = 'Only JPG, JPEG, PNG allowed.';
        if ($file['size'] > 2 * 1024 * 1024) $errors[] = 'File size must be <= 2MB.';
        if (empty($errors)) {
            $newname = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $target = __DIR__ . '/uploads/' . $newname;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                if (!empty($student['photo']) && file_exists(__DIR__ . '/uploads/' . $student['photo'])) @unlink(__DIR__ . '/uploads/' . $student['photo']);
                $photo_name = $newname;
            } else $errors[] = 'Failed to move uploaded file.';
        }
    }
    if (empty($errors)) {
        $sql = "UPDATE students SET nim = ?, name = ?, major = ?, photo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssi', $nim, $name, $major, $photo_name, $id);
        if (mysqli_stmt_execute($stmt)) { header('Location: index.php'); exit; } 
        else $errors[] = 'Update error: ' . mysqli_error($conn);
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Student</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col items-center py-10 min-h-screen">
  <div class="bg-white p-6 rounded-2xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Edit Student</h2>
    <?php if (!empty($errors)) echo '<div class="bg-red-100 text-red-600 p-3 rounded mb-3 text-sm">'.implode('<br>', $errors).'</div>'; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block text-sm text-gray-600 mb-1">NIM</label>
        <input type="text" name="nim" value="<?php echo htmlspecialchars($student['nim']); ?>" required class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Major</label>
        <input type="text" name="major" value="<?php echo htmlspecialchars($student['major']); ?>" required class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Current Photo</label>
        <?php if (!empty($student['photo']) && file_exists('uploads/' . $student['photo'])) { ?>
          <img src="uploads/<?php echo rawurlencode($student['photo']); ?>" class="w-24 h-24 object-cover rounded-md border mb-2">
        <?php } else { echo '<p class="text-gray-400">-</p>'; } ?>
        <input type="file" name="photo" accept="image/*" class="w-full border rounded-lg p-2 mt-1 focus:ring focus:ring-blue-300">
      </div>
      <button type="submit" name="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Update</button>
    </form>
    <a href="index.php" class="block text-center text-blue-500 mt-4 hover:underline">← Back to list</a>
  </div>
</body>
</html>
