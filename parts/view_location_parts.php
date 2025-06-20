<?php
require_once '../config.php';
$sql = "SELECT * FROM location_parts ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Location Parts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .form-inline-edit input,
        .form-inline-edit textarea {
            width: 100%;
            font-size: 14px;
        }
    </style>
</head>
<body class="p-3">
<div class="container">
<form method="POST" action="edit_location_parts.php" id="editForm">
  <input type="hidden" name="id" id="edit-id">
  <input type="hidden" name="nama_location_part" id="edit-nama">
  <input type="hidden" name="keterangan" id="edit-keterangan">
</form>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>No.</th>
      <th>Locations Parts Name</th>
      <th>Note</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result->num_rows > 0): ?>
      <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr data-id="<?= $row['id'] ?>">
          <td><?= $no++ ?></td>
          <td class="td-nama_location_part"><?= htmlspecialchars($row['nama_location_part']) ?></td>
          <td class="td-keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
          <td class="td-actions">
            <button type="button" class="btn btn-sm btn-warning" onclick="enableEdit(this)">Edit</button>
            <a href="delete_location_parts.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus lokasi parts bagian ini?')" class="btn btn-sm btn-danger">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<script>
function enableEdit(btn) {
  const row = btn.closest('tr');
  const id = row.dataset.id;
  const nama = row.querySelector('.td-nama_location_part').textContent;
  const ket = row.querySelector('.td-keterangan').textContent;

  row.querySelector('.td-nama_location_part').innerHTML = `<input type="text" class="form-control" value="${nama}">`;
  row.querySelector('.td-keterangan').innerHTML = `<textarea class='form-control' rows='2'>${ket}</textarea>`;

  row.querySelector('.td-actions').innerHTML = `
    <button type='button' class='btn btn-sm btn-success' onclick='saveEdit(this, ${id})'>Save</button>
    <button type='button' class='btn btn-sm btn-secondary' onclick='cancelEdit(this, \"${nama.replace(/\"/g, '&quot;')}\", \"${ket.replace(/\"/g, '&quot;')}\")'>Cancel</button>`;
}

function cancelEdit(btn, nama, ket) {
  const row = btn.closest('tr');
  row.querySelector('.td-nama_location_part').textContent = nama;
  row.querySelector('.td-keterangan').textContent = ket;

  row.querySelector('.td-actions').innerHTML = `
    <button type='button' class='btn btn-sm btn-warning' onclick='enableEdit(this)'>Edit</button>
    <a href='delete_location_parts.php?id=${row.dataset.id}' onclick='return confirm("Hapus lokasi parts bagian ini?")' class='btn btn-sm btn-danger'>Delete</a>`;
}

function saveEdit(btn, id) {
  const row = btn.closest('tr');
  const nama = row.querySelector('.td-nama_location_part input').value;
  const ket = row.querySelector('.td-keterangan textarea').value;

  document.getElementById('edit-id').value = id;
  document.getElementById('edit-nama').value = nama;
  document.getElementById('edit-keterangan').value = ket;
  document.getElementById('editForm').submit();
}
</script>

</body>
</html>

<?php 
$conn->close(); 
?>