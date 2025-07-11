<?php
require_once '../auth/session_check.php';
requireLogin();

$userRole = getUserRole();
$canEdit = hasPermission('edit') || hasPermission('update');
$canDelete = hasPermission('delete');

require_once '../config.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build search condition - only search in location part name
$searchCondition = '';
$searchParams = [];
$paramTypes = '';

if (!empty($search)) {
    $searchCondition = "WHERE nama_location_part LIKE ?";
    $searchTerm = "%$search%";
    $searchParams = [$searchTerm];
    $paramTypes = 's';
}

// Get total count with search
$countSql = "SELECT COUNT(*) AS total FROM location_parts $searchCondition";
if (!empty($searchParams)) {
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param($paramTypes, ...$searchParams);
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
    $countStmt->close();
} else {
    $totalResult = $conn->query($countSql);
}
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Get data with search and pagination
$sql = "SELECT * FROM location_parts $searchCondition ORDER BY id DESC LIMIT $limit OFFSET $offset";
if (!empty($searchParams)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($paramTypes, ...$searchParams);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Locations Parts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="view_location_parts.css"></link>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="p-4">
  <!-- Table Container -->
  <div class="table-container">
    <form method="POST" action="edit_location_parts.php" id="editForm">
      <input type="hidden" name="id" id="edit-id">
      <input type="hidden" name="nama_location_part" id="edit-nama-hidden">
      <input type="hidden" name="keterangan" id="edit-keterangan-hidden">
    </form>

    <!-- Search Section -->
    <div class="mb-3">
        <form method="GET" class="d-flex gap-2">
            <input 
                type="text" 
                name="search" 
                class="form-control" 
                placeholder="Search location parts..." 
                value="<?= htmlspecialchars($search) ?>"
                style="max-width: 300px;"
            >
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
            <?php if (!empty($search)): ?>
                <a href="view_location_parts.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </form>
        
        <?php if (!empty($search)): ?>
            <small class="text-muted mt-2 d-block">
                Showing results for: "<strong><?= htmlspecialchars($search) ?></strong>" (<?= $totalRows ?> found)
            </small>
        <?php endif; ?>
    </div>

    <table class="table table-hover">
      <thead>
        <tr>
          <th style="width: 5%">No.</th>
          <th style="width: <?= ($canEdit || $canDelete) ? '35%' : '45%' ?>">Locations Parts Name</th>
          <th style="width: <?= ($canEdit || $canDelete) ? '40%' : '50%' ?>">Note</th>
          <?php if ($canEdit || $canDelete): ?>
          <th style="width: 20%">Actions</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody id="tableBody">
        <?php if ($result->num_rows > 0): ?>
          <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['id'] ?>">
              <td><?= $no++ ?></td>
              <td class="td-nama_location_part"><?= htmlspecialchars($row['nama_location_part']) ?></td>
              <td class="td-keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
              <?php if ($canEdit || $canDelete): ?>
              <td class="td-actions">
                <?php if ($canEdit): ?>
                  <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                <?php endif; ?>
                <?php if ($canDelete): ?>
                  <a href="delete_location_parts.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus lokasi parts ini?')" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Delete
                  </a>
                <?php endif; ?>
              </td>
              <?php endif; ?>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="<?= ($canEdit || $canDelete) ? '4' : '3' ?>" class="text-center text-muted py-4">
              <?php if (!empty($search)): ?>
                No location parts found for "<?= htmlspecialchars($search) ?>"
              <?php else: ?>
                Belum ada data.
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination - Completely Outside Table Container -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
          <i class="fas fa-chevron-left"></i> Previous
        </a>
      </li>
      
      <?php
      $start = max(1, $page - 2);
      $end = min($totalPages, $page + 2);
      
      if ($start > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">1</a>
        </li>
        <?php if ($start > 2): ?>
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
        <?php endif;
      endif;
      
      for ($i = $start; $i <= $end; $i++): ?>
        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
        </li>
      <?php endfor;
      
      if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?>
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
        <?php endif; ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $totalPages ?></a>
        </li>
      <?php endif; ?>
      
      <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
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
        <?php if ($canEdit): ?>
          <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
            <i class="fas fa-edit"></i> Edit
          </button>
        <?php endif; ?>
        <?php if ($canDelete): ?>
          <a href="delete_location_parts.php?id=${row.getAttribute('data-id')}" onclick="return confirm('Hapus lokasi parts ini?')" class="btn btn-sm btn-danger">
            <i class="fas fa-trash"></i> Delete
          </a>
        <?php endif; ?>
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