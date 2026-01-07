@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-modern p-3 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pendaftaran</h4>
        <div class="d-flex align-items-center gap-2">
          <div style="width:300px">
            <input id="search-input" class="form-control form-control-sm" placeholder="Cari (nama / no daftar / hari / tanggal / jam / lainnya)..." />
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

let pendaftaranEditId = null;

async function loadPendaftaran(){
  const res = await fetch('/api/pendaftaran');
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
  const method = pendaftaranEditId ? 'PUT' : 'POST';
  const url = pendaftaranEditId ? '/api/pendaftaran/' + pendaftaranEditId : '/api/pendaftaran';
  const res = await fetch(url, { method, headers: {'X-CSRF-TOKEN': csrf}, body });
  if (res.ok) { form.reset(); pendaftaranEditId = null; loadPendaftaran(); alert('Tersimpan'); }
  else { alert('Gagal menyimpan'); }
});

async function hapus(id){
  if (!confirm('Hapus data?')) return;
  const res = await fetch('/api/pendaftaran/' + id, { method: 'DELETE', headers: {'X-CSRF-TOKEN': csrf} });
  if (res.ok) loadPendaftaran(); else alert('Gagal menghapus');
}

function edit(id){
  // populate form for editing
  fetch('/api/pendaftaran/' + id).then(r=>r.json()).then(d=>{
    const form = document.getElementById('pendaftaran-form');
    form.querySelector('input[name="nama_pemohon"]').value = d.nama_pemohon || '';
    // tanggal_daftar comes as d.tanggal_daftar in format d-M-Y, try to fetch raw via API show returns d.tanggal_daftar in d-M-Y; we will fetch raw from API as ISO by calling the endpoint that returns JSON original values if exists
    // The show returns formatted date; so prefer to set tanggal_daftar input using a quick fetch of stored model via /api/pendaftaran (list) and finding the item
    fetch('/api/pendaftaran').then(r=>r.json()).then(list=>{
      const item = list.find(x=>x.no_daftar == id);
      const raw = item && (item.tanggal_daftar_raw || item.tanggal_daftar);
      if (raw) {
        // if raw is ISO (Y-m-d), set directly; otherwise try to parse
        if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
          form.querySelector('input[name="tanggal_daftar"]').value = raw;
        } else {
          // fallback: try parsing formatted d-M-Y
          const parts = item.tanggal_daftar.split('-');
          if (parts.length===3) {
            const dd = parts[0]; const mm = parts[1]; const yyyy = parts[2];
            if (!isNaN(mm)) {
              form.querySelector('input[name="tanggal_daftar"]').value = `${yyyy}-${mm.padStart(2,'0')}-${dd.padStart(2,'0')}`;
            }
          }
        }
      }
    });
    document.getElementById('form-area').style.display = 'block';
    pendaftaranEditId = id;
  }).catch(()=>{ alert('Gagal mengambil data'); });
}

loadPendaftaran();
</script>

<script>
// jQuery live search for pendaftaran page: directly render into table
$(function(){
  let t = null;
  $('#search-input').on('input', function(){
    const q = $(this).val().trim();
    clearTimeout(t);
    t = setTimeout(()=>{
      if (!q) { loadPendaftaran(); return; }
  $.getJSON('/api/pendaftaran/search', { q }).done(function(res){
        const tbody = $('#pendaftaran-table tbody').empty();
        if (!res.length) { tbody.append('<tr><td colspan="7" class="text-center small text-muted">Tidak ada hasil</td></tr>'); return; }
        res.forEach(r => {
          tbody.append(`<tr><td>${r.no_daftar}</td><td>${r.nama_pemohon}</td><td>${r.tanggal_daftar}</td><td>${r.hari}</td><td>${r.tanggal_hadir}</td><td>${r.jam_hadir}</td><td class="table-actions"><button class="btn btn-sm btn-warning" onclick="edit(${r.no_daftar})">Edit</button> <button class="btn btn-sm btn-danger" onclick="hapus(${r.no_daftar})">Hapus</button></td></tr>`);
        });
      }).fail(function(){
        // on failure, fall back to full list
        loadPendaftaran();
      });
    }, 250);
  });
});
</script>
@endpush
