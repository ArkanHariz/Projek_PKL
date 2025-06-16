<?php
// Koneksi ke database
$host = 'localhost:3308'; // Host database
$user = 'root'; // Username database
$password = ''; // Password database
$dbname = 'cmms'; // Nama database

$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>