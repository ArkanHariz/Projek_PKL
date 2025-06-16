<?php
// Koneksi ke database
$servername = "localhost:3308";
$username = "root"; // ganti sesuai konfigurasi lokal Anda
$password = "";     // ganti jika Anda menggunakan password
$dbname = "cmms";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT id, nama_location, keterangan FROM locations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <style>
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background-color: #f2f2f2; }
  </style>
</head>
<body>
  <table>
    <thead>
      <tr>
        <th>No.</th>
        <th>Locations Name</th>
        <th>Note</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['nama_location']) ?></td>
            <td><?= htmlspecialchars($row['keterangan']) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3" style="text-align:center;">Belum ada data.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>

<?php
$conn->close();
?>