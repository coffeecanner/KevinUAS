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
      /* Search dropdown */
      .live-search { position: relative; }
      .live-search-results { position: absolute; z-index: 2000; top: 100%; left: 0; right: 0; background: #fff; border-radius: 8px; box-shadow: 0 6px 18px rgba(32,40,60,0.12); max-height: 260px; overflow: auto; }
      .live-search-results .item { padding: 10px; border-bottom: 1px solid #f1f5f9; cursor: pointer; }
      .live-search-results .item:last-child { border-bottom: 0; }
      .live-search-results .item:hover { background: #f8fafc; }
    </style>
  </head>
  <body>
    @include('partials.navbar')
    <main class="container py-4">
      @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
  </body>
</html>
