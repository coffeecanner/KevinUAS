<aside class="sidebar bg-dark text-white p-3" style="width:250px; position:fixed; top:64px; bottom:0; left:0; overflow:auto;">
  <div class="mb-4">
    <h5 class="text-white">Imigrasi</h5>
    <p class="small text-muted">Manajemen Antrian Paspor</p>
  </div>

  <ul class="nav flex-column mb-3">
    <li class="nav-item mb-2"><a class="nav-link text-white" href="/"><i class="fas fa-tachometer-alt me-2"></i>Overview</a></li>
    <li class="nav-item mb-2"><a class="nav-link text-white" href="/ui/pendaftaran"><i class="fas fa-file-signature me-2"></i>Pendaftaran</a></li>
    <li class="nav-item mb-2"><a class="nav-link text-white" href="/ui/daftar-ulang"><i class="fas fa-check-square me-2"></i>Daftar Ulang</a></li>
    <li class="nav-item mb-2"><a class="nav-link text-white" href="/ui/pengurusan"><i class="fas fa-file-invoice-dollar me-2"></i>Pengurusan</a></li>
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
  body.sidebar-collapsed .sidebar { display: none; }
  body { padding-left: 250px; }
  body.sidebar-collapsed { padding-left: 0; }
  @media(max-width: 992px){ body { padding-left: 0; } .sidebar { position:relative; top:0; width:100%; } }
</style>
