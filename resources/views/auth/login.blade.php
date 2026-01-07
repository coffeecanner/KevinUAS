@extends('layouts.app')

@section('content')
<div class="row align-items-center" style="min-height:70vh;">
  <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center">
    <div class="text-center px-4">
      <h1 class="display-6 fw-bold">Selamat Datang</h1>
      <p class="lead text-muted">Selamat datang di Layanan Pengurusan Paspor - Kantor Imigrasi. Layanan ini membantu pendaftaran, verifikasi ulang, dan pengurusan dokumen terkait secara cepat dan aman.</p>
      <ul class="text-start small text-muted mt-3">
        <li>Mengatur jadwal kedatangan.</li>
        <li>Generate antrian otomatis untuk daftar ulang.</li>
        <li>Ringkasan pendapatan dan status pengurusan.</li>
      </ul>
      <p class="small text-muted mt-3">Silakan masuk menggunakan akun Anda untuk mengelola data pendaftar dan pengurusan.</p>
    </div>
  </div>

  <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
    <div class="card card-modern p-4" style="max-width:420px; width:100%;">
      <div class="text-center mb-3">
        <i class="fas fa-passport fa-2x text-primary"></i>
        <h4 class="mb-0 mt-2">Masuk ke Sistem</h4>
        <p class="small text-muted">Masukkan kredensial Anda</p>
      </div>

      @if($errors->any())
        <div class="alert alert-danger small">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="mb-2">
          <label class="form-label small">Email</label>
          <input name="email" type="email" class="form-control" value="{{ old('email') }}" required />
        </div>
        <div class="mb-2">
          <label class="form-label small">Password</label>
          <input name="password" type="password" class="form-control" required />
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
            <label class="form-check-label small" for="remember">Remember me</label>
          </div>
          <a href="#" id="forgot-link" class="small">Lupa password?</a>
        </div>

        <div class="d-grid">
          <button class="btn btn-primary">Masuk</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Forgot modal -->
<div class="modal fade" id="forgotModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Lupa Password</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p>Silakan hubungi admin departemen untuk reset password.</p>
        <p class="small text-muted">Kontak: admin@imigrasi.local / +62-21-555-1234</p>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
$('#forgot-link').on('click', function(e){ e.preventDefault(); var m = new bootstrap.Modal(document.getElementById('forgotModal')); m.show(); });
</script>
@endpush
