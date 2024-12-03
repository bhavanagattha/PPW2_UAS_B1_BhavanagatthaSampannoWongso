<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use Illuminate\Http\Request;

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::orderBy('tanggal_pembelian','DESC')->get();

        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        return view('transaksi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pembelian' => 'required|date',
            'bayar' => 'required|numeric',
            'nama_produk1' => 'required|string',
            'harga_satuan1' => 'required|numeric',
            'jumlah1' => 'required|numeric',
            'nama_produk2' => 'required|string',
            'harga_satuan2' => 'required|numeric',
            'jumlah2' => 'required|numeric',
            'nama_produk3' => 'required|string',
            'harga_satuan3' => 'required|numeric',
            'jumlah3' => 'required|numeric',
        ]);

        // Gunakan transaction
        try {
            $transaksi->tanggal_pembelian = $request->input('tanggal_pembelian');
            $transaksi->total_harga = 0;
            $transaksi->bayar = $request->input('bayar');
            $transaksi->kembalian = 0;
            $transaksi->save();

            $total_harga = 0;
            for ($i = 1; $i <= 3; $i++){
                $nama_produk = $request->input("nama_produk$i");
            $harga_satuan = $request->input("harga_satuan$i");
            $jumlah = $request->input("jumlah$i");

            if ($nama_produk && $harga_satuan && $jumlah) {
                $subtotal = $harga_satuan * $jumlah;

                $transaksiDetail = new TransaksiDetail();
                $transaksiDetail->id_transaksi = $transaksi->id;
                $transaksiDetail->nama_produk = $nama_produk;
                $transaksiDetail->harga_satuan = $harga_satuan;
                $transaksiDetail->jumlah = $jumlah;
                $transaksiDetail->subtotal = $subtotal;
                $transaksiDetail->save();

                $total_harga += $subtotal;
            }
        }

        $transaksi->total_harga = $total_harga;
        $transaksi->kembalian = $transaksi->bayar - $total_harga;
        $transaksi->save();

        DB::commit();
        return redirect()->route('transaksidetail.index', $transaksi->id)->with('pesan', 'Berhasil menambahkan data');


        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Transaction' => 'Gagal menambahkan data'])->withInput();
        }
    }

    public function edit($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return view('transaksi.edit',);
    }

    public function update(Request $request)
    {
        $request->validate([
            'bayar' => 'required|numeric'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->bayar = $request->input('bayar');
        $transaksi->kembalian =

        return redirect('/transaksi') -> with('pesan', 'Berhasil mengubah data');
    }

    public function destroy()
    {
        $transaksi = Transaksi::findOrFail($id);

        return redirect('/transaksi');
    }
}
