<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Setting;
use App\Services\RankingEngine;
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
        $settings = [
            'snbp_menu_status' => Setting::get('snbp_menu_status', 'nonaktif'),
            'snbp_deadline' => Setting::get('snbp_deadline'),
        ];

        $jurusans = Jurusan::withCount(['murid as pendaftar_count' => function ($q) {
            $q->whereHas('snbpPendaftar');
        }])->get();

        $selectedJurusanId = $request->input('jurusan_id');
        $rankingList = [];
        $selectedJurusan = null;

        if ($selectedJurusanId) {
            $selectedJurusan = Jurusan::find($selectedJurusanId);
            if ($selectedJurusan) {
                $rankingList = $this->rankingEngine->getSnbpRankingList($selectedJurusanId);
            }
        }

        return view('admin.snbp.index', compact('settings', 'jurusans', 'selectedJurusanId', 'rankingList', 'selectedJurusan'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'snbp_menu_status' => 'required|in:aktif,nonaktif',
            'snbp_deadline' => 'required|date',
        ]);

        Setting::set('snbp_menu_status', $data['snbp_menu_status']);
        Setting::set('snbp_deadline', $data['snbp_deadline']);

        return redirect()->route('admin.snbp.index')->with('success', 'Pengaturan Seleksi SNBP berhasil diperbarui.');
    }

    public function updateQuota(Request $request)
    {
        $data = $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'kuota_snbp' => 'required|integer|min:0',
        ]);

        $jurusan = Jurusan::findOrFail($data['jurusan_id']);
        $jurusan->update([
            'kuota_snbp' => $data['kuota_snbp']
        ]);

        return redirect()->route('admin.snbp.index', ['jurusan_id' => $jurusan->id])
            ->with('success', 'Kuota SNBP untuk jurusan ' . $jurusan->nama_jurusan . ' berhasil diperbarui.');
    }
}
