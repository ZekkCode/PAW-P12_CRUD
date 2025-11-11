<?php
include 'db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

// ambil file
$stmt = mysqli_prepare($conn, "SELECT photo FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
if ($row) {
    if (!empty($row['photo']) && file_exists(__DIR__ . '/uploads/' . $row['photo'])) {
        @unlink(__DIR__ . '/uploads/' . $row['photo']);
    }
    $del = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
    mysqli_stmt_bind_param($del, 'i', $id);
    mysqli_stmt_execute($del);
}
header('Location: index.php');
exit;
