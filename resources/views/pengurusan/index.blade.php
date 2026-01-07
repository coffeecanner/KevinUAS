@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-modern p-3 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h4 class="mb-0">Pengurusan Paspor</h4>
          <small class="text-muted">Tampilkan data yang sudah ber-antrian dan ringkasan pendapatan</small>
        </div>
        <div class="d-flex align-items-center gap-2">
          <input id="search-peng" class="form-control form-control-sm" placeholder="Cari (nama / no antrian / no daftar / status / keterangan)..." style="width:300px" />
        </div>
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
  const res = await fetch('/api/pengurusan');
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
      <td><button class="btn btn-sm btn-warning" onclick="openPengEdit(${r.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="hapus(${r.id})">Hapus</button></td>`;
    tbody.appendChild(tr);
  });
  const total = data.total_pendapatan ?? 0;
  document.getElementById('total-pendapatan').textContent = 'Rp ' + Number(total).toLocaleString();
}

async function hapus(id){ if (!confirm('Hapus record?')) return; const res = await fetch('/api/pengurusan/' + id, { method: 'DELETE', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')} }); if (res.ok) loadPengurusan(); }

loadPengurusan();

// Edit modal (Bootstrap)
const pengModalHtml = `
<div class="modal fade" id="pengEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Edit Pengurusan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body">
        <form id="peng-edit-form">
          <input type="hidden" name="id" />
          <div class="mb-2"><label class="form-label">Nama Pemohon</label><input name="nama_pemohon" class="form-control" /></div>
          <div class="mb-2"><label class="form-label">Berkas</label><input name="berkas" class="form-control" /></div>
          <div class="mb-2"><label class="form-label">Status</label><input name="status" class="form-control" /></div>
          <div class="mb-2"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control" /></div>
          <div class="mb-2"><label class="form-label">Pembayaran</label><input name="pembayaran" type="number" class="form-control" /></div>
        </form>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button id="peng-save-btn" type="button" class="btn btn-primary">Simpan</button></div>
    </div>
  </div>
</div>`;

document.body.insertAdjacentHTML('beforeend', pengModalHtml);
const pengEditModalEl = document.getElementById('pengEditModal');
const pengBsModal = new bootstrap.Modal(pengEditModalEl);

async function openPengEdit(id){
  const res = await fetch('/api/pengurusan/' + id);
  if (!res.ok) { alert('Gagal memuat data'); return; }
  const d = await res.json();
  const f = document.getElementById('peng-edit-form');
  f.elements['id'].value = d.id;
  f.elements['nama_pemohon'].value = d.nama_pemohon || '';
  f.elements['berkas'].value = d.berkas || '';
  f.elements['status'].value = d.status || '';
  f.elements['keterangan'].value = d.keterangan || '';
  f.elements['pembayaran'].value = d.pembayaran ?? 0;
  pengBsModal.show();
}

document.getElementById('peng-save-btn').addEventListener('click', async function(){
  const f = document.getElementById('peng-edit-form');
  const id = f.elements['id'].value;
  const body = new URLSearchParams(new FormData(f));
  const res = await fetch('/api/pengurusan/' + id, { method: 'PUT', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}, body });
  if (res.ok) { pengBsModal.hide(); loadPengurusan(); } else { alert('Gagal menyimpan'); }
});
</script>

<script>
// jQuery live search for pengurusan
$(function(){
  let t = null;
  $('#search-peng').on('input', function(){
    const q = $(this).val().trim();
    clearTimeout(t);
    t = setTimeout(()=>{
      if (!q) { loadPengurusan(); return; }
  $.getJSON('/api/pengurusan/search', { q }).done(function(res){
        const data = res.data || [];
        const tbody = $('#pengurusan-table tbody').empty();
        if (!data.length) { tbody.append('<tr><td colspan="8" class="text-center small text-muted">Tidak ada hasil</td></tr>'); }
        data.forEach(r=>{
          tbody.append(`<tr><td>${r.no_antrian}</td><td>${r.no_daftar}</td><td>${r.nama_pemohon}</td><td>${r.berkas}</td><td>${r.status}</td><td>${r.keterangan}</td><td>${r.pembayaran ? 'Rp ' + Number(r.pembayaran).toLocaleString() : '-'}</td><td><button class="btn btn-sm btn-warning" onclick="openPengEdit(${r.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="hapus(${r.id})">Hapus</button></td></tr>`);
        });
        const total = res.total_pendapatan ?? 0;
        $('#total-pendapatan').text('Rp ' + Number(total).toLocaleString());
      }).fail(function(){ loadPengurusan(); });
    }, 250);
  });
});
</script>
@endpush
