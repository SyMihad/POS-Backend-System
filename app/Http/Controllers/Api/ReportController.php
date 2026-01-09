<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dailySales()
    {
        $today = Carbon::today();

        $totalSales = Order::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $totalOrders = Order::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->count();

        return response()->json([
            'date' => $today->toDateString(),
            'total_orders' => $totalOrders,
            'total_sales' => $totalSales,
        ]);
    }

    public function topProducts()
    {
        $products = OrderItem::select(
                'product_id',
                DB::raw('SUM(qty) as total_sold')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'paid')
            ->where('orders.tenant_id', auth()->user()->tenant_id)
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->with('product:id,name')
            ->get();

        return response()->json($products);
    }

    public function lowStock()
    {
        $products = Product::where('stock', '<=', 5)
            ->select('id', 'name', 'stock')
            ->orderBy('stock')
            ->get();

        return response()->json($products);
    }

}
