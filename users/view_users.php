<?php
require_once '../config.php';

$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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
        <table class="table table-bordered tabl-striped">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUsersModal<?= $row['id'] ?>">Edit</button>
                                <a href="delete_users.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus users ini ?')" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Belum ada data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </body>
</html>

<?php
$conn->close();
?>