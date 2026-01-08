<aside class="sidebar bg-dark text-white p-3"
       style="width:250px; position:fixed; top:64px; bottom:0; left:0; overflow:auto;">

  <!-- MENU -->
  <ul class="nav flex-column mb-3">
    <li class="nav-item mb-2">
      <a class="nav-link text-white d-flex align-items-center" href="/">
        <i class="fas fa-tachometer-alt me-2 fa-fw"></i>
        <span>Overview</span>
      </a>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link text-white" href="/pendaftaran">
        <i class="fas fa-file-signature me-2"></i>
        <span>Pendaftaran</span>
      </a>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link text-white" href="/daftar-ulang">
        <i class="fas fa-check-square me-2"></i>
        <span>Daftar Ulang</span>
      </a>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link text-white" href="/pengurusan">
        <i class="fas fa-file-invoice-dollar me-2"></i>
        <span>Pengurusan</span>
      </a>
    </li>

    <li class="nav-item mt-3">
      <a class="nav-link text-white" href="/user/profile">
        <i class="fas fa-user me-2"></i>
        <span>Profil Saya</span>
      </a>
    </li>
  </ul>

  <hr class="border-secondary" />

  <!-- QUICK STATS -->
  <h6 class="text-white-50">Quick Stats</h6>
  <div class="d-flex flex-column gap-2 mt-2 small">
    <div class="d-flex justify-content-between">
      <span>Pendaftar</span><strong id="s-pendaftar">0</strong>
    </div>
    <div class="d-flex justify-content-between">
      <span>Daftar Ulang</span><strong id="s-daftar-ulang">0</strong>
    </div>
    <div class="d-flex justify-content-between">
      <span>Pengurusan</span><strong id="s-pengurusan">0</strong>
    </div>
    <div class="d-flex justify-content-between">
      <span>Pendapatan</span><strong id="s-pendapatan">Rp 0</strong>
    </div>
  </div>

  <!-- CREDIT -->
  <div class="mt-4 pt-3 border-top border-secondary text-center small text-white-50">
    <div class="fw-semibold text-white">SIMPAS</div>
    <div class="opacity-75">Sistem Imigrasi & Paspor</div>
    <div class="mt-2">
      <span class="opacity-75">Programmer</span><br>
      <strong class="text-white">Kevin Novebrianto</strong><br>
      <span class="opacity-75">221011400853 • 07TPLP021</span>
    </div>
  </div>

</aside>

<style>
/* ================= SIDEBAR CORE ================= */
.sidebar {
  transition: transform .32s cubic-bezier(.2,.9,.2,1);
  z-index: 1080;
}

/* 🔥 INI KUNCI UTAMA */
body.sidebar-collapsed .sidebar {
  transform: translateX(-260px);
}

/* body spacing */
body.has-sidebar {
  padding-left: 250px;
}

body.has-sidebar.sidebar-collapsed {
  padding-left: 0;
}

/* ================= HOVER ================= */
.sidebar .nav-link {
  position: relative;
  border-radius: 6px;
  padding: 8px 10px;
  transition: background-color .2s, transform .18s;
}

.sidebar .nav-link:hover {
  background: rgba(255,255,255,0.04);
  transform: translateX(6px);
}

/* ================= ACTIVE ================= */
.sidebar .nav-link.active {
  background: linear-gradient(
    90deg,
    rgba(99,102,241,0.18),
    rgba(255,255,255,0.02)
  );
  box-shadow: inset 4px 0 0 #6366f1;
  transform: translateX(6px);
}

.sidebar .nav-link.active i {
  color: #a5b4fc;
}

/* accent bar */
.sidebar .nav-link::before {
  content: '';
  position: absolute;
  left: 0;
  top: 10%;
  height: 80%;
  width: 0;
  background: #6366f1;
  border-radius: 0 4px 4px 0;
  transition: width .25s ease;
}

.sidebar .nav-link.active::before {
  width: 4px;
}

/* ================= MOBILE ================= */
/* ❗ JANGAN pakai transform:none di sini */
@media (max-width: 992px) {
  body.has-sidebar {
    padding-left: 0;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){

  /* ACTIVE MENU */
  const links = document.querySelectorAll('.sidebar .nav-link');
  const currentPath = location.pathname.replace(/\/$/, '');

  links.forEach(link => {
    const href = link.getAttribute('href').replace(/\/$/, '');
    if (href === currentPath) link.classList.add('active');
  });

  /* QUICK STATS */
  function updateQuickStats(){
    fetch('/dashboard/summary')
      .then(r => r.ok ? r.json() : null)
      .then(d => {
        if (!d) return;
        s('s-pendaftar', d.totalPendaftar);
        s('s-daftar-ulang', d.totalDaftarUlang);
        s('s-pengurusan', d.totalPengurusan);
        s('s-pendapatan', 'Rp ' + Number(d.totalPendapatan).toLocaleString());
      });
  }
  function s(id,val){ const e=document.getElementById(id); if(e) e.textContent=val; }

  updateQuickStats();
  setInterval(updateQuickStats, 60000);
});
</script>
