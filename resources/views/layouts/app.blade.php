<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Imigrasi - Pengelolaan Paspor</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      body { background: #f5f7fb; }
      .card-modern { border: 0; border-radius: 12px; box-shadow: 0 6px 18px rgba(32,40,60,0.08); }
      .table-actions button { margin-right: 6px; }
      /* Admin header/footer */
      header.navbar { position: fixed; top:0; left:0; right:0; z-index: 1100; }
      main.container { margin-top: 80px; }
      /* sidebar spacing only when present */
      body.has-sidebar { padding-left: 250px; }
      body.has-sidebar.sidebar-collapsed { padding-left: 0; }
      @media(max-width: 992px){ body.has-sidebar { padding-left: 0; } }
  /* Search dropdown */
      .live-search { position: relative; }
      .live-search-results { position: absolute; z-index: 2000; top: 100%; left: 0; right: 0; background: #fff; border-radius: 8px; box-shadow: 0 6px 18px rgba(32,40,60,0.12); max-height: 260px; overflow: auto; }
      .live-search-results .item { padding: 10px; border-bottom: 1px solid #f1f5f9; cursor: pointer; }
      .live-search-results .item:last-child { border-bottom: 0; }
      .live-search-results .item:hover { background: #f8fafc; }
  /* Button hover & focus polish */
  .btn { transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease; }
  .btn:not(.disabled):hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(16,24,40,0.08); }
  .btn:active { transform: translateY(-1px); }
  .btn-outline-light:hover { color: #fff; background: rgba(255,255,255,0.06); }
  .btn-primary { box-shadow: 0 6px 14px rgba(79,70,229,0.08); }
  .btn-primary:hover { box-shadow: 0 10px 30px rgba(79,70,229,0.14); }
  .btn-sm { transition: transform .12s ease; }
  .btn:focus { outline: 0; box-shadow: 0 0 0 0.15rem rgba(79,70,229,0.12); }
    </style>
  </head>
  <body class="{{ auth()->check() ? 'has-sidebar' : '' }}">
    @include('partials.navbar')
    @if(auth()->check())
      @include('partials.sidebar')
    @endif
    <!-- Session expired modal -->
    <div class="modal fade" id="sessionExpiredModal" tabindex="-1">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"><h6 class="modal-title">Sesi Berakhir</h6></div>
          <div class="modal-body">Sesi anda telah berakhir. Silahkan login kembali.</div>
          <div class="modal-footer"><a href="/login" class="btn btn-primary">Login</a></div>
        </div>
      </div>
    </div>

    <main class="container py-4">
      @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Inactivity-based session expiry (client-side): uses Laravel session.lifetime (minutes)
    (function(){
      var lifetime = {{ config('session.lifetime') }} || 360; // minutes
      var timeoutMs = lifetime * 60 * 1000;
      var idleTimer = null;
      var warningShown = false;

      function resetTimer(){
        if (idleTimer) clearTimeout(idleTimer);
        idleTimer = setTimeout(onIdle, timeoutMs);
        if (warningShown) { warningShown = false; }
      }

      function onIdle(){
        // show modal and redirect to login
        var modalEl = document.getElementById('sessionExpiredModal');
        if (modalEl) {
          var bs = new bootstrap.Modal(modalEl);
          bs.show();
        } else {
          alert('Sesi anda telah berakhir. Silahkan login kembali');
          window.location = '/login';
        }
        // after short delay, force redirect
        setTimeout(function(){ window.location = '/login'; }, 3500);
      }

      // events to reset inactivity timer
      ['mousemove','mousedown','keydown','scroll','touchstart'].forEach(function(ev){ document.addEventListener(ev, resetTimer, true); });

      // start timer on load
      document.addEventListener('DOMContentLoaded', function(){ resetTimer(); });
    })();
    </script>
    @stack('scripts')
  </body>
</html>
