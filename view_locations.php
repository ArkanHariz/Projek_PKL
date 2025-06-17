<?php
require_once 'config.php';

$sql = "SELECT * FROM locations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }

        .modal .form-label {
            font-weight: 500;
        }

        .table th, .table td {
            vertical-align: middle;
        }
    </style>

</head>
<body class="p-3">

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>No.</th>
        <th>Locations Name</th>
        <th>Note</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php $no = 1; // Inisialisasi nomor urut
        while ($row = $result->fetch_assoc()): 
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_location']) ?></td>
                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                    <a href="delete_location.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus lokasi ini?')" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Tambahkan modal-lg -->
                  <div class="modal-content shadow rounded-3">
                    <form action="edit_location.php" method="POST">
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title" id="modalLabel<?= $row['id'] ?>">Edit Locations</h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body overflow-auto" style="max-height: 70vh;"> <!-- Batas tinggi agar tidak meledak -->
                          <input type="hidden" name="id" value="<?= $row['id'] ?>">
                          <div class="mb-3">
                              <label class="form-label">Locations Name</label>
                              <input type="text" name="nama_location" class="form-control" value="<?= htmlspecialchars($row['nama_location']) ?>" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Note</label>
                              <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($row['keterangan']) ?></textarea>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-success">Save Edit</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                  </div>
              </div>
            </div>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>

<?php 
$conn->close(); 
?>