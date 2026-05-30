<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'customerCount' => User::whereHas('role', fn ($q) => $q->where('slug', 'customer'))->count(),
            'revenue' => Order::sum('total'),
            'recentOrders' => Order::with('user')->latest()->take(10)->get(),
        ]);
    }
}
