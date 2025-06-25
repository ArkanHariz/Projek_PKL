<?php
require_once '../config.php';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM parts");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

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
        ORDER BY p.id DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Parts List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="view_parts.css" />
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

        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 8%">No.</th>
                    <th style="width: 20%">Parts Name</th>
                    <th style="width: 18%">Parts Locations</th>
                    <th style="width: 25%">Equipment</th>
                    <th style="width: 20%">Note</th>
                    <th style="width: 15%">Actions</th>
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
                            <td class="td-actions">
                                <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="delete_parts.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus Parts ini ?')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
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