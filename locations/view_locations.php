<?php
require_once '../config.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM locations");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT * FROM locations ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Locations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
    }

    .edit-mode input,
    .edit-mode textarea {
      width: 100%;
      font-size: 14px;
    }

    .container {
      max-width: 100%;
      overflow: visible !important;
    }

    table {
      table-layout: auto;
      width: 100%;
    }
  </style>
</head>
<body class="p-4">
  <div class="container">
    <form method="POST" action="edit_location.php" id="editForm">
      <input type="hidden" name="id" id="edit-id">
      <input type="hidden" name="nama_location" id="edit-nama-hidden">
      <input type="hidden" name="keterangan" id="edit-keterangan-hidden">
    </form>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>No.</th>
          <th>Location Name</th>
          <th>Note</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        <?php if ($result->num_rows > 0): ?>
          <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['id'] ?>">
              <td><?= $no++ ?></td>
              <td class="td-nama"><?= htmlspecialchars($row['nama_location']) ?></td>
              <td class="td-keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
              <td class="td-actions">
                <button type="button" class="btn btn-sm btn-warning" onclick="enableEdit(this)">Edit</button>
                <a href="delete_location.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus lokasi ini?')" class="btn btn-sm btn-danger">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <nav>
      <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $page == $i ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
        </li>
      </ul>
    </nav>
  </div>

  <script>
    function enableEdit(btn) {
      const row = btn.closest('tr');
      const namaCell = row.querySelector('.td-nama');
      const ketCell = row.querySelector('.td-keterangan');
      const actionsCell = row.querySelector('.td-actions');
      const id = row.getAttribute('data-id');

      const currentNama = namaCell.textContent;
      const currentKet = ketCell.textContent;

      namaCell.innerHTML = `<input type="text" class="form-control" value="${currentNama}" />`;
      ketCell.innerHTML = `<textarea class="form-control" rows="2">${currentKet}</textarea>`;
      actionsCell.innerHTML = `
        <button type="button" class="btn btn-sm btn-success" onclick="saveEdit(this, '${id}')">Save</button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit(this, '${currentNama}', '${currentKet}')">Cancel</button>
      `;
      row.classList.add('edit-mode');
    }

    function cancelEdit(btn, nama, ket) {
      const row = btn.closest('tr');
      row.querySelector('.td-nama').textContent = nama;
      row.querySelector('.td-keterangan').textContent = ket;
      row.querySelector('.td-actions').innerHTML = `
        <button type="button" class="btn btn-sm btn-warning" onclick="enableEdit(this)">Edit</button>
        <a href="delete_location.php?id=${row.getAttribute('data-id')}" onclick="return confirm('Hapus lokasi ini?')" class="btn btn-sm btn-danger">Delete</a>
      `;
      row.classList.remove('edit-mode');
    }

    function saveEdit(btn, id) {
      const row = btn.closest('tr');
      const newNama = row.querySelector('.td-nama input').value;
      const newKet = row.querySelector('.td-keterangan textarea').value;

      document.getElementById('edit-id').value = id;
      document.getElementById('edit-nama-hidden').value = newNama;
      document.getElementById('edit-keterangan-hidden').value = newKet;

      document.getElementById('editForm').submit();
    }
  </script>
</body>
</html>

<?php 
$conn->close(); 
?>