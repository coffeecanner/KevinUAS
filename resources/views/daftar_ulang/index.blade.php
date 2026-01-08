@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-modern p-3 mb-4">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Ulang</h4>
        <div class="d-flex align-items-center gap-2">
          <input id="search-du" class="form-control form-control-sm"
                 placeholder="Cari (nama / no / hari / tanggal / status / antrian)..."
                 style="width:260px" />
          <button class="btn btn-primary" id="btn-show-form">
            Tambah Daftar Ulang
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover" id="daftar-ulang-table">
          <thead>
            <tr>
              <th>No Daftar</th>
              <th>Nama Pemohon</th>
              <th>Keperluan</th>
              <th>KTP</th>
              <th>KK</th>
              <th>Ijazah/Akta</th>
              <th>Keterangan</th>
              <th>No Antrian</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="daftarUlangModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Tambah Daftar Ulang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="daftar-ulang-form">
          <div class="row g-2">

            <div class="col-md-4">
              <label class="form-label">No Daftar</label>
              <select name="no_daftar" class="form-select" id="select-pendaftar" required></select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Nama Pemohon</label>
              <input name="nama_pemohon" id="nama-pemohon" class="form-control" required />
            </div>

            <div class="col-md-2">
              <label class="form-label">Hari Harus Datang</label>
              <!-- 🔒 READONLY -->
              <input name="hari_harus_datang" class="form-control" readonly />
            </div>

            <div class="col-md-2">
              <label class="form-label">Tanggal Harus Datang</label>
              <input type="date" name="tanggal_harus_datang" class="form-control" required />
            </div>

            <div class="col-12 mt-2">
              <label class="form-label">Checklist Berkas</label>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ktp" id="ktp">
                <label class="form-check-label">KTP</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="kk" id="kk">
                <label class="form-check-label">KK</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ijazah_akta" id="ijazah">
                <label class="form-check-label">Ijazah / Akta</label>
              </div>
            </div>

          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-success" id="btn-submit-du">Simpan</button>
      </div>

    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const duModal = new bootstrap.Modal(document.getElementById('daftarUlangModal'));
let daftarUlangEditId = null;

/* ================= HELPER HARI ================= */
function getHariIndonesia(dateStr) {
  if (!dateStr) return '';
  const hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const d = new Date(dateStr);
  return hari[d.getDay()];
}

/* ================= SINKRON TANGGAL -> HARI ================= */
document.querySelector('[name=tanggal_harus_datang]')
  .addEventListener('change', function () {
    document.querySelector('[name=hari_harus_datang]').value =
      getHariIndonesia(this.value);
  });

/* ================= OPEN MODAL ================= */
document.getElementById('btn-show-form').addEventListener('click', () => {
  daftarUlangEditId = null;
  document.getElementById('daftar-ulang-form').reset();
  document.querySelector('#daftarUlangModal .modal-title').innerText = 'Tambah Daftar Ulang';
  duModal.show();
});

/* ================= LOAD PENDAFTAR ================= */
async function loadPendaftarForSelect(){
  const res = await fetch('/api/pendaftaran');
  const data = await res.json();
  const sel = document.getElementById('select-pendaftar');
  sel.innerHTML = '<option value="">-- Pilih --</option>';
  data.forEach(d=>{
    sel.innerHTML += `
      <option value="${d.no_daftar}"
        data-nama="${d.nama_pemohon}"
        data-tanggal="${d.tanggal_hadir}">
        ${d.no_daftar} - ${d.nama_pemohon}
      </option>`;
  });
}

document.getElementById('select-pendaftar').addEventListener('change', function(){
  const opt = this.selectedOptions[0];
  if (!opt) return;
  document.getElementById('nama-pemohon').value = opt.dataset.nama || '';
  document.querySelector('[name=tanggal_harus_datang]').value = opt.dataset.tanggal || '';
  document.querySelector('[name=tanggal_harus_datang]')
    .dispatchEvent(new Event('change')); // 🔥 PAKSA SINKRON
});

/* ================= SUBMIT ================= */
document.getElementById('btn-submit-du').addEventListener('click', () => {
  document.getElementById('daftar-ulang-form').requestSubmit();
});

document.getElementById('daftar-ulang-form').addEventListener('submit', async function(e){
  e.preventDefault();

  const body = new URLSearchParams(new FormData(this));
  ['ktp','kk','ijazah_akta'].forEach(k=>{
    body.set(k, body.get(k) ? '1' : '0');
  });

  const method = daftarUlangEditId ? 'PUT' : 'POST';
  const url = daftarUlangEditId
    ? '/api/daftar-ulang/' + daftarUlangEditId
    : '/api/daftar-ulang';

  const res = await fetch(url, {
    method,
    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    body
  });

  if (res.ok) {
    duModal.hide();
    this.reset();
    daftarUlangEditId = null;
    loadDaftarUlang();
    alert('Tersimpan');
  } else {
    console.error(await res.text());
    alert('Gagal menyimpan');
  }
});

/* ================= TABLE ================= */
async function loadDaftarUlang(){
  const res = await fetch('/api/daftar-ulang');
  const data = await res.json();
  const tbody = document.querySelector('#daftar-ulang-table tbody');
  tbody.innerHTML = '';
  data.forEach(r=>{
    tbody.innerHTML += `
      <tr>
        <td>${r.no_daftar}</td>
        <td>${r.nama_pemohon}</td>
        <td>Daftar Ulang</td>
        <td>${r.ktp ? 'Ada' : 'Tidak'}</td>
        <td>${r.kk ? 'Ada' : 'Tidak'}</td>
        <td>${r.ijazah_akta ? 'Ada' : 'Tidak'}</td>
        <td>${r.keterangan}</td>
        <td>${r.no_antrian ?? ''}</td>
        <td>
            ${r.keterangan === 'OK' && r.no_antrian && !r.processed
                ? `<button class="btn btn-sm btn-primary me-1"
                    onclick="prosesPeng(${r.id}, this)">
                    Proses
                </button>`
                : ''
            }

            ${r.processed
                ? `<span class="badge bg-success me-1">Diproses</span>`
                : ''
            }

            <button class="btn btn-sm btn-warning" onclick="editDu(${r.id})">Edit</button>
            <button class="btn btn-sm btn-danger" onclick="hapusDu(${r.id})">Hapus</button>
        </td>
      </tr>`;
  });
}

function editDu(id){
  fetch('/api/daftar-ulang/' + id).then(r=>r.json()).then(d=>{
    daftarUlangEditId = id;
    document.querySelector('#daftarUlangModal .modal-title').innerText = 'Edit Daftar Ulang';
    document.querySelector('[name=no_daftar]').value = d.no_daftar;
    document.getElementById('nama-pemohon').value = d.nama_pemohon;
    document.querySelector('[name=tanggal_harus_datang]').value = d.tanggal_harus_datang;
    document.querySelector('[name=tanggal_harus_datang]')
      .dispatchEvent(new Event('change')); // 🔥 SINKRON ULANG
    document.getElementById('ktp').checked = !!d.ktp;
    document.getElementById('kk').checked = !!d.kk;
    document.getElementById('ijazah').checked = !!d.ijazah_akta;
    duModal.show();
  });
}
async function prosesPeng(duId, btn){
  if (!confirm('Proses entry ini ke Pengurusan?')) return;

  btn.disabled = true;
  btn.innerHTML = '...';

  const res = await fetch('/api/pengurusan', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Accept': 'application/json'
    },
    body: new URLSearchParams({ daftar_ulang_id: duId })
  });

  if (res.ok) {
    alert('Pengurusan dibuat');
    loadDaftarUlang();
    if (typeof loadPengurusan === 'function') loadPengurusan();
  } else {
    alert('Gagal proses');
    btn.disabled = false;
    btn.innerHTML = 'Proses';
  }
}

async function hapusDu(id){
  if (!confirm('Hapus data?')) return;
  const res = await fetch('/api/daftar-ulang/' + id, {
    method: 'DELETE',
    headers: {'X-CSRF-TOKEN': csrf}
  });
  if (res.ok) loadDaftarUlang();
}

loadPendaftarForSelect();
loadDaftarUlang();
</script>
@endpush
