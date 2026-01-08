@extends('layouts.app')

@section('content')
<div class="row">

  {{-- ================= ANTRIAN AKTIF ================= --}}
  <div class="col-12 col-md-4">
    <div class="card text-center shadow-sm mb-4">
      <div class="card-body">
        <h6 class="text-muted mb-2">SEDANG DIPANGGIL</h6>

        <div id="current-antrian">
          <h1 class="display-3 fw-bold text-primary">-</h1>
          <p class="mb-1 fw-semibold">-</p>
          <small class="text-muted">Menunggu antrian...</small>
        </div>

        <button id="btn-selesai-current"
                class="btn btn-success mt-3 d-none"
                onclick="selesaikanCurrent()">
          Selesaikan & Panggil Berikutnya
        </button>
      </div>
    </div>
  </div>

  {{-- ================= TABEL ================= --}}
  <div class="col-12 col-md-8">
    <div class="card card-modern p-3 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h4 class="mb-0">Pengurusan Paspor</h4>
          <small class="text-muted">Daftar antrian & pendapatan</small>
        </div>
        <input id="search-peng"
               class="form-control form-control-sm"
               placeholder="Cari (nama / no antrian / status)..."
               style="width:280px" />
      </div>

      <div class="table-responsive">
        <table class="table table-hover" id="pengurusan-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Status</th>
              <th>Pembayaran</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="mt-3 text-end">
        <h6>Total Pendapatan:
          <span id="total-pendapatan">Rp 0</span>
        </h6>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
let currentAntrianId = null;

/* ================== LOAD CURRENT ================== */
async function loadCurrentAntrian(){
  const res = await fetch('/api/pengurusan/current', {
    headers: { 'Accept': 'application/json' }
  });

  const data = await res.json();

  const box = document.getElementById('current-antrian');
  const btn = document.getElementById('btn-selesai-current');

  // 🔥 INI KUNCI NYA
  if (!data || !data.id) {
    box.innerHTML = `
      <h1 class="display-3 fw-bold text-muted">-</h1>
      <p class="mb-1 fw-semibold">Tidak ada antrian</p>
      <small class="text-muted">Silakan menunggu</small>
    `;
    btn.classList.add('d-none');
    currentAntrianId = null;
    return;
  }

  // ================= NORMAL =================
  currentAntrianId = data.id;

  box.innerHTML = `
    <h1 class="display-3 fw-bold text-primary">${data.no_antrian}</h1>
    <p class="mb-1 fw-semibold">${data.nama_pemohon}</p>
    <small class="text-muted">Silakan menuju meja pengurusan</small>
  `;

  btn.classList.remove('d-none');
}


/* ================== SELESAIKAN ================== */
async function selesaikanCurrent(){
  if (!currentAntrianId) return;

  if (!confirm('Selesaikan antrian ini?')) return;

  const res = await fetch(
    `/api/pengurusan/${currentAntrianId}/selesai-next`,
    {
      method: 'PUT',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    }
  );

  if (res.ok) {
    await loadCurrentAntrian();
    await loadPengurusan();
  }
}

/* ================== LOAD TABLE ================== */
async function loadPengurusan(){
  const res = await fetch('/api/pengurusan');
  const data = await res.json();
  const tbody = document.querySelector('#pengurusan-table tbody');

  tbody.innerHTML = '';
  data.data.forEach(r=>{
    tbody.innerHTML += `
      <tr>
        <td>${r.no_antrian}</td>
        <td>${r.nama_pemohon}</td>
        <td>${r.status}</td>
        <td>${r.pembayaran
          ? 'Rp ' + r.pembayaran.toLocaleString()
          : '-'}
        </td>
      </tr>`;
  });

  document.getElementById('total-pendapatan').textContent =
    'Rp ' + Number(data.total_pendapatan).toLocaleString();
}

/* ================== INIT ================== */
loadCurrentAntrian();
loadPengurusan();

// auto refresh tiap 10 detik (opsional, buat monitor)
setInterval(loadCurrentAntrian, 10000);
</script>
@endpush
