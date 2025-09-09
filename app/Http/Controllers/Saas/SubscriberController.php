<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscriber::latest();

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('source') && $request->source !== '') {
            $query->where('source', $request->source);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $subscribers = $query->paginate(15);

        // Get unique sources for filter dropdown
        $sources = Subscriber::distinct('source')->pluck('source')->filter()->toArray();

        return view('saas.subscribers.index', compact('subscribers', 'sources'));
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('saas.subscribers.index')
            ->with('success', 'Subscriber deleted successfully.');
    }
}