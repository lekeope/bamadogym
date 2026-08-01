<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AppSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'schema' => AppSettings::schema(),
            'settings' => AppSettings::all(),
        ]);
    }

    public function update(Request $request)
    {
        $rules = [];

        foreach (AppSettings::schema() as $group) {
            foreach ($group['fields'] as $key => $field) {
                $rules[$key] = match ($field['type']) {
                    'email' => ['required', 'email', 'max:255'],
                    'number' => ['required', 'integer', 'min:1', 'max:90'],
                    'textarea' => ['required', 'string', 'max:2000'],
                    default => ['required', 'string', 'max:255'],
                };

                if ($key === 'currency') {
                    $rules[$key] = ['required', 'string', 'size:3', 'alpha'];
                }
            }
        }

        $validated = $request->validate($rules);

        if (isset($validated['currency'])) {
            $validated['currency'] = strtolower($validated['currency']);
        }

        AppSettings::putMany($validated);

        return back()->with('success', 'Settings saved.');
    }
}
