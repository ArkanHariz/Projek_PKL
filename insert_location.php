<?php
$servername = "localhost:3308";
$username = "root";
$password = "";
$dbname = "cmms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$nama_location = $_POST['nama_location'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_location)) {
    $stmt = $conn->prepare("INSERT INTO locations (nama_location, keterangan) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_location, $keterangan);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Data berhasil disimpan!'); window.location.href='main.html';</script>";
} else {
    echo "<script>alert('Nama lokasi wajib diisi.'); window.history.back();</script>";
}

$conn->close();
?>