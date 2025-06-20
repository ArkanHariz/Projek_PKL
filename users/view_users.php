<?php
require_once '../config.php';
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body class="p-3">
<div class="container">
<form method="POST" action="edit_users.php" id="editForm">
  <input type="hidden" name="id" id="edit-id">
  <input type="hidden" name="username" id="edit-username">
  <input type="hidden" name="email" id="edit-email">
  <input type="hidden" name="role" id="edit-role">
</form>

<table class="table table-bordered table-striped">
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
        <tr data-id="<?= $row['id'] ?>">
          <td><?= $no++ ?></td>
          <td class="td-username"><?= htmlspecialchars($row['username']) ?></td>
          <td class="td-email"><?= htmlspecialchars($row['email']) ?></td>
          <td class="td-role"><?= htmlspecialchars($row['role']) ?></td>
          <td class="td-actions">
            <button type="button" class="btn btn-sm btn-warning" onclick="enableEdit(this)">Edit</button>
            <a href="delete_users.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus users ini ?')" class="btn btn-sm btn-danger">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5" class="text-center">Belum ada data.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<script>
function enableEdit(btn) {
  const row = btn.closest('tr');
  const id = row.dataset.id;
  const username = row.querySelector('.td-username').textContent;
  const email = row.querySelector('.td-email').textContent;
  const role = row.querySelector('.td-role').textContent;

  row.querySelector('.td-username').innerHTML = `<input type='text' class='form-control' value='${username}'>`;
  row.querySelector('.td-email').innerHTML = `<input type='email' class='form-control' value='${email}'>`;
  row.querySelector('.td-role').innerHTML = `
    <select class='form-select'>
      <option value='Admin' ${role === 'Admin' ? 'selected' : ''}>Admin (All Role)</option>
      <option value='Dispatch' ${role === 'Dispatch' ? 'selected' : ''}>Dispatch (Create Only)</option>
      <option value='Tech' ${role === 'Tech' ? 'selected' : ''}>Tech (Create or Close Only)</option>
      <option value='Work Order' ${role === 'Work Order' ? 'selected' : ''}>Work Order (View Only)</option>
    </select>`;

  row.querySelector('.td-actions').innerHTML = `
    <button type='button' class='btn btn-sm btn-success' onclick='saveEdit(this, ${id})'>Save</button>
    <button type='button' class='btn btn-sm btn-secondary' onclick='window.location.reload()'>Cancel</button>`;
}

function saveEdit(btn, id) {
  const row = btn.closest('tr');
  const username = row.querySelector('.td-username input').value;
  const email = row.querySelector('.td-email input').value;
  const role = row.querySelector('.td-role select').value;

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