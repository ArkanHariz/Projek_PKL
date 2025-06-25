<?php
require_once '../config.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM equipment");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT equipment.*, locations.nama_location 
        FROM equipment 
        JOIN locations ON equipment.location_id = locations.id
        ORDER BY equipment.id DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
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

        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 8%">No.</th>
                    <th style="width: 25%">Equipment Name</th>
                    <th style="width: 20%">Equipment Locations</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 20%">Note</th>
                    <th style="width: 12%">Actions</th>
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
                            <td class="td-actions">
                                <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="delete_equipment.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus Equipment ini ?')" class="btn btn-sm btn-danger">
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
        let locationsData = [];

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
    </script>
</body>
</html>

<?php 
$conn->close(); 
?>