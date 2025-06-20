<?php
require_once '../config.php';
$sql = "SELECT equipment.*, locations.nama_location 
        FROM equipment 
        JOIN locations ON equipment.location_id = locations.id
        ORDER BY locations.nama_location DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Equipment List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .form-inline-edit input,
        .form-inline-edit select,
        .form-inline-edit textarea {
            width: 100%;
            font-size: 14px;
        }
    </style>
</head>
<body class="p-3">
<div class="container">
<form method="POST" action="edit_equipment.php" id="editForm">
  <input type="hidden" name="id" id="edit-id">
  <input type="hidden" name="nama_equipment" id="edit-nama-equipment">
  <input type="hidden" name="location_id" id="edit-location-id">
  <input type="hidden" name="status" id="edit-status">
  <input type="hidden" name="keterangan" id="edit-keterangan">
</form>

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
      <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr data-id="<?= $row['id'] ?>">
          <td><?= $no++ ?></td>
          <td class="td-nama_equipment"><?= htmlspecialchars($row['nama_equipment']) ?></td>
          <td class="td-location" data-location-id="<?= $row['location_id'] ?>"><?= htmlspecialchars($row['nama_location']) ?></td>
          <td class="td-status"><?= htmlspecialchars($row['status']) ?></td>
          <td class="td-keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
          <td class="td-actions">
            <button type="button" class="btn btn-sm btn-warning" onclick="enableEdit(this)">Edit</button>
            <a href="delete_equipment.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus Equipment ini ?')" class="btn btn-sm btn-danger">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6" class="text-center">Belum ada data.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<script>
let locationsData = [];

fetch('../locations/get_locations.php')
  .then(response => response.json())
  .then(data => { locationsData = data; })
  .catch(err => console.error('Failed to load locations:', err));

function enableEdit(btn) {
  const row = btn.closest('tr');
  const id = row.dataset.id;
  const nama = row.querySelector('.td-nama_equipment').textContent;
  const status = row.querySelector('.td-status').textContent;
  const keterangan = row.querySelector('.td-keterangan').textContent;
  const locationId = row.querySelector('.td-location').dataset.locationId;

  row.querySelector('.td-nama_equipment').innerHTML = `<input type="text" class="form-control" value="${nama}">`;

  let locSelect = `<select class='form-select'>`;
  locSelect += `<option value=''>-- Choose --</option>`;
  locationsData.forEach(loc => {
    locSelect += `<option value='${loc.id}' ${loc.id == locationId ? 'selected' : ''}>${loc.nama_location}</option>`;
  });
  locSelect += `</select>`;
  row.querySelector('.td-location').innerHTML = locSelect;

  row.querySelector('.td-status').innerHTML = `
    <select class='form-select'>
      <option value='Aktif' ${status === 'Aktif' ? 'selected' : ''}>Aktif</option>
      <option value='Tidak Aktif' ${status === 'Tidak Aktif' ? 'selected' : ''}>Tidak Aktif</option>
    </select>`;

  row.querySelector('.td-keterangan').innerHTML = `<textarea class='form-control' rows='2'>${keterangan}</textarea>`;

  row.querySelector('.td-actions').innerHTML = `
    <button type='button' class='btn btn-sm btn-success' onclick='saveEdit(this, ${id})'>Save</button>
    <button type='button' class='btn btn-sm btn-secondary' onclick='cancelEdit(this, ` +
    `\"${nama.replace(/\"/g, '&quot;')}\", \"${keterangan.replace(/\"/g, '&quot;')}\", ${locationId}, \"${status}\")'>Cancel</button>`;
}

function cancelEdit(btn, nama, ket, locationId, status) {
  const row = btn.closest('tr');
  const locationName = locationsData.find(l => l.id == locationId)?.nama_location || '-';

  row.querySelector('.td-nama_equipment').textContent = nama;
  row.querySelector('.td-keterangan').textContent = ket;
  row.querySelector('.td-location').textContent = locationName;
  row.querySelector('.td-status').textContent = status;

  row.querySelector('.td-actions').innerHTML = `
    <button type='button' class='btn btn-sm btn-warning' onclick='enableEdit(this)'>Edit</button>
    <a href='delete_equipment.php?id=${row.dataset.id}' onclick='return confirm("Hapus Equipment ini ?")' class='btn btn-sm btn-danger'>Delete</a>`;
}

function saveEdit(btn, id) {
  const row = btn.closest('tr');

  const nama = row.querySelector('.td-nama_equipment input').value;
  const locationId = row.querySelector('.td-location select').value;
  const status = row.querySelector('.td-status select').value;
  const keterangan = row.querySelector('.td-keterangan textarea').value;

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