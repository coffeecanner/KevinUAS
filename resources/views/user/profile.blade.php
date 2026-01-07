@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card card-modern p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Profil Pengguna</h4>
        <a class="btn btn-sm btn-outline-secondary" href="/">Kembali</a>
      </div>

      <dl class="row">
        <dt class="col-sm-3">Nama</dt>
        <dd class="col-sm-9">{{ $user->name }}</dd>

        <dt class="col-sm-3">Email</dt>
        <dd class="col-sm-9">{{ $user->email }}</dd>

        <dt class="col-sm-3">Peran</dt>
        <dd class="col-sm-9">{{ $user->role ?? 'Staf' }}</dd>

        <dt class="col-sm-3">Kontak Dukungan</dt>
        <dd class="col-sm-9">admin@imigrasi.local — <a href="mailto:admin@imigrasi.local">Kirim email</a></dd>
      </dl>

      <div class="mt-3">
        <p class="small text-muted">Untuk mengganti password atau informasi sensitif, silakan hubungi support.</p>
      </div>
    </div>
  </div>
</div>
@endsection
