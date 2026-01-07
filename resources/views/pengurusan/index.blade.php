@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-modern p-3 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pengurusan Paspor</h4>
        <small class="text-muted">Tampilkan data yang sudah ber-antrian dan ringkasan pendapatan</small>
      </div>

      <div class="table-responsive">
        <table class="table table-hover" id="pengurusan-table">
          <thead>
            <tr>
              <th>No Antrian</th>
              <th>No Daftar</th>
              <th>Nama Pemohon</th>
              <th>Berkas</th>
              <th>Status</th>
              <th>Keterangan</th>
              <th>Pembayaran</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="mt-3 text-end">
        <h5>Total Pendapatan: <span id="total-pendapatan">Rp 0</span></h5>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
async function loadPengurusan(){
  const res = await fetch('/pengurusan');
  const data = await res.json();
  const list = data.data ?? data;
  const tbody = document.querySelector('#pengurusan-table tbody');
  tbody.innerHTML = '';
  list.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.no_antrian}</td>
      <td>${r.no_daftar}</td>
      <td>${r.nama_pemohon}</td>
      <td>${r.berkas}</td>
      <td>${r.status}</td>
      <td>${r.keterangan}</td>
      <td>${r.pembayaran ? 'Rp ' + r.pembayaran.toLocaleString() : '-'}</td>
      <td><button class="btn btn-sm btn-danger" onclick="hapus(${r.id})">Hapus</button></td>`;
    tbody.appendChild(tr);
  });
  const total = data.total_pendapatan ?? 0;
  document.getElementById('total-pendapatan').textContent = 'Rp ' + Number(total).toLocaleString();
}

async function hapus(id){ if (!confirm('Hapus record?')) return; const res = await fetch('/pengurusan/' + id, { method: 'DELETE', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')} }); if (res.ok) loadPengurusan(); }

loadPengurusan();
</script>
@endpush
