<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Preorder;
use App\Models\FarmProduce;
use Illuminate\Support\Facades\Auth;

class PreorderController extends Controller
{
    // ------------------------------
    // Manager: View all preorders
    // ------------------------------
    public function index()
    {
        $preorders = Preorder::with(['produce.farmer', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // dd($preorders);

        return view('manager.preorder.index', compact('preorders'));
       
    }

    // ------------------------------
    // Customer: View own preorders
    // ------------------------------
    public function customerIndex()
    {
        $preorders = Preorder::with('produce.farmer')
            ->where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.preorders.index', compact('preorders'));
    }

    // ------------------------------
    // Manager approves preorder
    // ------------------------------
    public function approve(Preorder $preorder)
    {
        if ($preorder->status !== 'pending') {
            return back()->withErrors('Only pending preorders can be approved.');
        }

        $produce = $preorder->produce;

        if ($preorder->quantity > $produce->reserved_quantity || $preorder->quantity > $produce->quantity) {
            return back()->withErrors('Not enough stock to approve this preorder.');
        }

        // Decrement stock and reserved quantity
        $produce->quantity -= $preorder->quantity;
        $produce->reserved_quantity -= $preorder->quantity;
        $produce->save();

        $preorder->status = 'approved';
        $preorder->save();

        return back()->with('success', 'Preorder approved.');
    }

    // ------------------------------
    // Manager rejects preorder
    // ------------------------------
    public function reject(Preorder $preorder)
    {
        if ($preorder->status !== 'pending') {
            return back()->withErrors('Only pending preorders can be rejected.');
        }

        $produce = $preorder->produce;

        // Release reserved quantity
        $produce->reserved_quantity -= $preorder->quantity;
        $produce->save();

        $preorder->status = 'rejected';
        $preorder->save();

        return back()->with('success', 'Preorder rejected.');
    }

    // ------------------------------
    // Customer cancels preorder
    // ------------------------------
    public function cancel(Preorder $preorder)
    {
        if ($preorder->customer_id !== Auth::id()) {
            abort(403);
        }

        if ($preorder->status !== 'pending') {
            return back()->withErrors('Only pending preorders can be cancelled.');
        }

        $produce = $preorder->produce;

        // Release reserved quantity
        $produce->reserved_quantity -= $preorder->quantity;
        $produce->save();

        $preorder->status = 'rejected';
        $preorder->save();

        return back()->with('success', 'Preorder cancelled.');
    }
}
