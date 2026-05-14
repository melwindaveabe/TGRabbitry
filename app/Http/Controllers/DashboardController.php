<?php

namespace App\Http\Controllers;

use App\Models\OrderDetails;
use App\Models\User;
use App\Models\Rating;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Rabbit;

class DashboardController
{
    public function product(){
        return inertia('Product/Inventory', [
            'low_stocks' => \App\Models\Product::query()
                ->selectRaw('name,case when category="Rabbit" then count(*) else stock end as stock')
                ->whereNotNull('price')
                ->where('stock', '<', 11)
                ->groupBy('name', 'stock', 'category')
                ->get()
        ]);
    }


    public function salesReport(){
        return inertia('Product/Report', [
            'header' => asset('images/website/header.png'),
            'logo' => asset('images/website/logo.jpeg'),
        ]);
    }
    
    public function index(){
        $categories = Category::all();
        foreach($categories as $cat){
            $cat->bal = (Budget::where('is_budget', 1)->where('category_id', $cat->id)->sum('amount') ?? 0) - 
                (Budget::where('is_budget', 0)->where('category_id', $cat->id)->sum('amount') ?? 0);
        }

        $m = [];
        $expenses = [];
        $t = 0;
        for ($i = 1; $i < 13; $i++){
            $c = 0;
            $h = OrderDetails::with(['product:id,price'])
                ->whereHas('order', function($q) use($i){
                    $q->whereMonth('created_at', $i)
                    ->where('status', 'Delivered')->where('paid', 1);
                })->get();
            foreach($h as $v){
                $c += ((int) $v->qty) * ((int) $v->product->price);
            }
            $t += $c;
            array_push($m, $c);

            array_push($expenses, Budget::where('is_budget', 0)->whereMonth('created_at', $i)->sum('amount'));
        }

        $rabbits = [];

        return inertia('Dashboard', [
            'recent_orders' => OrderDetails::sum('qty'),
            'todays_orders' => OrderDetails::whereHas('order', function($q){
                    $q->whereDate('created_at', date('Y-m-d'))
                        ->whereDate('paid', 1);
                })->sum('qty'),
            'users' => User::where('admin', 0)->count('*'),
            'ratings' => Rating::count('*'),
            'all_budget' => (Budget::where('is_budget', 1)->sum('amount') ?? 0) - 
                (Budget::where('is_budget', 0)->sum('amount') ?? 0),
            'todays_budgets' => Budget::where('is_budget', 1)->whereDate('created_at', date('Y-m-d'))->sum('amount') ?? 0,
            'todays_expenses' => Budget::where('is_budget', 0)->whereDate('created_at', date('Y-m-d'))->sum('amount') ?? 0,
            'categories' => $categories,
            'monthly_income' => $m,
            'monthly_expenses' => $expenses,
            'total_sales' => $t,
            // 'rabbits' => \App\Models\Product::selectRaw('count(*) as total, name')->where('Category', 'Rabbit')->groupBy('name')->get(),
            // 'for_sales' => Rabbit::selectRaw('count(*) as total')->where('status', 'For Sale')->first()->total,
            // 'for_restings' => Rabbit::selectRaw('count(*) as total')->where('status', 'For Resting')->first()->total,
        ]);
    }

    public function rabbit(){
        return inertia('Rabbit/Dashboard', [
            'title' => ['Sold', 'For Sale','Bundle','Breeder', 'Buck', 'Doe','Active Buck','Active Doe','Kits','Died','Resting','Sick'],
            'data' => [
                Rabbit::where('sold', 1)->count(),
                Rabbit::where('sold', 0)->where('status', 'For Sale')->count(),
                Rabbit::where('status', 'For Bundle')->count(),
                Rabbit::where('status', 'For Breeding')->count(),
                Rabbit::where('gender', 'Buck')->where('status', 'For Breeding')->count(),
                Rabbit::where('gender', 'Doe')->where('status', 'For Breeding')->count(),
                Rabbit::where('gender', 'Buck')->count(),
                Rabbit::where('gender', 'Doe')->count(),
                Rabbit::where('status', 'Kits')->count(),
                Rabbit::where('status', 'Died')->count(),
                Rabbit::where('status', 'For Resting')->count(),
                Rabbit::whereHas('illness', function($q){$q->where('status', '!=', 'Cured');})->count(),
            ]
            // 'data' => [
            //     'Sold' => Rabbit::where('sold', 1)->count(),
            //     'For Sale' => Rabbit::where('sold', 0)->where('status', 'For Sale')->count(),
            //     'Bundle' => Rabbit::where('status', 'For Bundle')->count(),
            //     'Breeder' => Rabbit::where('status', 'For Breeding')->count(),
            //     'Active Buck' => Rabbit::where('gender', 'Buck')->where('status', 'For Breeding')->count(),
            //     'Active Doe' => Rabbit::where('gender', 'Doe')->where('status', 'For Breeding')->count(),
            // ],
            // 'overall' => [
            //     'Buck' => Rabbit::where('gender', 'Buck')->count(),
            //     'Doe' => Rabbit::where('gender', 'Doe')->count(),
            //     'Kits' => Rabbit::where('status', 'Kits')->count(),
            //     'Died' => Rabbit::where('status', 'Died')->count(),
            //     'Resting' => Rabbit::where('status', 'For Resting')->count(),
            //     'Sick' => Rabbit::whereHas('illness', function($q){$q->where('status', '!=', 'Cured');})->count(),
            // ]
        ]);
    }
    
}
