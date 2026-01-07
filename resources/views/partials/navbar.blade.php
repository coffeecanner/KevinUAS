<header class="navbar navbar-expand-lg navbar-dark bg-dark" style="height:64px;">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-dark" id="sidebarToggle"><i class="fas fa-bars"></i></button>
      <a class="navbar-brand fw-bold text-white ms-2" href="/">Imigrasi Paspor</a>
    </div>

    <div class="d-flex align-items-center gap-3">
      <nav class="d-none d-md-block">
        <a class="nav-link d-inline text-white" href="/ui/pendaftaran">Pendaftaran</a>
        <a class="nav-link d-inline text-white" href="/ui/daftar-ulang">Daftar Ulang</a>
        <a class="nav-link d-inline text-white" href="/ui/pengurusan">Pengurusan</a>
      </nav>

      @if(auth()->check())
        <div class="dropdown">
          <a class="btn btn-sm btn-dark text-white dropdown-toggle" href="#" role="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="/user/profile"><i class="fas fa-id-card me-2"></i> Profil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="/logout" id="logout-form">@csrf<button class="dropdown-item text-danger" type="submit"><i class="fas fa-sign-out-alt me-2"></i> Logout</button></form>
            </li>
          </ul>
        </div>
      @else
        <a class="btn btn-sm btn-outline-light" href="/login">Masuk</a>
      @endif
    </div>
  </div>
</header>

<script>
$('#sidebarToggle').on('click', function(){ $('body').toggleClass('sidebar-collapsed'); });
</script>
