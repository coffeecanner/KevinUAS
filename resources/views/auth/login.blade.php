@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height:70vh;">
  <div class="card card-modern p-4" style="width:420px;">
    <h4 class="mb-3">Masuk ke Sistem</h4>

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
