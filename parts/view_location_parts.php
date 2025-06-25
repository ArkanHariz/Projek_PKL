<?php
require_once '../config.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM location_parts");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT * FROM location_parts ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Locations Parts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="view_location_parts.css"></link>
</head>
<body class="p-4">
  <!-- Table Container -->
  <div class="table-container">
    <form method="POST" action="edit_location_parts.php" id="editForm">
      <input type="hidden" name="id" id="edit-id">
      <input type="hidden" name="nama_location_part" id="edit-nama-hidden">
      <input type="hidden" name="keterangan" id="edit-keterangan-hidden">
    </form>

    <table class="table table-hover">
      <thead>
        <tr>
          <th style="width: 10%">No.</th>
          <th style="width: 35%">Locations Parts Name</th>
          <th style="width: 35%">Note</th>
          <th style="width: 20%">Actions</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        <?php if ($result->num_rows > 0): ?>
          <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['id'] ?>">
              <td><?= $no++ ?></td>
              <td class="td-nama_location_part"><?= htmlspecialchars($row['nama_location_part']) ?></td>
              <td class="td-keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
              <td class="td-actions">
                <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <a href="delete_location_parts.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus lokasi parts ini?')" class="btn btn-sm btn-danger">
                  <i class="fas fa-trash"></i> Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4" class="text-center text-muted">Belum ada data.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination - Completely Outside Table Container -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>">
          <i class="fas fa-chevron-left"></i> Previous
        </a>
      </li>
      
      <?php
      $start = max(1, $page - 2);
      $end = min($totalPages, $page + 2);
      
      if ($start > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=1">1</a>
        </li>
        <?php if ($start > 2): ?>
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
        <?php endif;
      endif;
      
      for ($i = $start; $i <= $end; $i++): ?>
        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor;
      
      if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?>
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
        <?php endif; ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a>
        </li>
      <?php endif; ?>
      
      <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>">
          Next <i class="fas fa-chevron-right"></i>
        </a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
  <script>
    function enableEdit(btn) {
      const row = btn.closest('tr');
      const namaCell = row.querySelector('.td-nama_location_part');
      const ketCell = row.querySelector('.td-keterangan');
      const actionsCell = row.querySelector('.td-actions');
      const id = row.getAttribute('data-id');

      const currentNama = namaCell.textContent;
      const currentKet = ketCell.textContent;

      namaCell.innerHTML = `<input type="text" class="form-control" value="${currentNama}" />`;
      ketCell.innerHTML = `<textarea class="form-control" rows="2">${currentKet}</textarea>`;
      actionsCell.innerHTML = `
        <button type="button" class="btn btn-sm btn-success me-1" onclick="saveEdit(this, '${id}')">
          <i class="fas fa-save"></i> Save
        </button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit(this, '${currentNama}', '${currentKet}')">
          <i class="fas fa-times"></i> Cancel
        </button>
      `;
      row.classList.add('edit-mode');
    }

    function cancelEdit(btn, nama, ket) {
      const row = btn.closest('tr');
      row.querySelector('.td-nama_location_part').textContent = nama;
      row.querySelector('.td-keterangan').textContent = ket;
      row.querySelector('.td-actions').innerHTML = `
        <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
          <i class="fas fa-edit"></i> Edit
        </button>
        <a href="delete_location_parts.php?id=${row.getAttribute('data-id')}" onclick="return confirm('Hapus lokasi parts ini?')" class="btn btn-sm btn-danger">
          <i class="fas fa-trash"></i> Delete
        </a>
      `;
      row.classList.remove('edit-mode');
    }

    function saveEdit(btn, id) {
      const row = btn.closest('tr');
      const newNama = row.querySelector('.td-nama_location_part input').value;
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