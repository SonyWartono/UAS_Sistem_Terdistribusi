<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class BukuController extends Controller
{
    public function index()
    {
        return Buku::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun' => 'required|integer|digits:4',
            'penerbit' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tersedia' => 'required|boolean',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        } else {
            $photoPath = null;
        }

        $buku = Buku::create([
            'judul' => $request->judul,
            'pengarang' => $request->pengarang,
            'deskripsi' => $request->deskripsi,
            'tahun' => $request->tahun,
            'penerbit' => $request->penerbit,
            'lokasi' => $request->lokasi,
            'tersedia' => $request->tersedia,
            'photo' => $photoPath,
        ]);

        return response()->json($buku, 201);
    }

    public function show($id)
    {
        $buku = Buku::find($id);
        if ($buku) {
            return response()->json($buku);
        } else {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }
    }

    public function update(Request $request, $id)
{
    $buku = Buku::find($id);
    if ($buku) {
        $request->validate([
            'judul' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'pengarang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun' => 'required|integer|digits:4',
            'penerbit' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tersedia' => 'required|boolean',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($buku->photo) {
                Storage::disk('public')->delete($buku->photo);
            }

            // Upload foto baru
            $photoPath = $request->file('photo')->store('photos', 'public');
        } else {
            $photoPath = $buku->photo;
        }

        $buku->update([
            'judul' => $request->judul,
            'photo' => $photoPath,
            'pengarang' => $request->pengarang,
            'deskripsi' => $request->deskripsi,
            'tahun' => $request->tahun,
            'penerbit' => $request->penerbit,
            'lokasi' => $request->lokasi,
            'tersedia' => $request->tersedia,
        ]);

        return response()->json($buku);
    } else {
        return response()->json(['message' => 'Buku tidak ditemukan'], 404);
    }
}

    public function destroy($id)
    {
        $buku = Buku::find($id);
        if ($buku) {
            $buku->delete();
            return response()->json(['message' => 'Buku berhasil dihapus']);
        } else {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }
    }
}

