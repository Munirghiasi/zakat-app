<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\ZakatYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $years = ZakatYear::where('user_id', $user->id)
            ->orderBy('year', 'desc')
            ->get();
        
        return view('history.index', compact('years'));
    }
    
    public function show($year)
    {
        $user = Auth::user();
        $zakatYear = ZakatYear::where('user_id', $user->id)
            ->where('year', $year)
            ->firstOrFail();
        
        $distributions = Distribution::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->with('recipient')
            ->orderBy('distribution_date', 'desc')
            ->get();
        
        return view('history.show', compact('zakatYear', 'distributions'));
    }
}
