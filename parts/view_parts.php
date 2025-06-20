<?php
require_once '../config.php';
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
    <meta charset="UTF-8">
    <title>Parts List</title>
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
<form method="POST" action="edit_parts.php" id="editForm">
  <input type="hidden" name="id" id="edit-id">
  <input type="hidden" name="partName" id="edit-part-name">
  <input type="hidden" name="location_part_id" id="edit-location-part-id">
  <input type="hidden" name="equipmentLocation" id="edit-equipment-id">
  <input type="hidden" name="keterangan" id="edit-keterangan">
</form>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>No.</th>
      <th>Parts Name</th>
      <th>Parts Location</th>
      <th>Equipment</th>
      <th>Note</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result->num_rows > 0): ?>
      <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr data-id="<?= $row['id'] ?>">
          <td><?= $no++ ?></td>
          <td class="td-nama_part"><?= htmlspecialchars($row['nama_part']) ?></td>
          <td class="td-location_part" data-location-id="<?= $row['location_part_id'] ?>"><?= htmlspecialchars($row['nama_location_part']) ?></td>
          <td class="td-equipment" data-equipment-id="<?= $row['equipment_id'] ?>"><?= htmlspecialchars($row['nama_equipment'].' - '.$row['nama_location']) ?></td>
          <td class="td-keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
          <td class="td-actions">
            <button type="button" class="btn btn-sm btn-warning" onclick="enableEdit(this)">Edit</button>
            <a href="delete_parts.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus Parts ini ?')" class="btn btn-sm btn-danger">Delete</a>
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
let locationParts = [];
let equipmentList = [];

fetch('get_location_parts.php').then(res => res.json()).then(data => locationParts = data);
fetch('get_equipment_with_location.php').then(res => res.json()).then(data => equipmentList = data);

function enableEdit(btn) {
  const row = btn.closest('tr');
  const id = row.dataset.id;
  const part = row.querySelector('.td-nama_part').textContent;
  const ket = row.querySelector('.td-keterangan').textContent;
  const locId = row.querySelector('.td-location_part').dataset.locationId;
  const eqId = row.querySelector('.td-equipment').dataset.equipmentId;

  row.querySelector('.td-nama_part').innerHTML = `<input type='text' class='form-control' value='${part}'>`;

  let locSel = `<select class='form-select'>`;
  locationParts.forEach(loc => {
    locSel += `<option value='${loc.id}' ${loc.id == locId ? 'selected' : ''}>${loc.nama_location_part}</option>`;
  });
  locSel += `</select>`;
  row.querySelector('.td-location_part').innerHTML = locSel;

  let eqSel = `<select class='form-select'>`;
  equipmentList.forEach(eq => {
    const label = `${eq.nama_equipment} - ${eq.nama_location}`;
    eqSel += `<option value='${eq.id}' ${eq.id == eqId ? 'selected' : ''}>${label}</option>`;
  });
  eqSel += `</select>`;
  row.querySelector('.td-equipment').innerHTML = eqSel;

  row.querySelector('.td-keterangan').innerHTML = `<textarea class='form-control'>${ket}</textarea>`;

  row.querySelector('.td-actions').innerHTML = `
    <button type='button' class='btn btn-sm btn-success' onclick='saveEdit(this, ${id})'>Save</button>
    <button type='button' class='btn btn-sm btn-secondary' onclick='window.location.reload()'>Cancel</button>`;
}

function saveEdit(btn, id) {
  const row = btn.closest('tr');
  const nama = row.querySelector('.td-nama_part input').value;
  const loc = row.querySelector('.td-location_part select').value;
  const eq = row.querySelector('.td-equipment select').value;
  const ket = row.querySelector('.td-keterangan textarea').value;

  document.getElementById('edit-id').value = id;
  document.getElementById('edit-part-name').value = nama;
  document.getElementById('edit-location-part-id').value = loc;
  document.getElementById('edit-equipment-id').value = eq;
  document.getElementById('edit-keterangan').value = ket;

  document.getElementById('editForm').submit();
}
</script>

</body>
</html>

<?php 
$conn->close(); 
?>