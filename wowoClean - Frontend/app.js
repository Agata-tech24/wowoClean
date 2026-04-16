const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

let localContainers = [];

async function loadContainers() {
  try {
    const res = await api.get('/containers');
    localContainers = res.data;
    renderContainers(localContainers);
  } catch (err) {
    document.getElementById('container-list').innerHTML = '<p style="color:red">Gagal memuat data.</p>';
  }
}

function renderContainers(containers) {
  const total = containers.reduce((sum, c) => sum + c.weight_kg, 0);
  document.getElementById('total-muatan').textContent = `Total Muatan: ${total} kg`;

  const list = document.getElementById('container-list');
  if (containers.length === 0) {
    list.innerHTML = '<p>Belum ada data kontainer.</p>';
    return;
  }

  list.innerHTML = containers.map(c => `
    <div class="card ${c.status === 'Archived' ? 'archived' : ''}">
      <h3>${c.container_id}</h3>
      <span class="badge ${c.status === 'Active' ? 'active' : 'archived'}">${c.status}</span>
      <p><strong>Tipe:</strong> ${c.waste_type}</p>
      <p><strong>Berat:</strong> ${c.weight_kg} kg</p>
      <div class="card-actions">
        ${c.status === 'Active' ? `<button class="btn-archive" onclick="archiveContainer('${c.container_id}')">Archive</button>` : ''}
        <button class="btn-delete" onclick="deleteContainer('${c.container_id}')">Hapus</button>
      </div>
    </div>
  `).join('');
}

async function createContainer() {
  document.getElementById('err-id').style.display = 'none';
  document.getElementById('err-weight').style.display = 'none';

  const data = {
    container_id: document.getElementById('input-id').value,
    waste_type:   document.getElementById('input-type').value,
    weight_kg:    parseFloat(document.getElementById('input-weight').value)
  };

  try {
    const res = await api.post('/containers', data);
    localContainers.push(res.data);
    renderContainers(localContainers);
    alert('Kontainer berhasil ditambahkan!');
  } catch (err) {
    if (err.response && err.response.status === 422) {
      const errors = err.response.data.errors;
      if (errors.container_id) {
        const el = document.getElementById('err-id');
        el.textContent = errors.container_id[0];
        el.style.display = 'block';
      }
      if (errors.weight_kg) {
        const el = document.getElementById('err-weight');
        el.textContent = errors.weight_kg[0];
        el.style.display = 'block';
      }
    }
  }
}

async function archiveContainer(id) {
  try {
    await api.patch(`/containers/${id}`, { status: 'Archived' });
    const c = localContainers.find(c => c.container_id === id);
    if (c) c.status = 'Archived';
    renderContainers(localContainers);
  } catch (err) {
    alert('Gagal mengarsipkan kontainer.');
  }
}

async function deleteContainer(id) {
  if (!confirm(`Yakin ingin menghapus kontainer ${id}?`)) return;
  try {
    await api.delete(`/containers/${id}`);
    localContainers = localContainers.filter(c => c.container_id !== id);
    renderContainers(localContainers);
  } catch (err) {
    alert('Gagal menghapus kontainer.');
  }
}

loadContainers();