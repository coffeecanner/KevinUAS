<aside class="sidebar bg-dark text-white p-3" style="width:250px; position:fixed; top:64px; bottom:0; left:0; overflow:auto;">
  <div class="mb-4">
    <h5 class="text-white">Imigrasi</h5>
    <p class="small text-muted">Manajemen Antrian Paspor</p>
  </div>

  <ul class="nav flex-column mb-3">
  <li class="nav-item mb-2"><a class="nav-link text-white d-flex align-items-center" href="/"><i class="fas fa-tachometer-alt me-2 fa-fw"></i><span>Overview</span></a></li>
  <li class="nav-item mb-2"><a class="nav-link text-white" href="/pendaftaran"><i class="fas fa-file-signature me-2"></i>Pendaftaran</a></li>
  <li class="nav-item mb-2"><a class="nav-link text-white" href="/daftar-ulang"><i class="fas fa-check-square me-2"></i>Daftar Ulang</a></li>
  <li class="nav-item mb-2"><a class="nav-link text-white" href="/pengurusan"><i class="fas fa-file-invoice-dollar me-2"></i>Pengurusan</a></li>
    <li class="nav-item mt-3"><a class="nav-link text-white" href="/user/profile"><i class="fas fa-user me-2"></i>Profil Saya</a></li>
  </ul>

  <hr class="border-secondary" />
  <h6 class="text-muted text-white-50">Quick Stats</h6>
  <div class="d-flex flex-column gap-2 mt-2 small">
    <div class="d-flex justify-content-between"><span>Pendaftar</span><strong id="s-pendaftar">0</strong></div>
    <div class="d-flex justify-content-between"><span>Daftar Ulang</span><strong id="s-daftar-ulang">0</strong></div>
    <div class="d-flex justify-content-between"><span>Pengurusan</span><strong id="s-pengurusan">0</strong></div>
    <div class="d-flex justify-content-between"><span>Pendapatan</span><strong id="s-pendapatan">Rp 0</strong></div>
  </div>
</aside>

<style>
  /* sliding animation and hover effects */
  .sidebar { transition: transform 0.32s cubic-bezier(.2,.9,.2,1), box-shadow 0.2s; z-index:1080; }
  /* ensure collapsed state moves sidebar off-canvas and works when has-sidebar is present */
  body.sidebar-collapsed .sidebar,
  body.has-sidebar.sidebar-collapsed .sidebar { transform: translateX(-260px); box-shadow: none; }
  body.has-sidebar { transition: padding-left 0.32s cubic-bezier(.2,.9,.2,1); }
  body.has-sidebar.sidebar-collapsed { padding-left: 0; }
  .sidebar .nav-link { transition: background-color .2s, transform .18s; border-radius:6px; padding:8px 10px; }
  .sidebar .nav-link .fa-fw { width:18px; }
  .sidebar .nav-link:hover { background: rgba(255,255,255,0.04); transform: translateX(6px); }
  .sidebar .nav-link:hover i { transform: translateX(4px); transition: transform .2s; }
  .sidebar .nav-link:focus, .sidebar .nav-link:active { background: rgba(255,255,255,0.06); box-shadow: inset 3px 0 0 rgba(255,255,255,0.06); }
  .sidebar .nav-link .title { transition: color .18s; }
  .sidebar .nav-link:hover .title { color: #fff; }
  /* cool dropdown effect for nested items if any */
  .sidebar .submenu { max-height: 0; overflow: hidden; transition: max-height .28s cubic-bezier(.2,.9,.2,1), opacity .2s; opacity: 0; }
  .sidebar .submenu.show { max-height: 400px; opacity: 1; }
  @media(max-width: 992px){ .sidebar { position:relative; top:0; width:100%; transform:none; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
  function updateQuickStats(){
    fetch('/dashboard/summary')
      .then(function(r){ if (!r.ok) throw new Error('network'); return r.json(); })
      .then(function(data){
        try{
          var el1 = document.getElementById('s-pendaftar'); if (el1) el1.textContent = data.totalPendaftar;
          var el2 = document.getElementById('s-daftar-ulang'); if (el2) el2.textContent = data.totalDaftarUlang;
          var el3 = document.getElementById('s-pengurusan'); if (el3) el3.textContent = data.totalPengurusan;
          var el4 = document.getElementById('s-pendapatan'); if (el4) el4.textContent = 'Rp ' + Number(data.totalPendapatan).toLocaleString();
        }catch(e){ console.warn('quickstats update failed', e); }
      }).catch(function(e){ /* silently ignore */ console.warn('Could not update quick stats', e); });
  }
  updateQuickStats();
  setInterval(updateQuickStats, 60000); // refresh every minute
});
</script>
