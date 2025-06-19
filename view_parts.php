<?php
require_once 'config.php';

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
        ORDER BY p.id DESC";

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
                    <td>No.</td>
                    <td>Parts Name</td>
                    <td>Parts Location</td>
                    <td>Equipment</td>
                    <td>Note</td>
                    <td>Actions</td>
                </tr>
            </thead>
            
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_part']) ?></td>
                            <td><?= htmlspecialchars($row['nama_location_part']) ?></td>
                            <td><?= htmlspecialchars($row['nama_equipment']) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPartsModal<?= $row['id'] ?>">
                                    Edit
                                </button>
                                <a href="delete_parts.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus Parts ini ?')" class="btn btn-sm btn-danger ">
                                    Delete
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editPartsModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content shadow rounded-3">
                                    <form action="edit_parts.php" method="POST">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="modalLabel<?= $row['id'] ?>">Edit Parts</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>

                                        <div class="modal-body overflow-auto" style="max-height: 70vh;">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Parts Name</label>
                                                <input type="text" name="partName" id="partName" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="location_part_id<?= $row['id'] ?>" class="form-label">Parts Location</label>
                                                <select class="form-select location-select" name="location_part_id" id="location_part_id<?= $row['id'] ?>" required data-selected="<?= $row['location_part_id'] ?>">
                                                    <option value="">-- Choose Parts Location --</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="equipmentLocation<?= $row['id'] ?>" class="form-label">Equipment - Location</label>
                                                <select class="form-select equipment-location-select" name="equipmentLocation" id="equipmentLocation<?= $row['id'] ?>" required data-selected="<?= $row['equipment_id'] ?>">
                                                    <option value="">-- Choose Equipment - Location</option>
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

<?php
$conn->close();
?>