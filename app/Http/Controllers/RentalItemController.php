<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RentalItem;
use Illuminate\Support\Facades\Auth;

class RentalItemController extends Controller
{
    public function index(Request $request)
    {
        $query = RentalItem::query()->where('status', 'approved');
        
        // Filtering
        if ($request->has('location')) {
            $query->where('location', 'like', '%'.$request->location.'%');
        }
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:home,room,cabana,hotel,car',
            'category' => 'required|in:vehicle,residence,other',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        $rentalItem = RentalItem::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'category' => $request->category,
            'price' => $request->price,
            'location' => $request->location,
            'amenities' => $request->amenities,
            'images' => $request->images,
            'status' => Auth::user()->hasRole('admin') ? 'approved' : 'pending',
        ]);

        return response()->json($rentalItem, 201);
    }

    public function pendingItems()
    {
        
        return RentalItem::where('status', 'pending')->paginate(10);
    }

    public function updateStatus(Request $request, RentalItem $rentalItem)
    {        
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);
        
        $rentalItem->update(['status' => $request->status]);
        
        return response()->json($rentalItem);
    }
}
