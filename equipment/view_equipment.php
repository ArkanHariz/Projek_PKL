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

// Build search condition - only search in equipment name and location name
$searchCondition = '';
$searchParams = [];
$paramTypes = '';

if (!empty($search)) {
    $searchCondition = "WHERE equipment.nama_equipment LIKE ? OR locations.nama_location LIKE ?";
    $searchTerm = "%$search%";
    $searchParams = [$searchTerm, $searchTerm];
    $paramTypes = 'ss';
}

// Get total count with search
$countSql = "SELECT COUNT(*) AS total FROM equipment 
             JOIN locations ON equipment.location_id = locations.id 
             $searchCondition";
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
$sql = "SELECT equipment.*, locations.nama_location 
        FROM equipment 
        JOIN locations ON equipment.location_id = locations.id
        $searchCondition
        ORDER BY equipment.id DESC
        LIMIT $limit OFFSET $offset";
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
    <title>Equipment List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="view_equipment.css" />
</head>
<body class="p-4">
    <!-- Table Container -->
    <div class="table-container">
        <form method="POST" action="edit_equipment.php" id="editForm">
            <input type="hidden" name="id" id="edit-id">
            <input type="hidden" name="nama_equipment" id="edit-nama-equipment">
            <input type="hidden" name="location_id" id="edit-location-id">
            <input type="hidden" name="status" id="edit-status">
            <input type="hidden" name="keterangan" id="edit-keterangan">
        </form>

        <!-- Search Section -->
        <div class="mb-3">
            <form method="GET" class="d-flex gap-2">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search equipment name or location..." 
                    value="<?= htmlspecialchars($search) ?>"
                    style="max-width: 350px;"
                >
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="view_equipment.php" class="btn btn-outline-secondary">
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
                    <th style="width: 8%">No.</th>
                    <th style="width: <?= ($canEdit || $canDelete) ? '25%' : '30%' ?>">Equipment Name</th>
                    <th style="width: <?= ($canEdit || $canDelete) ? '20%' : '25%' ?>">Equipment Locations</th>
                    <th style="width: <?= ($canEdit || $canDelete) ? '15%' : '18%' ?>">Status</th>
                    <th style="width: <?= ($canEdit || $canDelete) ? '20%' : '27%' ?>">Note</th>
                    <?php if ($canEdit || $canDelete): ?>
                    <th style="width: 12%">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= $no++ ?></td>
                            <td class="td-nama_equipment"><?= htmlspecialchars($row['nama_equipment']) ?></td>
                            <td class="td-location" data-location-id="<?= $row['location_id'] ?>"><?= htmlspecialchars($row['nama_location']) ?></td>
                            <td class="td-status">
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="td-keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
                            <?php if ($canEdit || $canDelete): ?>
                            <td class="td-actions">
                                <?php if ($canEdit): ?>
                                    <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <a href="delete_equipment.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus Equipment ini ?')" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= ($canEdit || $canDelete) ? '6' : '5' ?>" class="text-center text-muted py-4">
                            <?php if (!empty($search)): ?>
                                No equipment found for "<?= htmlspecialchars($search) ?>"
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
        let locationsData = [];
        let canEdit = <?php echo $canEdit ? 'true' : 'false'; ?>;
        let canDelete = <?php echo $canDelete ? 'true' : 'false'; ?>;

        // Load locations data for dropdown
        fetch('../locations/get_locations.php')
            .then(response => response.json())
            .then(data => { locationsData = data; })
            .catch(err => console.error('Failed to load locations:', err));

        function enableEdit(btn) {
            const row = btn.closest('tr');
            const id = row.dataset.id;
            const nama = row.querySelector('.td-nama_equipment').textContent;
            const status = row.querySelector('.td-status span').textContent.trim();
            const keterangan = row.querySelector('.td-keterangan').textContent;
            const locationId = row.querySelector('.td-location').dataset.locationId;

            // Edit equipment name
            row.querySelector('.td-nama_equipment').innerHTML = `<input type="text" class="form-control" value="${nama}">`;

            // Edit location dropdown
            let locSelect = `<select class='form-select'>`;
            locSelect += `<option value=''>-- Choose --</option>`;
            locationsData.forEach(loc => {
                locSelect += `<option value='${loc.id}' ${loc.id == locationId ? 'selected' : ''}>${loc.nama_location}</option>`;
            });
            locSelect += `</select>`;
            row.querySelector('.td-location').innerHTML = locSelect;

            // Edit status dropdown
            row.querySelector('.td-status').innerHTML = `
                <select class='form-select'>
                    <option value='Aktif' ${status === 'Aktif' ? 'selected' : ''}>Aktif</option>
                    <option value='Tidak Aktif' ${status === 'Tidak Aktif' ? 'selected' : ''}>Tidak Aktif</option>
                </select>`;

            // Edit keterangan
            row.querySelector('.td-keterangan').innerHTML = `<textarea class='form-control' rows='2'>${keterangan}</textarea>`;

            // Change action buttons
            row.querySelector('.td-actions').innerHTML = `
                <button type='button' class='btn btn-sm btn-success me-1' onclick='saveEdit(this, ${id})'>
                    <i class='fas fa-save'></i> Save
                </button>
                <button type='button' class='btn btn-sm btn-secondary' onclick='cancelEdit(this)'>
                    <i class='fas fa-times'></i> Cancel
                </button>`;
            
            row.classList.add('edit-mode');
        }

        function cancelEdit(btn) {
            // Reload the page to cancel edit
            window.location.reload();
        }

        function saveEdit(btn, id) {
            const row = btn.closest('tr');
            const nama = row.querySelector('.td-nama_equipment input').value;
            const locationId = row.querySelector('.td-location select').value;
            const status = row.querySelector('.td-status select').value;
            const keterangan = row.querySelector('.td-keterangan textarea').value;

            // Validation
            if (!nama.trim() || !locationId || !status) {
                alert('Nama equipment, lokasi, dan status wajib diisi!');
                return;
            }

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nama-equipment').value = nama;
            document.getElementById('edit-location-id').value = locationId;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-keterangan').value = keterangan;

            document.getElementById('editForm').submit();
        }

        // Disable edit functionality if user doesn't have edit permissions
        if (!canEdit) {
            function disableEditButtons() {
                const editButtons = document.querySelectorAll('.btn-warning');
                editButtons.forEach(button => {
                    button.disabled = true;
                    button.classList.remove('btn-warning');
                    button.classList.add('btn-secondary');
                });
            }

            // Call the function to disable edit buttons after the page loads
            window.addEventListener('load', disableEditButtons);
        }

        // Disable delete functionality if user doesn't have delete permissions
        if (!canDelete) {
            function disableDeleteButtons() {
                const deleteButtons = document.querySelectorAll('.btn-danger');
                deleteButtons.forEach(button => {
                    button.style.display = 'none';
                });
            }

            // Call the function to disable delete buttons after the page loads
            window.addEventListener('load', disableDeleteButtons);
        }
    </script>
</body>
</html>

<?php 
$conn->close(); 
?>