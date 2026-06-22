<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SnbpPendaftar;
use App\Services\RankingEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SnbpController extends Controller
{
    protected $rankingEngine;

    public function __construct(RankingEngine $rankingEngine)
    {
        $this->rankingEngine = $rankingEngine;
    }

    public function index(Request $request)
    {
        // Check if menu is enabled
        if (Setting::get('snbp_menu_status', 'nonaktif') !== 'aktif') {
            abort(404, 'Menu SNBP dinonaktifkan.');
        }

        $user = auth()->user();
        $murid = $user->murid;

        if (!$murid || !$murid->kelas) {
            return redirect()->route('login')->with('error', 'Data murid tidak lengkap.');
        }

        $deadlineStr = Setting::get('snbp_deadline');
        $deadline = $deadlineStr ? Carbon::parse($deadlineStr) : null;
        $isExpired = $deadline ? now()->gt($deadline) : true;

        $isRegistered = $murid->snbpPendaftar()->exists();
        $jurusan = $murid->kelas->jurusan;
        $kuota = $jurusan->kuota_snbp ?? 0;

        $rankingList = $this->rankingEngine->getSnbpRankingList($jurusan->id);

        return view('murid.snbp.index', compact(
            'murid',
            'deadline',
            'isExpired',
            'isRegistered',
            'rankingList',
            'kuota',
            'jurusan'
        ));
    }

    public function register()
    {
        if (Setting::get('snbp_menu_status', 'nonaktif') !== 'aktif') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $deadlineStr = Setting::get('snbp_deadline');
        if ($deadlineStr && now()->gt(Carbon::parse($deadlineStr))) {
            return redirect()->back()->with('error', 'Batas waktu pendaftaran telah berakhir.');
        }

        $murid = auth()->user()->murid;
        if (!$murid) {
            return redirect()->back()->with('error', 'Murid tidak ditemukan.');
        }

        if ($murid->snbpPendaftar()->exists()) {
            return redirect()->back()->with('info', 'Anda sudah terdaftar.');
        }

        $murid->snbpPendaftar()->create();

        return redirect()->route('murid.snbp.index')->with('success', 'Pendaftaran Seleksi SNBP berhasil dilakukan.');
    }

    public function withdraw()
    {
        if (Setting::get('snbp_menu_status', 'nonaktif') !== 'aktif') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $deadlineStr = Setting::get('snbp_deadline');
        if ($deadlineStr && now()->gt(Carbon::parse($deadlineStr))) {
            return redirect()->back()->with('error', 'Batas waktu pengunduran diri telah berakhir.');
        }

        $murid = auth()->user()->murid;
        if (!$murid) {
            return redirect()->back()->with('error', 'Murid tidak ditemukan.');
        }

        $murid->snbpPendaftar()->delete();

        return redirect()->route('murid.snbp.index')->with('success', 'Anda berhasil mundur dari seleksi SNBP.');
    }
}
