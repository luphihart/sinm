<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MuridRequest;
use App\Models\User;
use App\Models\Murid;
use App\Repositories\Contracts\MuridRepositoryInterface;
use App\Repositories\Contracts\KelasRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MuridController extends Controller
{
    protected $muridRepo;
    protected $kelasRepo;

    public function __construct(MuridRepositoryInterface $muridRepo, KelasRepositoryInterface $kelasRepo)
    {
        $this->muridRepo = $muridRepo;
        $this->kelasRepo = $kelasRepo;
    }

    public function index()
    {
        $murids = $this->muridRepo->getWithDetails();
        $kelas = $this->kelasRepo->all();
        return view('admin.murid.index', compact('murids', 'kelas'));
    }

    public function store(MuridRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            // 1. Buat User Account
            $user = User::create([
                'name' => $data['nama_lengkap'],
                'username' => $data['nis'],
                'password' => Hash::make($data['password']),
                'role' => 'murid',
            ]);

            // 2. Buat Murid Record
            $data['user_id'] = $user->id;
            $this->muridRepo->create($data);

            DB::commit();
            return redirect()->route('admin.murid.index')->with('success', 'Data murid dan akun berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.murid.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(MuridRequest $request, $id)
    {
        $data = $request->validated();
        $murid = $this->muridRepo->find($id);
        $user = $murid->user;

        DB::beginTransaction();
        try {
            // 1. Update User Account
            $userUpdate = [
                'name' => $data['nama_lengkap'],
                'username' => $data['nis'],
            ];
            if (!empty($data['password'])) {
                $userUpdate['password'] = Hash::make($data['password']);
            }
            $user->update($userUpdate);

            // 2. Update Murid Record
            $this->muridRepo->update($id, $data);

            DB::commit();
            return redirect()->route('admin.murid.index')->with('success', 'Data murid berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.murid.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $murid = $this->muridRepo->find($id);
        $user = $murid->user;

        DB::beginTransaction();
        try {
            if ($user) {
                // Menghapus User akan menghapus Murid via cascade foreign key
                $user->delete();
            } else {
                $murid->delete();
            }
            DB::commit();
            return redirect()->route('admin.murid.index')->with('success', 'Data murid dan akun berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.murid.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for importing students.
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MuridTemplateExport(), 'template_import_murid.xlsx');
    }

    /**
     * Preview student import Excel content.
     */
    public function importPreview(\Illuminate\Http\Request $request, \App\Services\ImportMuridService $importService)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        $file = $request->file('file_excel');
        $previewResult = $importService->preview($file);

        if (!$previewResult['isValid'] && !empty($previewResult['errors'])) {
            return back()->withErrors($previewResult['errors']);
        }

        // Store preview data in session
        $request->session()->put('murid_import_preview_data', $previewResult['data']);

        return view('admin.murid.import_preview', [
            'previewData' => $previewResult['data'],
            'isValidFile' => $previewResult['isValid']
        ]);
    }

    /**
     * Confirm and execute student import.
     */
    public function importConfirm(\Illuminate\Http\Request $request, \App\Services\ImportMuridService $importService)
    {
        if ($request->has('cancel')) {
            $request->session()->forget('murid_import_preview_data');
            return redirect()->route('admin.murid.index')->with('info', 'Import dibatalkan.');
        }

        $data = $request->session()->get('murid_import_preview_data');

        if (!$data) {
            return redirect()->route('admin.murid.index')->with('error', 'Data preview tidak ditemukan. Silakan upload ulang file Excel.');
        }

        try {
            $importService->import($data);
            $request->session()->forget('murid_import_preview_data');
            return redirect()->route('admin.murid.index')->with('success', 'Data murid dan akun login berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('admin.murid.index')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
