<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\NisabSetting;
use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use Auditable;
    
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !Auth::user()->is_admin) {
                abort(403);
            }
            return $next($request);
        });
    }
    
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_zakat_paid' => DB::table('distributions')->sum('amount'),
            'active_nisab' => NisabSetting::getActive(),
        ];
        
        return view('admin.index', compact('stats'));
    }
    
    public function nisabSettings()
    {
        $settings = NisabSetting::orderBy('effective_from', 'desc')->get();
        $activeSetting = NisabSetting::getActive();
        
        return view('admin.nisab-settings', compact('settings', 'activeSetting'));
    }
    
    public function updateNisab(Request $request)
    {
        $validated = $request->validate([
            'gold_price_per_gram' => 'required|numeric|min:0',
            'silver_price_per_gram' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:255',
            'effective_from' => 'required|date',
        ]);
        
        // Deactivate all existing settings
        NisabSetting::where('is_active', true)->update(['is_active' => false]);
        
        // Calculate Nisab (Gold price * 87.48 grams)
        $nisabValue = $validated['gold_price_per_gram'] * 87.48;
        
        $setting = NisabSetting::create([
            'gold_price_per_gram' => $validated['gold_price_per_gram'],
            'silver_price_per_gram' => $validated['silver_price_per_gram'] ?? null,
            'nisab_value' => $nisabValue,
            'source' => $validated['source'] ?? null,
            'effective_from' => $validated['effective_from'],
            'is_active' => true,
            'updated_by' => Auth::id(),
        ]);
        
        $this->logAudit('created', $setting, null, $setting->toArray());
        
        return redirect()->route('admin.nisab-settings')->with('success', 'Nisab settings updated successfully.');
    }
    
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.users', compact('users'));
    }
    
    public function toggleUserStatus(User $user)
    {
        // Note: You might want to add a 'is_blocked' field to users table
        // For now, we'll just show the users list
        return redirect()->route('admin.users')->with('info', 'User status toggle not implemented yet.');
    }
    
    public function exchangeRates()
    {
        $rates = ExchangeRate::orderBy('currency')->orderBy('effective_from', 'desc')->get();
        $currencies = app(\App\Services\CurrencyService::class)->getSupportedCurrencies();
        
        return view('admin.exchange-rates', compact('rates', 'currencies'));
    }
    
    public function updateExchangeRate(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|size:3',
            'rate' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'source' => 'nullable|string|max:255',
        ]);
        
        // Deactivate existing rates for this currency
        ExchangeRate::where('currency', $validated['currency'])
            ->where('is_active', true)
            ->update(['is_active' => false]);
        
        // Create new rate
        $rate = ExchangeRate::create([
            'currency' => $validated['currency'],
            'rate' => $validated['rate'],
            'effective_from' => $validated['effective_from'],
            'is_active' => true,
            'source' => $validated['source'] ?? 'Manual Update',
            'updated_by' => Auth::id(),
        ]);
        
        $this->logAudit('created', $rate, null, $rate->toArray());
        
        return redirect()->route('admin.exchange-rates')->with('success', 'Exchange rate updated successfully.');
    }
}
