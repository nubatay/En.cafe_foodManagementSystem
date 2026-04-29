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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'price' => 'required|numeric|min:0.01',
                'stock' => 'required|integer|min:0',
                'category' => 'required|string|max:255',
                'options' => 'nullable|string',
            ]);

            if (empty(trim($validated['name']))) {
                throw new \Exception('Food name cannot be empty.');
            }

            if ((float) $validated['price'] <= 0) {
                throw new \Exception('Price must be greater than 0.');
            }

            if ((int) $validated['stock'] < 0) {
                throw new \Exception('Stock cannot be negative.');
            }

            if (empty(trim($validated['category']))) {
                throw new \Exception('Category cannot be empty.');
            }

            $validated['options'] = null;

            if ($request->filled('options')) {
                $parsedOptions = [];
                $optionsInput = trim($request->options);

                foreach (explode(',', $optionsInput) as $item) {
                    $item = trim($item);
                    if (empty($item)) continue;

                    if (!str_contains($item, ':')) {
                        throw new \Exception('Invalid option format. Use "OptionName:price" (e.g., "Hot:0,Iced:10")');
                    }

                    $parts = array_map('trim', explode(':', $item, 2));
                    if (count($parts) !== 2 || empty($parts[0]) || !is_numeric($parts[1])) {
                        throw new \Exception('Invalid option format. Use "OptionName:price" (e.g., "Hot:0,Iced:10")');
                    }

                    $parsedOptions[] = [
                        'name' => $parts[0],
                        'price' => (float) $parts[1],
                    ];
                }

                $validated['options'] = !empty($parsedOptions) ? $parsedOptions : null;
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if (!$file->isValid()) {
                    throw new \Exception('Image upload failed. Please try again.');
                }
                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                    throw new \Exception('Image must be JPG, PNG, or WebP format.');
                }
                if ($file->getSize() > 2048 * 1024) {
                    throw new \Exception('Image size cannot exceed 2MB.');
                }
                $validated['image'] = $file->store('foods', 'public');
            }

            FoodItem::create([
                ...$validated,
                'is_available' => true,
            ]);

            return redirect()->route('foods.index')
                ->with('success', 'Food added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    public function edit(FoodItem $food)
    {
        return view('foods.edit', compact('food'));
    }


    public function update(Request $request, FoodItem $food)
    {
        try {
            if (!$food) {
                throw new \Exception('Food item not found.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'price' => 'required|numeric|min:0.01',
                'stock' => 'required|integer|min:0',
                'category' => 'required|string|max:255',
                'options' => 'nullable|string',
            ]);

            if (empty(trim($validated['name']))) {
                throw new \Exception('Food name cannot be empty.');
            }

            if ((float) $validated['price'] <= 0) {
                throw new \Exception('Price must be greater than 0.');
            }

            if ((int) $validated['stock'] < 0) {
                throw new \Exception('Stock cannot be negative.');
            }

            if (empty(trim($validated['category']))) {
                throw new \Exception('Category cannot be empty.');
            }

            $validated['options'] = null;

            if ($request->filled('options')) {
                $parsedOptions = [];
                $optionsInput = trim($request->options);

                foreach (explode(',', $optionsInput) as $item) {
                    $item = trim($item);
                    if (empty($item)) continue;

                    if (!str_contains($item, ':')) {
                        throw new \Exception('Invalid option format. Use "OptionName:price" (e.g., "Hot:0,Iced:10")');
                    }

                    $parts = array_map('trim', explode(':', $item, 2));
                    if (count($parts) !== 2 || empty($parts[0]) || !is_numeric($parts[1])) {
                        throw new \Exception('Invalid option format. Use "OptionName:price" (e.g., "Hot:0,Iced:10")');
                    }

                    $parsedOptions[] = [
                        'name' => $parts[0],
                        'price' => (float) $parts[1],
                    ];
                }

                $validated['options'] = !empty($parsedOptions) ? $parsedOptions : null;
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if (!$file->isValid()) {
                    throw new \Exception('Image upload failed. Please try again.');
                }
                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                    throw new \Exception('Image must be JPG, PNG, or WebP format.');
                }
                if ($file->getSize() > 2048 * 1024) {
                    throw new \Exception('Image size cannot exceed 2MB.');
                }
                $validated['image'] = $file->store('foods', 'public');
            }

            $food->update($validated);

            return redirect()->route('foods.index')
                ->with('success', 'Food updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
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
