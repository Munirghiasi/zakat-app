<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipientController extends Controller
{
    use Auditable;
    
    public function index()
    {
        $user = Auth::user();
        $recipients = Recipient::where('user_id', $user->id)->orderBy('name')->get();
        
        return view('recipients.index', compact('recipients'));
    }
    
    public function create()
    {
        return view('recipients.create');
    }
    
    public function store(Request $request)
    {
        $validCategories = array_keys(Recipient::getValidCategories());
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', $validCategories),
            'notes' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        
        $recipient = Recipient::create([
            'user_id' => $user->id,
            ...$validated,
        ]);
        
        $this->logAudit('created', $recipient, null, $recipient->toArray());
        
        return redirect()->route('recipients.index')->with('success', 'Recipient added successfully.');
    }
    
    public function edit(Recipient $recipient)
    {
        $user = Auth::user();
        
        if ($recipient->user_id !== $user->id) {
            abort(403);
        }
        
        return view('recipients.edit', compact('recipient'));
    }
    
    public function update(Request $request, Recipient $recipient)
    {
        $user = Auth::user();
        
        if ($recipient->user_id !== $user->id) {
            abort(403);
        }
        
        $validCategories = array_keys(Recipient::getValidCategories());
        
        $oldValues = $recipient->toArray();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', $validCategories),
            'notes' => 'nullable|string',
        ]);
        
        $recipient->update($validated);
        
        $this->logAudit('updated', $recipient, $oldValues, $recipient->toArray());
        
        return redirect()->route('recipients.index')->with('success', 'Recipient updated successfully.');
    }
    
    public function destroy(Recipient $recipient)
    {
        $user = Auth::user();
        
        if ($recipient->user_id !== $user->id) {
            abort(403);
        }
        
        $oldValues = $recipient->toArray();
        
        $recipient->delete();
        
        $this->logAudit('deleted', (object)['id' => $oldValues['id'], 'model_type' => Recipient::class], $oldValues, null);
        
        return redirect()->route('recipients.index')->with('success', 'Recipient deleted successfully.');
    }
}
