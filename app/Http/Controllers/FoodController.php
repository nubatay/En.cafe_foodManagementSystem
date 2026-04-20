<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function menu()
    {
        if (!session('table_number')) {
            return redirect()->route('customer.home')
                ->with('error', 'Please select your table number first.');
        }

        $foods = FoodItem::active()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('menu.index', compact('foods'));
    }

    
    public function index()
    {
        $foods = FoodItem::orderBy('category')
            ->orderBy('name')
            ->get();

        return view('foods.index', compact('foods'));
    }

 
    public function create()
    {
        return view('foods.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'options' => 'nullable|string',
        ]);

        $validated['options'] = null;

        if ($request->filled('options')) {
            $parsedOptions = [];

            foreach (explode(',', $request->options) as $item) {
                $parts = array_map('trim', explode(':', $item));

                if (count($parts) === 2) {
                    $parsedOptions[] = [
                        'name' => $parts[0],
                        'price' => (float) $parts[1],
                    ];
                }
            }

            $validated['options'] = !empty($parsedOptions) ? $parsedOptions : null;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('foods', 'public');
        }

        FoodItem::create([
            ...$validated,
            'is_available' => true,
        ]);

        return redirect()->route('foods.index')
            ->with('success', 'Food added successfully.');
    }


    public function edit(FoodItem $food)
    {
        return view('foods.edit', compact('food'));
    }


    public function update(Request $request, FoodItem $food)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'options' => 'nullable|string',
        ]);

        $validated['options'] = null;

        if ($request->filled('options')) {
            $parsedOptions = [];

            foreach (explode(',', $request->options) as $item) {
                $parts = array_map('trim', explode(':', $item));

                if (count($parts) === 2) {
                    $parsedOptions[] = [
                        'name' => $parts[0],
                        'price' => (float) $parts[1],
                    ];
                }
            }

            $validated['options'] = !empty($parsedOptions) ? $parsedOptions : null;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('foods', 'public');
        }

        $food->update($validated);

        return redirect()->route('foods.index')
            ->with('success', 'Food updated successfully.');
    }

  
    public function destroy(FoodItem $food)
    {
        $food->update([
            'is_available' => !$food->is_available,
        ]);

        $message = $food->is_available
            ? 'Food activated successfully.'
            : 'Food deactivated successfully.';

        return redirect()->route('foods.index')
            ->with('success', $message);
    }
}
