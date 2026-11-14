<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enquiry;
use App\Models\Service;
use Illuminate\Http\Request;

class EnquiryController extends AdminController
{
    protected function filtered(Request $request)
    {
        return Enquiry::query()
            ->when($request->filled('spam'), fn ($q) => $q->where('is_spam', true), fn ($q) => $q->where('is_spam', false))
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('full_name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%")->orWhere('company_name', 'like', "%$s%")))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->service, fn ($q, $s) => $q->where('service_id', $s))
            ->when($request->from, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest();
    }

    public function index(Request $request)
    {
        return view('admin.enquiries.index', [
            'enquiries' => $this->filtered($request)->paginate(20)->withQueryString(),
            'services' => Service::ordered()->get(['id', 'title']),
            'spamCount' => Enquiry::where('is_spam', true)->count(),
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        if (! $enquiry->read_at) {
            $enquiry->update(['read_at' => now()]);
        }

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Enquiry::STATUSES))],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $data['is_spam'] = $request->boolean('is_spam');
        $enquiry->update($data);
        $this->log('update', "Updated enquiry #{$enquiry->id} ({$enquiry->status})", $enquiry);

        return back()->with('success', 'Enquiry updated.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();
        $this->log('delete', "Deleted enquiry #{$enquiry->id}");

        return redirect()->route('admin.enquiries.index')->with('success', 'Enquiry deleted.');
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'], 'ids.*' => ['integer'],
            'action' => ['required', 'in:new,contacted,converted,closed,spam,not_spam,delete'],
        ]);
        $q = Enquiry::whereIn('id', $data['ids']);
        match ($data['action']) {
            'spam' => $q->update(['is_spam' => true]),
            'not_spam' => $q->update(['is_spam' => false]),
            'delete' => $q->delete(),
            default => $q->update(['status' => $data['action']]),
        };
        $this->log('bulk', 'Bulk '.$data['action'].' on '.count($data['ids']).' enquiries');

        return back()->with('success', 'Bulk action applied.');
    }

    /** CSV export (opens in Excel) honouring the current filters. */
    public function export(Request $request)
    {
        $rows = $this->filtered($request)->get();
        $this->log('export', 'Exported '.$rows->count().' enquiries');

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            fputcsv($out, ['ID', 'Date', 'Name', 'Company', 'Phone', 'Email', 'Service', 'Workforce Size', 'Location', 'Subject', 'Message', 'Status', 'Notes', 'Source']);
            foreach ($rows as $e) {
                $cells = [$e->id, $e->created_at->format('Y-m-d H:i'), $e->full_name, $e->company_name, $e->phone, $e->email, $e->service_name,
                    $e->workforce_size, $e->location, $e->subject, $e->message, $e->status, $e->notes, $e->source_url];
                // Prevent CSV formula injection
                fputcsv($out, array_map(fn ($v) => is_string($v) && preg_match('/^[=+\-@]/', $v) ? "'".$v : $v, $cells));
            }
            fclose($out);
        }, 'enquiries-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
