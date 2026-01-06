<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyFollow;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Список всех компаний
     */
    public function index(Request $request)
    {
        $query = Company::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->withCount('followers');

        // Поиск
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $sort = $request->input('sort', 'name');
        match ($sort) {
            'products' => $query->orderByDesc('products_count'),
            'followers' => $query->orderByDesc('followers_count'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderBy('name'),
        };

        $companies = $query->paginate(12);

        return view('companies.index', compact('companies'));
    }

    /**
     * Страница компании
     */
    public function show(string $slug)
    {
        $company = Company::where('slug', $slug)
            ->where('is_active', true)
            ->withCount('followers')
            ->firstOrFail();

        $products = $company->products()
            ->where('is_active', true)
            ->with(['images', 'category'])
            ->orderByDesc('created_at')
            ->paginate(12);

        $isFollowing = auth()->check() && $company->isFollowedBy(auth()->user());

        return view('companies.show', compact('company', 'products', 'isFollowing'));
    }

    /**
     * Подписаться/отписаться от компании
     */
    public function toggleFollow(Request $request, Company $company)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Нельзя подписаться на свою компанию
        if ($company->user_id === $user->id) {
            return response()->json(['error' => 'Cannot follow your own company'], 400);
        }

        $follow = CompanyFollow::where('user_id', $user->id)
            ->where('company_id', $company->id)
            ->first();

        if ($follow) {
            $follow->delete();
            $isFollowing = false;
        } else {
            CompanyFollow::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
            ]);
            $isFollowing = true;
        }

        return response()->json([
            'success' => true,
            'is_following' => $isFollowing,
            'followers_count' => $company->followers()->count(),
        ]);
    }
}
