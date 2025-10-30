<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor' => 'required|unique:employees,nomor',
            'nama' => 'required',
            'photo' => 'nullable|image|max:5120'
        ]);

        $url = null;

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // pakai putFile agar lebih reliable
            try {
                $path = Storage::disk('s3')->putFile('photos', $request->file('photo'));
            } catch (\Exception $e) {
                \Log::error('S3 putFile error: '.$e->getMessage());
                return back()->withInput()->withErrors(['photo' => 'Gagal menulis file ke storage. Periksa MinIO & konfigurasi .env.']);
            }
        
            if ($path && is_string($path) && strlen($path) > 0) {
                try {
                    Storage::disk('s3')->setVisibility($path, 'public');
                    $url = Storage::disk('s3')->url($path);
                } catch (\Exception $e) {
                    \Log::error('S3 setVisibility/url error: '.$e->getMessage());
                    return back()->withInput()->withErrors(['photo' => 'Gagal menyimpan file ke storage (S3/MinIO).']);
                }
            } else {
                return back()->withInput()->withErrors(['photo' => 'Upload file gagal. Coba lagi.']);
            }
        }
        
        $emp = Employee::create([
            'nomor' => $request->nomor,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'talahir' => $request->talahir,
            'photo_upload_path' => $url,
            'created_on' => \Carbon\Carbon::now(),
            'created_by' => auth()->user()->name ?? 'system',
        ]);

        \Illuminate\Support\Facades\Redis::set('emp_'.$emp->nomor, $emp->toJson());

        return redirect()->route('employees.index')->with('success','Employee created');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $emp = Employee::findOrFail($id);
        return view('employees.show', compact('emp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $emp = Employee::findOrFail($id);
        if ($request->hasFile('photo')) {
            if ($emp->photo_upload_path) {
                $endpoint = rtrim(config('filesystems.disks.s3.endpoint'), '/');
                $relative = ltrim(str_replace($endpoint, '', $emp->photo_upload_path), '/');
                if ($relative) {
                    Storage::disk('s3')->delete($relative);
                }
            }
            $path = $request->file('photo')->store('photos', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $emp->photo_upload_path = Storage::disk('s3')->url($path);
        }

        $emp->nama = $request->nama;
        $emp->jabatan = $request->jabatan;
        $emp->talahir = $request->talahir;
        $emp->updated_on = Carbon::now();
        $emp->updated_by = auth()->user()->name ?? 'system';
        $emp->save();

        Redis::set('emp_'.$emp->nomor, $emp->toJson());

        return redirect()->route('employees.index')->with('success','Employee updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $emp = Employee::findOrFail($id);
        if ($emp->photo_upload_path) {
            $endpoint = rtrim(config('filesystems.disks.s3.endpoint'), '/');
            $relative = ltrim(str_replace($endpoint, '', $emp->photo_upload_path), '/');
            if ($relative) {
                Storage::disk('s3')->delete($relative);
            }
        }

        $emp->delete();
        Redis::del('emp_'.$emp->nomor);
        return redirect()->route('employees.index')->with('success','Employee deleted');
    }
}
