<?php
require_once '../config.php';

$sql = "SELECT equipment.*, locations.nama_location 
        FROM equipment 
        JOIN locations ON equipment.location_id = locations.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
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
                <th>Equipment Name</th>
                <th>Equipment Location</th>
                <th>Status</th>
                <th>Note</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php $no = 1; 
                while ($row = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_equipment']) ?></td>
                        <td><?= htmlspecialchars($row['nama_location']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEquipmentModal<?= $row['id'] ?>">
                                Edit
                            </button>
                            <a href="delete_equipment.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus Equipment ini ?')" class="btn btn-sm btn-danger">
                                Delete
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editEquipmentModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content shadow rounded-3">
                                <form action="edit_equipment.php" method="POST">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="modalLabel<?= $row['id'] ?>">Edit Equipment</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>

                                    <div class="modal-body overflow-auto" style="max-height: 70vh;">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Equipment Name</label>
                                            <input type="text" name="nama_equipment" class="form-control" value="<?= htmlspecialchars($row['nama_equipment']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="location_id<?= $row['id'] ?>" class="form-label">Equipment Locations</label>
                                            <select class="form-select location-select" name="location_id" id="location_id<?= $row['id'] ?>" required data-selected="<?= $row['location_id'] ?>">
                                                <option value="">-- Choose Equipment Location --</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" id="status">
                                                <option value="">-- Choose Status</option>
                                                <option value="Aktif" <?= $row['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                                <option value="Tidak Aktif" <?= $row['status'] == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Note</label>
                                            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($row['keterangan']) ?></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Save Edit</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Belum ada data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<script>
function populateAllLocationDropdowns() {
    fetch('../locations/get_locations.php')
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.location-select').forEach(select => {
                const selectedValue = select.getAttribute('data-selected');
                select.innerHTML = '<option value="">-- Choose Equipment Locations --</option>';
                data.forEach(location => {
                    const option = document.createElement('option');
                    option.value = location.id;
                    option.textContent = location.nama_location;
                    if (selectedValue == location.id) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            });
        })
        .catch(error => {
            console.error('Error loading locations for edit:', error);
        });
}

document.addEventListener('DOMContentLoaded', populateAllLocationDropdowns);
</script>

<?php
$conn->close();
?>