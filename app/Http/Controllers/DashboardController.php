<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\DaftarUlang;
use App\Models\Pengurusan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalPendaftar = Pendaftaran::count();
        $totalDaftarUlang = DaftarUlang::count();
        $totalPengurusan = Pengurusan::count();
        $totalPendapatan = Pengurusan::where('status', 'Diterima')->sum('pembayaran');

        // Prepare next 7 days schedule counts
        $labels = [];
        $values = [];
        $start = Carbon::today();
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->format('Y-m-d');
            $count = Pendaftaran::whereDate('tanggal_hadir', $day->toDateString())->count();
            $values[] = $count;
        }

        // Pie data for pengurusan status
        $accepted = Pengurusan::where('status', 'Diterima')->count();
        $rejected = Pengurusan::where('status', 'Ditolak')->count();

        return view('dashboard', compact(
            'totalPendaftar', 'totalDaftarUlang', 'totalPengurusan', 'totalPendapatan',
            'labels', 'values', 'accepted', 'rejected'
        ));
    }
}
