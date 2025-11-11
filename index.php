<?php
include 'db.php';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$countSql = "SELECT COUNT(*) AS total FROM students WHERE name LIKE ? OR nim LIKE ? OR major LIKE ?";
$stmt = mysqli_prepare($conn, $countSql);
$like = "%" . $search . "%";
mysqli_stmt_bind_param($stmt, 'sss', $like, $like, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
$total = (int) $row['total'];
$pages = ($total > 0) ? ceil($total / $limit) : 1;

$sql = "SELECT * FROM students WHERE name LIKE ? OR nim LIKE ? OR major LIKE ? ORDER BY created_at DESC LIMIT ?, ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'sssii', $like, $like, $like, $start, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Data Mahasiswa Universitas Trunojoyo Madura</title>
  <meta name="description" content="CRUD Data Mahasiswa Universitas Trunojoyo Madura">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center py-10">
  <div class="w-full max-w-4xl bg-white rounded-2xl shadow p-6">
    <h1 class="text-2xl font-semibold text-gray-700 mb-1 text-center">Data Mahasiswa Universitas Trunojoyo Madura</h1>
    <p class="text-center text-sm text-gray-500 mb-4">Sistem CRUD sederhana - P12 - PAW B</p>
    <div class="flex items-center justify-between mb-4 gap-4">
      <form method="GET" class="flex w-2/3">
        <input type="text" name="search" placeholder="Cari nama, NIM, atau jurusan..." value="<?php echo htmlspecialchars($search); ?>" class="w-full border rounded-l-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" aria-label="Kolom pencarian">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700">Cari</button>
      </form>
      <a href="add.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">+ Tambah Mahasiswa</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="px-4 py-2 text-left">ID</th>
            <th class="px-4 py-2 text-left">NIM</th>
            <th class="px-4 py-2 text-left">Nama</th>
            <th class="px-4 py-2 text-left">Jurusan</th>
            <th class="px-4 py-2 text-left">Foto</th>
            <th class="px-4 py-2 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-600">
          <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2"><?php echo $row['id']; ?></td>
              <td class="px-4 py-2"><?php echo htmlspecialchars($row['nim']); ?></td>
              <td class="px-4 py-2"><?php echo htmlspecialchars($row['name']); ?></td>
              <td class="px-4 py-2"><?php echo htmlspecialchars($row['major']); ?></td>
              <td class="px-4 py-2">
                <?php if (!empty($row['photo']) && file_exists('uploads/' . $row['photo'])) { ?>
                  <img src="uploads/<?php echo rawurlencode($row['photo']); ?>" alt="Foto <?php echo htmlspecialchars($row['name']); ?>" class="w-16 h-16 object-cover rounded-md">
                <?php } else { echo '<span class="text-gray-400">Tidak ada</span>'; } ?>
              </td>
              <td class="px-4 py-2 text-center space-x-2">
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:underline">Ubah</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus data mahasiswa ini?');" class="text-red-600 hover:underline">Hapus</a>
              </td>
            </tr>
          <?php } ?>
          <?php if ($total === 0) { ?>
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada data ditemukan.</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center mt-4 flex-wrap gap-2">
      <?php for ($i = 1; $i <= $pages; $i++) {
        $q = '?page=' . $i . '&search=' . urlencode($search);
        if ($i == $page) {
          echo "<span class='px-3 py-1 bg-blue-600 text-white rounded'>$i</span>";
        } else {
          echo "<a href='$q' class='px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300'>$i</a>";
        }
      } ?>
    </div>
    <footer class="mt-6 text-center text-xs text-gray-500">
      © <?php echo date('Y'); ?> <a style="text-decoration: none; hover: underline;" href="https://github.com/ZekkCode">Github: @zekkcode</a>
    </footer>
  </div>
</body>
</html>
