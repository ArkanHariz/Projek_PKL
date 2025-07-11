<?php
require_once '../auth/session_check.php';
requireLogin();

$userRole = getUserRole();
// Only admin can manage users
if ($userRole !== 'Admin') {
    header('Location: ../main.html');
    exit;
}

$canEdit = true; // Admin can edit
$canDelete = true; // Admin can delete
?>
<?php
require_once '../config.php';

function getRoleLabel($role) {
    return match($role) {
        'Admin' => 'Admin (All Role)',
        'Dispatch' => 'Dispatch (Create Only)',
        'Technician' => 'Tech (Create or Close Only)',
        'Viewer' => 'Work Order (View Only)',
        default => $role
    };
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build search condition - only search in username and email
$searchCondition = '';
$searchParams = [];
$paramTypes = '';

if (!empty($search)) {
    $searchCondition = "WHERE username LIKE ? OR email LIKE ?";
    $searchTerm = "%$search%";
    $searchParams = [$searchTerm, $searchTerm];
    $paramTypes = 'ss';
}

// Get total count with search
$countSql = "SELECT COUNT(*) AS total FROM users $searchCondition";
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
$sql = "SELECT * FROM users $searchCondition ORDER BY id DESC LIMIT $limit OFFSET $offset";
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
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="view_users.css" />
</head>
<body class="p-4">
    <!-- Table Container -->
    <div class="table-container">
        <form method="POST" action="edit_users.php" id="editForm">
            <input type="hidden" name="id" id="edit-id">
            <input type="hidden" name="username" id="edit-username">
            <input type="hidden" name="email" id="edit-email">
            <input type="hidden" name="role" id="edit-role">
        </form>

        <!-- Search Section -->
        <div class="mb-3">
            <form method="GET" class="d-flex gap-2">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search username or email..." 
                    value="<?= htmlspecialchars($search) ?>"
                    style="max-width: 300px;"
                >
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="view_users.php" class="btn btn-outline-secondary">
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
                    <th style="width: 20%">Username</th>
                    <th style="width: 30%">Email</th>
                    <th style="width: 25%">Role</th>
                    <th style="width: 17%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= $no++ ?></td>
                            <td class="td-username"><?= htmlspecialchars($row['username']) ?></td>
                            <td class="td-email"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="td-role" data-value="<?= $row['role'] ?>">
                                <span class="role-badge role-<?= strtolower($row['role']) ?>">
                                    <?= getRoleLabel($row['role']) ?>
                                </span>
                            </td>
                            <td class="td-actions">
                                <button type="button" class="btn btn-sm btn-warning me-1" onclick="enableEdit(this)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="delete_users.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus users ini ?')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <?php if (!empty($search)): ?>
                                No users found for "<?= htmlspecialchars($search) ?>"
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
            const id = row.dataset.id;
            const username = row.querySelector('.td-username').textContent;
            const email = row.querySelector('.td-email').textContent;
            const role = row.querySelector('.td-role').dataset.value;

            // Edit username
            row.querySelector('.td-username').innerHTML = `<input type='text' class='form-control' value='${username}'>`;
            
            // Edit email
            row.querySelector('.td-email').innerHTML = `<input type='email' class='form-control' value='${email}'>`;
            
            // Edit role dropdown
            row.querySelector('.td-role').innerHTML = `
                <select class='form-select'>
                    <option value='Admin' ${role === 'Admin' ? 'selected' : ''}>Admin (All Role)</option>
                    <option value='Dispatch' ${role === 'Dispatch' ? 'selected' : ''}>Dispatch (Create Only)</option>
                    <option value='Technician' ${role === 'Technician' ? 'selected' : ''}>Tech (Create or Close Only)</option>
                    <option value='Viewer' ${role === 'Viewer' ? 'selected' : ''}>Work Order (View Only)</option>
                </select>`;

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
            const username = row.querySelector('.td-username input').value;
            const email = row.querySelector('.td-email input').value;
            const role = row.querySelector('.td-role select').value;

            // Validation
            if (!username.trim() || !email.trim() || !role) {
                alert('Semua field wajib diisi!');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Format email tidak valid!');
                return;
            }

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-username').value = username;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-role').value = role;

            document.getElementById('editForm').submit();
        }
    </script>
</body>
</html>

<?php 
$conn->close(); 
?>