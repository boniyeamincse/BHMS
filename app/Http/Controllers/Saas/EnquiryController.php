<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::latest();

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $enquiries = $query->paginate(15);

        return view('saas.enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry)
    {
        // Mark as read if it was unread
        if ($enquiry->status === 'unread') {
            $enquiry->update([
                'status' => 'read',
                'responded_at' => now()
            ]);
        }

        return view('saas.enquiries.show', compact('enquiry'));
    }

    public function markRead(Enquiry $enquiry)
    {
        $enquiry->update([
            'status' => 'read',
            'responded_at' => now()
        ]);

        return redirect()->route('saas.enquiries.index')
            ->with('success', 'Enquiry marked as read.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('saas.enquiries.index')
            ->with('success', 'Enquiry deleted successfully.');
    }
}