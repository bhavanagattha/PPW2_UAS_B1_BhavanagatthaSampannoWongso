<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;

class DashboardController extends Controller
{
    public function index()
    {
        $transaksi_count = Transaksi::count(); 
        $total_items_sold = TransaksiDetail::sum('jumlah'); 
        $total_revenue = TransaksiDetail::sum('harga_total'); 

        return view('dashboard', [
            'transaksi_count' => $transaksi_count,
            'total_items_sold' => $total_items_sold,
            'total_revenue' => $total_revenue,
        ]);
    }
}
