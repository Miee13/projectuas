<?php

namespace App\Http\Controllers;

use App\Models\Trend;
use Illuminate\Http\Request;

class TrendController extends Controller
{
    public function index(Request $request)
    {
        $query = Trend::query();

        // 1. Filter Kategori
        if ($request->category && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // 2. Filter Pencarian
        if ($request->search) {
            $searchTerm = $request->search;
            
            // Menggunakan fungsi closure agar kondisi OR terbungkus di dalam kurung
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('summary', 'like', '%' . $searchTerm . '%');
            });
        }

        // Ambil 15 data terbaru yang sudah difilter
        $trends = $query->latest()->take(15)->get();
        
        return view('trends.index', compact('trends'));
    }

    public function trending()
    {
        // Ambil 50 data teratas untuk halaman Sedang Tren
        $trends = Trend::orderBy('id', 'asc')->take(50)->get();
        return view('trends.trending', compact('trends'));
    }

    public function about()
    {
        return view('trends.about');
    }
}