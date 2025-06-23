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
      background: #f8f9fa;
    }

    .edit-mode input,
    .edit-mode textarea {
      width: 100%;
      font-size: 14px;
    }

    .table-container {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
    }

    .pagination-container {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: center;
    }

    table {
      table-layout: auto;
      width: 100%;
    }

    .table th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      font-weight: 600;
    }

    .table td {
      vertical-align: middle;
      border-color: #e9ecef;
    }

    .btn {
      border-radius: 8px;
      font-weight: 500;
    }

    .pagination .page-link {
      border-radius: 8px;
      margin: 0 2px;
      border: 1px solid #dee2e6;
      color: #667eea;
    }

    .pagination .page-item.active .page-link {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-color: #667eea;
    }

    .pagination .page-link:hover {
      background-color: #f8f9fa;
      border-color: #667eea;
    }
  </style>
</head>
<body class="p-4">
  <div class="container-fluid">
    <!-- Table Container -->
    <div class="table-container">
      <form method="POST" action="edit_location.php" id="editForm">
        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="nama_location" id="edit-nama-hidden">
        <input type="hidden" name="keterangan" id="edit-keterangan-hidden">
      </form>

      <table class="table table-hover">
        <thead>
          <tr>
            <th style="width: 10%">No.</th>
            <th style="width: 30%">Location Name</th>
            <th style="width: 40%">Note</th>
            <th style="width: 20%">Actions</th>
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
                  <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <a href="delete_location.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus lokasi ini?')" class="btn btn-sm btn-danger">
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

    <!-- Pagination Container (Outside the table container) -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-container">
      <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
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
    </div>
    <?php endif; ?>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
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
      row.querySelector('.td-nama').textContent = nama;
      row.querySelector('.td-keterangan').textContent = ket;
      row.querySelector('.td-actions').innerHTML = `
        <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
          <i class="fas fa-edit"></i> Edit
        </button>
        <a href="delete_location.php?id=${row.getAttribute('data-id')}" onclick="return confirm('Hapus lokasi ini?')" class="btn btn-sm btn-danger">
          <i class="fas fa-trash"></i> Delete
        </a>
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