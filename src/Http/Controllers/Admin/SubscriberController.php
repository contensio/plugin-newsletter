<?php

/**
 * Newsletter — Contensio plugin.
 * https://contensio.com
 *
 * @copyright   Copyright (c) 2026 Iosif Gabriel Chimilevschi
 * @license     https://www.gnu.org/licenses/agpl-3.0.txt  AGPL-3.0-or-later
 */

namespace Contensio\Newsletter\Http\Controllers\Admin;

use Contensio\Newsletter\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = trim($request->query('search', ''));

        $query = Subscriber::query()->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                  ->orWhere('name',  'like', '%' . $search . '%');
            });
        }

        $subscribers = $query->paginate(50)->withQueryString();

        $counts = [
            'all'          => Subscriber::count(),
            'active'       => Subscriber::where('status', 'active')->count(),
            'pending'      => Subscriber::where('status', 'pending')->count(),
            'unsubscribed' => Subscriber::where('status', 'unsubscribed')->count(),
        ];

        return view('newsletter::admin.subscribers', compact('subscribers', 'counts', 'status', 'search'));
    }

    public function destroy(int $id)
    {
        Subscriber::findOrFail($id)->delete();

        return back()->with('success', 'Subscriber deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $status = $request->query('status', 'all');

        $query = Subscriber::query()->orderBy('email');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Email', 'Name', 'Status', 'Subscribed at', 'Confirmed at', 'Unsubscribed at', 'Source']);

            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->email,
                        $row->name ?? '',
                        $row->status,
                        $row->created_at?->toDateTimeString() ?? '',
                        $row->confirmed_at?->toDateTimeString() ?? '',
                        $row->unsubscribed_at?->toDateTimeString() ?? '',
                        $row->source ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
