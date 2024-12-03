<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use Illuminate\Http\Request;

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class TransaksiDetailController extends Controller
{
    public function index()
    {
        $transaksidetail = TransaksiDetail::with('transaksi')->orderBy('id','DESC')->get();

        return view('transaksidetail.index', compact('transaksidetail'));
    }

    public function detail(Request $request)
    {
        $transaksi = Transaksi::with('transaksidetail')->findOrFail($request->id_transaksi);

        return view('transaksidetail.detail', compact('transaksi'));
    }

    public function edit($id)
    {
        $transaksidetail = TransaksiDetail::findOrFail($id);
        return view('transaksidetail.edit', compact('transaksidetail'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string',
            'harga_satuan' => 'required|numeric',
            'jumlah' => 'required|numeric',
        ]);

        DB:beginTransaction();

        // Gunakan transaction
        try {
            $transaksidetail = TransaksiDetail::findOrFail($id);
            $transaksidetail->nama_produk = $request->input('nama_produk');
            $transaksidetail->harga_satuan = $request->input('harga_satuan');
            $transaksidetail->jumlah = $request->input('jumlah');
            $transaksidetail->subtotal = $request->input('harga_satuan') * $request->inpiut('jumlah');
            $transaksidetail->save();

            $transaksi = Transaksi::with('transaksidetail')->findOrFail($transaksidetail->id_transaksi);
            $transaksi->total_harga = $transaksi->transaksidetail->sum('subtotal');
            $transaksi->kembalian = $transaksi->bayar - $transaksi->total_harga; // hapus rumus
            $transaksi->save();

            DB::commit();

            return redirect()->route('transaksidetail.detail', $transaksi->id)->with('pesan', 'Berhasil mengubah data');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Transaction' => 'Gagal menambahkan data'])->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $transaksidetail = TransaksiDetail::findOrFail($id);

            $transaksi = Transaksi::with('transaksidetail')->findOrFail($transaksidetail->id_transaksi);

            $transaksidetail->delete();

            $transaksi->total_harga = $transaksi->transaksidetail->sum('subtotal');
            $transaksi->kembalian = $transaksi->bayar - $transaksi->total_harga;
            $transaksi->save();

            DB::commit();

            return redirect()->route('transaksidetail.detail', $transaksi->id)->with('pesan', 'Berhasil menghapus data');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['Transaction' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
