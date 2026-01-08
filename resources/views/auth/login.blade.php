@extends('layouts.app')

@section('content')
<div class="row align-items-center" style="min-height:75vh;">

  {{-- LEFT INFO (DESKTOP ONLY) --}}
  <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center">
    <div class="px-4" style="max-width:520px;">
      <div class="mb-3">
        <h2 class="fw-bold mb-1">SIMPAS</h2>
        <h6 class="text-muted">Sistem Imigrasi & Paspor</h6>
      </div>

      <p class="text-muted">
        SIMPAS merupakan sistem internal Kantor Imigrasi yang digunakan
        untuk mengelola proses <strong>pendaftaran, daftar ulang, antrian,
        dan pengurusan paspor</strong> secara terintegrasi.
      </p>

      <ul class="small text-muted mt-3">
        <li>Manajemen jadwal dan antrian pemohon paspor</li>
        <li>Verifikasi daftar ulang dan status pengurusan</li>
        <li>Monitoring pendapatan dan laporan layanan</li>
      </ul>

      <div class="alert alert-light border mt-4 small">
        <i class="fas fa-shield-alt me-2 text-primary"></i>
        Akses ke sistem ini terbatas untuk petugas berwenang.
        Setiap aktivitas tercatat dalam sistem.
      </div>
    </div>
  </div>

  {{-- RIGHT LOGIN CARD --}}
  <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
    <div class="card card-modern p-4" style="max-width:420px; width:100%;">

      <div class="text-center mb-4">
        <i class="fas fa-passport fa-2x text-primary mb-2"></i>
        <h4 class="mb-1">Masuk ke SIMPAS</h4>
        <p class="small text-muted">Silakan autentikasi untuk melanjutkan</p>
      </div>

      @if($errors->any())
        <div class="alert alert-danger small">
          <i class="fas fa-exclamation-triangle me-1"></i>
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ url('/login') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label small">Email</label>
          <input
            name="email"
            type="email"
            class="form-control"
            placeholder="nama@imigrasi.go.id"
            value="{{ old('email') }}"
            required
          />
        </div>

        <div class="mb-3">
          <label class="form-label small">Password</label>
          <input
            name="password"
            type="password"
            class="form-control"
            required
          />
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              name="remember"
              id="remember"
              {{ old('remember') ? 'checked' : '' }}
            />
            <label class="form-check-label small" for="remember">
              Ingat saya
            </label>
          </div>

          <a href="#" id="forgot-link" class="small text-decoration-none">
            Lupa password?
          </a>
        </div>

        <div class="d-grid">
          <button class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-1"></i>
            Masuk
          </button>
        </div>
      </form>

      <hr class="my-4">

      <p class="text-center small text-muted mb-0">
        © {{ date('Y') }} SIMPAS — Sistem Imigrasi & Paspor
      </p>
    </div>
  </div>
</div>

{{-- FORGOT PASSWORD MODAL --}}
<div class="modal fade" id="forgotModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Lupa Password</h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body small">
        <p>
          Untuk keamanan sistem, reset password hanya dapat dilakukan
          oleh administrator.
        </p>
        <p class="text-muted mb-0">
          Hubungi admin IT atau supervisor unit Anda.
        </p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('forgot-link')?.addEventListener('click', function(e){
  e.preventDefault();
  new bootstrap.Modal(document.getElementById('forgotModal')).show();
});
</script>
@endpush
