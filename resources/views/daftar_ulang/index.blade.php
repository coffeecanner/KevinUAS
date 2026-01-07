@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-modern p-3 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Ulang</h4>
        <div class="d-flex align-items-center gap-2">
          <input id="search-du" class="form-control form-control-sm" placeholder="Cari (nama / no / hari / tanggal / status / antrian)..." style="width:260px" />
          <button class="btn btn-primary" id="btn-show-form">Tambah Daftar Ulang</button>
        </div>
      </div>

      <div id="form-area" class="mb-3" style="display:none;">
        <div class="card p-3 mb-3">
          <form id="daftar-ulang-form">
            <div class="row g-2">
              <div class="col-md-4">
                <label class="form-label">No Daftar (Pilih)</label>
                <select name="no_daftar" class="form-select" id="select-pendaftar" required></select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Nama Pemohon</label>
                <input name="nama_pemohon" id="nama-pemohon" class="form-control" required />
              </div>
              <div class="col-md-2">
                <label class="form-label">Hari Harus Datang</label>
                <input name="hari_harus_datang" class="form-control" required />
              </div>
              <div class="col-md-2">
                <label class="form-label">Tanggal Harus Datang</label>
                <input type="date" name="tanggal_harus_datang" class="form-control" required />
              </div>

              <div class="col-12 mt-2">
                <label class="form-label">Checklist Berkas</label>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="ktp" id="ktp">
                  <label class="form-check-label" for="ktp">KTP</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="kk" id="kk">
                  <label class="form-check-label" for="kk">KK</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="ijazah_akta" id="ijazah">
                  <label class="form-check-label" for="ijazah">Ijazah / Akta</label>
                </div>
              </div>

              <div class="col-12 d-flex justify-content-end mt-3">
                <button class="btn btn-success" type="submit">Simpan</button>
              </div>
            </div>
          </form>
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

@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.getElementById('btn-show-form').addEventListener('click', ()=>{
  const fa = document.getElementById('form-area'); fa.style.display = fa.style.display === 'none' ? 'block' : 'none';
});

async function loadPendaftarForSelect(){
  const res = await fetch('/api/pendaftaran');
  const data = await res.json();
  const sel = document.getElementById('select-pendaftar');
  sel.innerHTML = '<option value="">-- Pilih --</option>';
  data.forEach(d=>{ sel.innerHTML += `<option value="${d.no_daftar}" data-nama="${d.nama_pemohon}" data-hari="${d.hari}" data-tanggal="${d.tanggal_hadir}">${d.no_daftar} - ${d.nama_pemohon}</option>`; });
}

document.getElementById('select-pendaftar').addEventListener('change', function(){
  const opt = this.selectedOptions[0];
  if (!opt || !opt.value) return;
  document.getElementById('nama-pemohon').value = opt.dataset.nama || '';
  document.querySelector('input[name="hari_harus_datang"]').value = opt.dataset.hari || '';
  document.querySelector('input[name="tanggal_harus_datang"]').value = opt.dataset.tanggal || '';
});

let daftarUlangEditId = null;

async function loadDaftarUlang(){
  const res = await fetch('/api/daftar-ulang');
  const data = await res.json();
  const tbody = document.querySelector('#daftar-ulang-table tbody');
  tbody.innerHTML = '';
  data.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.no_daftar}</td>
      <td>${r.nama_pemohon}</td>
      <td>Daftar Ulang</td>
      <td>${r.ktp ? 'Ada' : 'Tidak'}</td>
      <td>${r.kk ? 'Ada' : 'Tidak'}</td>
      <td>${r.ijazah_akta ? 'Ada' : 'Tidak'}</td>
      <td>${r.keterangan}</td>
      <td>${r.no_antrian ?? ''}</td>
      <td><button class="btn btn-sm btn-warning" onclick="editDu(${r.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="hapusDu(${r.id})">Hapus</button></td>`;
    tbody.appendChild(tr);
  });
}

document.getElementById('daftar-ulang-form').addEventListener('submit', async function(e){
  e.preventDefault();
  const form = this;
  const body = new URLSearchParams(new FormData(form));
  // checkbox values to boolean
  ['ktp','kk','ijazah_akta'].forEach(k=>{ if (!body.has(k)) body.append(k,'0'); });
  const method = daftarUlangEditId ? 'PUT' : 'POST';
  const url = daftarUlangEditId ? '/api/daftar-ulang/' + daftarUlangEditId : '/api/daftar-ulang';
  const res = await fetch(url, { method, headers: {'X-CSRF-TOKEN': csrf}, body });
  if (res.ok) { form.reset(); daftarUlangEditId = null; loadDaftarUlang(); alert('Tersimpan'); } else { alert('Gagal menyimpan'); }
});

async function hapusDu(id){ if (!confirm('Hapus?')) return; const res = await fetch('/api/daftar-ulang/' + id, { method: 'DELETE', headers: {'X-CSRF-TOKEN': csrf} }); if (res.ok) loadDaftarUlang(); }

loadPendaftarForSelect(); loadDaftarUlang();

function editDu(id){
  fetch('/api/daftar-ulang/' + id).then(r=>r.json()).then(d=>{
    // set form values
    document.querySelector('select[name="no_daftar"]').value = d.no_daftar;
    document.getElementById('nama-pemohon').value = d.nama_pemohon || '';
    document.querySelector('input[name="hari_harus_datang"]').value = d.hari_harus_datang || '';
    document.querySelector('input[name="tanggal_harus_datang"]').value = d.tanggal_harus_datang || '';
    document.getElementById('ktp').checked = !!d.ktp;
    document.getElementById('kk').checked = !!d.kk;
    document.getElementById('ijazah').checked = !!d.ijazah_akta;
    document.getElementById('form-area').style.display = 'block';
    daftarUlangEditId = id;
  }).catch(()=>{ alert('Gagal memuat data'); });
}
</script>

<script>
// jQuery live search for daftar ulang
$(function(){
  let t = null;
  $('#search-du').on('input', function(){
    const q = $(this).val().trim();
    clearTimeout(t);
    t = setTimeout(()=>{
      if (!q) { loadDaftarUlang(); return; }
  $.getJSON('/api/daftar-ulang/search', { q }).done(function(res){
        const tbody = $('#daftar-ulang-table tbody').empty();
        if (!res.length) { tbody.append('<tr><td colspan="9" class="text-center small text-muted">Tidak ada hasil</td></tr>'); return; }
        res.forEach(r=>{
          tbody.append(`<tr><td>${r.no_daftar}</td><td>${r.nama_pemohon}</td><td>Daftar Ulang</td><td>${r.ktp ? 'Ada' : 'Tidak'}</td><td>${r.kk ? 'Ada' : 'Tidak'}</td><td>${r.ijazah_akta ? 'Ada' : 'Tidak'}</td><td>${r.keterangan}</td><td>${r.no_antrian ?? ''}</td><td><button class="btn btn-sm btn-danger" onclick="hapusDu(${r.id})">Hapus</button></td></tr>`);
        });
      }).fail(function(){ loadDaftarUlang(); });
    }, 250);
  });
});
</script>
@endpush
