<?php
$servername = "localhost:3308";
$username = "root";
$password = "";
$dbname = "cmms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id = $_POST['id'];
$nama_location = $_POST['nama_location'];
$keterangan = $_POST['keterangan'];

$stmt = $conn->prepare("UPDATE locations SET nama_location = ?, keterangan = ? WHERE id = ?");
$stmt->bind_param("ssi", $nama_location, $keterangan, $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil diupdate'); window.location.href='view_locations.php';</script>";
} else {
    echo "<script>alert('Gagal update'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>