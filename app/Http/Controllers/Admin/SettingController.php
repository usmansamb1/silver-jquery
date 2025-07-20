<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get current settings
        $settings = []; // Replace with actual settings model when available
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'contact_email' => 'required|email',
            'tax_rate' => 'nullable|numeric|between:0,100',
            'maintenance_mode' => 'boolean',
        ]);
        
        // Update settings
        // Implementation will depend on how settings are stored
        
        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully');
    }
} 