@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Overview - Dashboard Imigrasi</h3>
      <div class="d-flex align-items-center gap-3">
        <div class="live-search" style="width:320px">
          <input id="search-input" class="form-control form-control-sm" placeholder="Cari pendaftar (ketik nama)..." />
          <div id="search-results" class="live-search-results" style="display:none"></div>
        </div>
        <div class="text-end text-muted small">{{ now()->format('d M Y') }}</div>
      </div>
    </div>



    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <div class="card card-modern p-3">
          <small class="text-muted">Total Pendaftar</small>
          <h4 class="mt-2">{{ number_format($totalPendaftar) }}</h4>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-modern p-3">
          <small class="text-muted">Total Daftar Ulang</small>
          <h4 class="mt-2">{{ number_format($totalDaftarUlang) }}</h4>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-modern p-3">
          <small class="text-muted">Total Pengurusan</small>
          <h4 class="mt-2">{{ number_format($totalPengurusan) }}</h4>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-modern p-3">
          <small class="text-muted">Total Pendapatan</small>
          <h4 class="mt-2">Rp {{ number_format($totalPendapatan,0,',','.') }}</h4>
        </div>
      </div>
    </div>

    <div class="card card-modern p-3 mb-3">
      <div class="row">
        <div class="col-lg-8">
          <h5>Jadwal Pendaftar (7 hari ke depan)</h5>
          <canvas id="scheduleChart" height="120"></canvas>
        </div>
        <div class="col-lg-4">
          <h5>Status Pengurusan</h5>
          <canvas id="statusChart" height="200"></canvas>
        </div>
      </div>
    </div>

    <div class="card card-modern p-3">
      <h5>Recent Pendaftar</h5>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr><th>No</th><th>Nama</th><th>Tgl Daftar</th><th>Tgl Hadir</th><th>Jam</th></tr>
          </thead>
          <tbody id="recent-body"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = {!! json_encode($labels) !!};
const values = {!! json_encode($values) !!};
const accepted = {{ $accepted }};
const rejected = {{ $rejected }};

// jQuery live search (debounced)
$(function(){
  let timer = null;
  $('#search-input').on('input', function(){
    const q = $(this).val().trim();
    clearTimeout(timer);
    if (!q) { $('#search-results').hide().empty(); return; }
  timer = setTimeout(()=>{
  $.getJSON('/api/pendaftaran/search', { q }).done(function(res){
        const box = $('#search-results').empty();
        if (!res.length) { box.append('<div class="item small text-muted">Tidak ada hasil</div>').show(); return; }
        res.forEach(r=>{
          const item = $(`<div class="item"><strong>${r.nama_pemohon}</strong><div class="small text-muted">${r.no_daftar} • ${r.tanggal_hadir} • ${r.hari}</div></div>`);
          item.on('click', ()=>{
            // show single result in recent list
            $('#recent-body').html(`<tr><td>${r.no_daftar}</td><td>${r.nama_pemohon}</td><td>${r.tanggal_daftar}</td><td>${r.tanggal_hadir}</td><td>${r.jam_hadir}</td></tr>`);
            box.hide();
            $('#search-input').val('');
          });
          box.append(item);
        });
        box.show();
      });
    }, 300);
  });
  // hide when clicking outside
  $(document).on('click', function(e){ if (!$(e.target).closest('.live-search').length) $('#search-results').hide(); });
});

// Schedule chart
const ctx = document.getElementById('scheduleChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [{ label: 'Jumlah Pendaftar', data: values, backgroundColor: '#4f46e5' }]
  },
  options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

// Status pie
const ctx2 = document.getElementById('statusChart').getContext('2d');
new Chart(ctx2, {
  type: 'doughnut',
  data: { labels: ['Diterima','Ditolak'], datasets: [{ data: [accepted, rejected], backgroundColor: ['#16a34a','#ef4444'] }] },
  options: { responsive: true }
});

// Fill recent pendaftar
fetch('/api/pendaftaran').then(r=>r.json()).then(data=>{
  const body = document.getElementById('recent-body');
  body.innerHTML = '';
  data.slice(-8).reverse().forEach((d, i)=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${d.no_daftar}</td><td>${d.nama_pemohon}</td><td>${d.tanggal_daftar}</td><td>${d.tanggal_hadir}</td><td>${d.jam_hadir}</td>`;
    body.appendChild(tr);
  });

  // update sidebar quick stats
  document.getElementById('s-pendaftar').textContent = {{ $totalPendaftar }};
  document.getElementById('s-daftar-ulang').textContent = {{ $totalDaftarUlang }};
  document.getElementById('s-pengurusan').textContent = {{ $totalPengurusan }};
  document.getElementById('s-pendapatan').textContent = 'Rp ' + Number({{ $totalPendapatan }}).toLocaleString();
});
</script>
@endpush
