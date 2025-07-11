<?php
require_once '../auth/session_check.php';
requireLogin();

$userRole = getUserRole();
$canEdit = hasPermission('edit') || hasPermission('update');
$canDelete = hasPermission('delete');

require_once '../config.php';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build search condition - only search in part name, location part name, and equipment name
$searchCondition = '';
$searchParams = [];
$paramTypes = '';

if (!empty($search)) {
    $searchCondition = "WHERE p.nama_part LIKE ? OR lp.nama_location_part LIKE ? OR e.nama_equipment LIKE ?";
    $searchTerm = "%$search%";
    $searchParams = [$searchTerm, $searchTerm, $searchTerm];
    $paramTypes = 'sss';
}

// Get total count with search
$countSql = "SELECT COUNT(*) AS total FROM parts p
             JOIN location_parts lp ON p.location_part_id = lp.id
             JOIN equipment e ON p.equipment_id = e.id
             JOIN locations l ON e.location_id = l.id
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
$sql = "SELECT 
            p.id,
            p.nama_part,
            lp.nama_location_part,
            lp.id AS location_part_id,
            e.nama_equipment,
            e.id AS equipment_id,
            l.nama_location,
            p.keterangan
        FROM parts p
        JOIN location_parts lp ON p.location_part_id = lp.id
        JOIN equipment e ON p.equipment_id = e.id
        JOIN locations l ON e.location_id = l.id
        $searchCondition
        ORDER BY p.id DESC
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
    <title>Parts List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="view_parts.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="p-4">
    <!-- Table Container -->
    <div class="table-container">
        <form method="POST" action="edit_parts.php" id="editForm">
            <input type="hidden" name="id" id="edit-id">
            <input type="hidden" name="partName" id="edit-part-name">
            <input type="hidden" name="location_part_id" id="edit-location-part-id">
            <input type="hidden" name="equipmentLocation" id="edit-equipment-id">
            <input type="hidden" name="keterangan" id="edit-keterangan">
        </form>

        <!-- Search Section -->
        <div class="mb-3">
            <form method="GET" class="d-flex gap-2">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search parts, location, or equipment..." 
                    value="<?= htmlspecialchars($search) ?>"
                    style="max-width: 350px;"
                >
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="view_parts.php" class="btn btn-outline-secondary">
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
                    <th style="width: <?= ($canEdit || $canDelete) ? '20%' : '25%' ?>">Parts Name</th>
                    <th style="width: <?= ($canEdit || $canDelete) ? '18%' : '22%' ?>">Parts Locations</th>
                    <th style="width: <?= ($canEdit || $canDelete) ? '25%' : '30%' ?>">Equipment</th>
                    <th style="width: <?= ($canEdit || $canDelete) ? '20%' : '23%' ?>">Note</th>
                    <?php if ($canEdit || $canDelete): ?>
                    <th style="width: 15%">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= $no++ ?></td>
                            <td class="td-nama_part"><?= htmlspecialchars($row['nama_part']) ?></td>
                            <td class="td-location_part" data-location-id="<?= $row['location_part_id'] ?>"><?= htmlspecialchars($row['nama_location_part']) ?></td>
                            <td class="td-equipment" data-equipment-id="<?= $row['equipment_id'] ?>"><?= htmlspecialchars($row['nama_equipment'].' - '.$row['nama_location']) ?></td>
                            <td class="td-keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
                            <?php if ($canEdit || $canDelete): ?>
                            <td class="td-actions">
                                <?php if ($canEdit): ?>
                                    <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <a href="delete_parts.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus Parts ini ?')" class="btn btn-sm btn-danger">
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
                                No parts found for "<?= htmlspecialchars($search) ?>"
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
        let locationParts = [];
        let equipmentList = [];

        // Load data for dropdowns
        fetch('get_location_parts.php').then(res => res.json()).then(data => locationParts = data);
        fetch('get_equipment_with_location.php').then(res => res.json()).then(data => equipmentList = data);

        function enableEdit(btn) {
            const row = btn.closest('tr');
            const id = row.dataset.id;
            const partName = row.querySelector('.td-nama_part').textContent;
            const keterangan = row.querySelector('.td-keterangan').textContent;
            const locationId = row.querySelector('.td-location_part').dataset.locationId;
            const equipmentId = row.querySelector('.td-equipment').dataset.equipmentId;

            // Edit part name
            row.querySelector('.td-nama_part').innerHTML = `<input type='text' class='form-control' value='${partName}'>`;

            // Edit location parts dropdown
            let locSelect = `<select class='form-select'>`;
            locationParts.forEach(loc => {
                locSelect += `<option value='${loc.id}' ${loc.id == locationId ? 'selected' : ''}>${loc.nama_location_part}</option>`;
            });
            locSelect += `</select>`;
            row.querySelector('.td-location_part').innerHTML = locSelect;

            // Edit equipment dropdown
            let eqSelect = `<select class='form-select'>`;
            equipmentList.forEach(eq => {
                const label = `${eq.nama_equipment} - ${eq.nama_location}`;
                eqSelect += `<option value='${eq.id}' ${eq.id == equipmentId ? 'selected' : ''}>${label}</option>`;
            });
            eqSelect += `</select>`;
            row.querySelector('.td-equipment').innerHTML = eqSelect;

            // Edit keterangan
            row.querySelector('.td-keterangan').innerHTML = `<textarea class='form-control' rows='2'>${keterangan}</textarea>`;

            // Change action buttons
            let actionsHTML = ``;
            actionsHTML += `<button type='button' class='btn btn-sm btn-success me-1' onclick='saveEdit(this, ${id})'><i class='fas fa-save'></i> Save</button>`;
            actionsHTML += `<button type='button' class='btn btn-sm btn-secondary' onclick='cancelEdit(this)'><i class='fas fa-times'></i> Cancel</button>`;
            row.querySelector('.td-actions').innerHTML = actionsHTML;
            
            row.classList.add('edit-mode');
        }

        function cancelEdit(btn) {
            // Reload the page to cancel edit
            window.location.reload();
        }

        function saveEdit(btn, id) {
            const row = btn.closest('tr');
            const nama = row.querySelector('.td-nama_part input').value;
            const locationId = row.querySelector('.td-location_part select').value;
            const equipmentId = row.querySelector('.td-equipment select').value;
            const keterangan = row.querySelector('.td-keterangan textarea').value;

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-part-name').value = nama;
            document.getElementById('edit-location-part-id').value = locationId;
            document.getElementById('edit-equipment-id').value = equipmentId;
            document.getElementById('edit-keterangan').value = keterangan;

            document.getElementById('editForm').submit();
        }
    </script>
</body>
</html>

<?php 
$conn->close(); 
?>