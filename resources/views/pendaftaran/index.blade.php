@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-modern p-3 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pendaftaran</h4>
        <div class="d-flex align-items-center gap-2">
          <div class="live-search" style="width:300px">
            <input id="search-input" class="form-control form-control-sm" placeholder="Cari nama..." />
            <div id="search-results" class="live-search-results" style="display:none"></div>
          </div>
          <button class="btn btn-primary" id="btn-show-form">Tambah Pendaftaran</button>
        </div>
      </div>

      <div id="form-area" class="mb-3" style="display:none;">
        <div class="card p-3 mb-3">
          <form id="pendaftaran-form">
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Nama Pemohon</label>
                <input name="nama_pemohon" class="form-control" required />
              </div>
              <div class="col-md-4">
                <label class="form-label">Tanggal Daftar</label>
                <input type="date" name="tanggal_daftar" class="form-control" required />
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-success w-100" type="submit">Simpan</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover" id="pendaftaran-table">
          <thead>
            <tr>
              <th>No. Daftar</th>
              <th>Nama Pemohon</th>
              <th>Tgl Daftar</th>
              <th>Hari</th>
              <th>Tanggal Hadir</th>
              <th>Jam Hadir</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.getElementById('btn-show-form').addEventListener('click', ()=>{
  const fa = document.getElementById('form-area'); fa.style.display = fa.style.display === 'none' ? 'block' : 'none';
});

async function loadPendaftaran(){
  const res = await fetch('/pendaftaran');
  const data = await res.json();
  const tbody = document.querySelector('#pendaftaran-table tbody');
  tbody.innerHTML = '';
  data.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.no_daftar}</td>
      <td>${r.nama_pemohon}</td>
      <td>${r.tanggal_daftar}</td>
      <td>${r.hari}</td>
      <td>${r.tanggal_hadir}</td>
      <td>${r.jam_hadir}</td>
      <td class="table-actions">
        <button class="btn btn-sm btn-warning" onclick="edit(${r.no_daftar})">Edit</button>
        <button class="btn btn-sm btn-danger" onclick="hapus(${r.no_daftar})">Hapus</button>
      </td>`;
    tbody.appendChild(tr);
  });
}

document.getElementById('pendaftaran-form').addEventListener('submit', async function(e){
  e.preventDefault();
  const form = e.target;
  const body = new URLSearchParams(new FormData(form));
  const res = await fetch('/pendaftaran', { method: 'POST', headers: {'X-CSRF-TOKEN': csrf}, body });
  if (res.ok) { form.reset(); loadPendaftaran(); alert('Tersimpan'); }
  else { alert('Gagal menyimpan'); }
});

async function hapus(id){
  if (!confirm('Hapus data?')) return;
  const res = await fetch('/pendaftaran/' + id, { method: 'DELETE', headers: {'X-CSRF-TOKEN': csrf} });
  if (res.ok) loadPendaftaran(); else alert('Gagal menghapus');
}

function edit(id){
  alert('Untuk sementara, gunakan API atau implementasi edit sesuai kebutuhan.');
}

loadPendaftaran();
</script>

<script>
// jQuery live search for pendaftaran page
$(function(){
  let t = null;
  $('#search-input').on('input', function(){
    const q = $(this).val().trim();
    clearTimeout(t);
    if (!q) { $('#search-results').hide().empty(); loadPendaftaran(); return; }
    t = setTimeout(()=>{
      $.getJSON('/pendaftaran/search', { q }).done(function(res){
        const box = $('#search-results').empty();
        if (!res.length) { box.append('<div class="item small text-muted">Tidak ada hasil</div>').show(); return; }
        res.forEach(r=>{
          const item = $(`<div class="item"><strong>${r.nama_pemohon}</strong><div class="small text-muted">${r.no_daftar} • ${r.tanggal_hadir}</div></div>`);
          item.on('click', ()=>{
            // render single result in table
            const tbody = $('#pendaftaran-table tbody').empty();
            tbody.append(`<tr><td>${r.no_daftar}</td><td>${r.nama_pemohon}</td><td>${r.tanggal_daftar}</td><td>${r.hari}</td><td>${r.tanggal_hadir}</td><td>${r.jam_hadir}</td><td class="table-actions"><button class="btn btn-sm btn-warning">Edit</button> <button class="btn btn-sm btn-danger">Hapus</button></td></tr>`);
            box.hide();
            $('#search-input').val('');
          });
          box.append(item);
        });
        box.show();
      });
    }, 250);
  });
  $(document).on('click', function(e){ if (!$(e.target).closest('.live-search').length) $('#search-results').hide(); });
});
</script>
</script>
@endpush
