<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query', $request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Поиск товаров
        $products = Product::with(['images', 'category', 'company'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%');
            })
            ->limit(8)
            ->get();

        $productResults = $products->map(function ($product) {
            $image = null;

            if ($product->images && $product->images->count() > 0) {
                $firstImage = $product->images->first();
                if ($firstImage && $firstImage->image_path) {
                    $imagePath = $firstImage->image_path;

                    if (strpos($imagePath, 'public/') === 0) {
                        $imagePath = substr($imagePath, 7);
                    }

                    $image = asset('storage/'.$imagePath);
                }
            }

            return [
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price,
                'image' => $image,
                'url' => route('products.show', $product->slug),
                'company' => $product->company?->name,
            ];
        });

        // Поиск компаний
        $companies = Company::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%')
                    ->orWhere('short_description', 'like', '%'.$query.'%');
            })
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->limit(4)
            ->get();

        $companyResults = $companies->map(function ($company) {
            return [
                'type' => 'company',
                'id' => $company->id,
                'name' => $company->name,
                'image' => $company->logo_url,
                'url' => route('companies.show', $company->slug),
                'products_count' => $company->products_count,
                'is_verified' => $company->is_verified,
            ];
        });

        // Объединяем результаты: сначала компании, потом товары
        $results = $companyResults->merge($productResults);

        return response()->json($results);
    }
}
