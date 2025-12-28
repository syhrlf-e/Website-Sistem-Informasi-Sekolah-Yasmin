<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPrestasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.prestasi.index', [
            'prestasi' => Prestasi::latest()
                ->filter(request(['kategori', 'tingkat', 'tahun']))
                ->paginate(10)
                ->withQueryString()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.prestasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'nama_prestasi' => 'required|max:255',
    //         'slug' => 'required|unique:prestasi',
    //         'kategori' => 'required|in:Akademik,Olahraga,Seni',
    //         'tingkat' => 'required|in:Sekolah,Kota,Provinsi,Nasional,Internasional',
    //         'tahun' => 'required|integer|min:2000|max:' . date('Y'),
    //         'peserta' => 'required|array|min:1',
    //         'peserta.*' => 'required|string|max:255',
    //         'image' => 'image|file|max:2048',
    //         'deskripsi' => 'required'
    //     ]);

    //     // Handle image upload
    //     if ($request->file('image')) {
    //         $validatedData['image'] = $request->file('image')->store('prestasi-images');
    //     }

    //     // Peserta already validated as array, no need to json_encode manually
    //     // Laravel will handle it automatically because of the cast in the model

    //     Prestasi::create($validatedData);

    //     return redirect('/yasmin-panel/prestasi')->with('success', 'Prestasi baru berhasil ditambahkan!');
    // }
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'nama_prestasi' => 'required|max:255',
        'slug' => 'required|unique:prestasi',
        'kategori' => 'required|in:Akademik,Olahraga,Seni',
        'tingkat' => 'required|in:Sekolah,Kota,Provinsi,Nasional,Internasional',
        'tahun' => 'required|integer|min:2000|max:' . date('Y'),
        'peserta' => 'required|array|min:1',
        'peserta.*' => 'required|string|max:255',
        'image' => 'image|file|max:2048',
        'deskripsi' => 'required'
    ]);

    // Handle image upload
    if ($request->file('image')) {
        $validatedData['image'] = $request->file('image')->store('prestasi-images');
    }

    // Filter peserta (hapus yang kosong)
    $validatedData['peserta'] = array_values(array_filter($validatedData['peserta']));

    Prestasi::create($validatedData);

    return redirect('/yasmin-panel/prestasi')->with('success', 'Prestasi baru berhasil ditambahkan!');
}

    /**
     * Display the specified resource.
     */
    public function show(Prestasi $prestasi)
    {
        return view('admin.prestasi.show', [
            'prestasi' => $prestasi
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prestasi $prestasi)
    {
        return view('admin.prestasi.edit', [
            'prestasi' => $prestasi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prestasi $prestasi)
    {
        $rules = [
            'nama_prestasi' => 'required|max:255',
            'kategori' => 'required|in:Akademik,Olahraga,Seni',
            'tingkat' => 'required|in:Sekolah,Kota,Provinsi,Nasional,Internasional',
            'tahun' => 'required|integer|min:2000|max:' . date('Y'),
            'peserta' => 'required|array|min:1',
            'peserta.*' => 'required|string|max:255',
            'image' => 'image|file|max:2048',
            'deskripsi' => 'required'
        ];

        if ($request->slug != $prestasi->slug) {
            $rules['slug'] = 'required|unique:prestasi';
        }

        $validatedData = $request->validate($rules);

        // Handle image upload
        if ($request->file('image')) {
            if ($prestasi->image) {
                Storage::delete($prestasi->image);
            }
            $validatedData['image'] = $request->file('image')->store('prestasi-images');
        }

        $prestasi->update($validatedData);

        return redirect('/yasmin-panel/prestasi')->with('success', 'Prestasi berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prestasi $prestasi)
    {
        if ($prestasi->image) {
            Storage::delete($prestasi->image);
        }

        Prestasi::destroy($prestasi->id);

        return redirect('/yasmin-panel/prestasi')->with('success', 'Prestasi berhasil dihapus!');
    }
}
