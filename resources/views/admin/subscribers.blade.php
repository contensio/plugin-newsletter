@extends('contensio::admin.layout')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Newsletter</h1>
            <p class="mt-1 text-gray-500">Manage your email subscribers.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('contensio-newsletter.export', array_filter(['status' => $status !== 'all' ? $status : null])) }}"
               class="inline-flex items-center gap-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <i class="bi bi-download"></i>
                Export CSV
            </a>
            <a href="{{ route('contensio-newsletter.settings') }}"
               class="inline-flex items-center gap-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <i class="bi bi-gear"></i>
                Settings
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Total',         'count' => $counts['all'],          'status' => 'all',          'color' => 'text-gray-900'],
            ['label' => 'Active',        'count' => $counts['active'],       'status' => 'active',       'color' => 'text-green-700'],
            ['label' => 'Pending',       'count' => $counts['pending'],      'status' => 'pending',      'color' => 'text-amber-700'],
            ['label' => 'Unsubscribed',  'count' => $counts['unsubscribed'], 'status' => 'unsubscribed', 'color' => 'text-red-700'],
        ] as $stat)
        <a href="{{ route('contensio-newsletter.subscribers', ['status' => $stat['status'], 'search' => $search ?: null]) }}"
           class="bg-white border {{ $status === $stat['status'] ? 'border-ember-400 ring-1 ring-ember-400' : 'border-gray-200' }} rounded-xl p-5 hover:border-ember-300 transition-colors">
            <p class="text-sm text-gray-500 font-medium">{{ $stat['label'] }}</p>
            <p class="mt-1 text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['count']) }}</p>
        </a>
        @endforeach
    </div>

    {{-- Search + filter bar --}}
    <form method="GET" action="{{ route('contensio-newsletter.subscribers') }}" class="flex gap-3 mb-4">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="relative flex-1 max-w-md">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="bi bi-search text-sm"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by email or name…"
                   class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ember-400">
        </div>
        <button type="submit"
                class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Search
        </button>
        @if($search)
        <a href="{{ route('contensio-newsletter.subscribers', ['status' => $status]) }}"
           class="border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-500 text-sm px-3 py-2 rounded-lg transition-colors">
            Clear
        </a>
        @endif
    </form>

    {{-- Subscriber table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @if($subscribers->isEmpty())
        <div class="py-16 text-center text-gray-400">
            <i class="bi bi-envelope text-4xl mb-3 block"></i>
            <p class="text-lg font-medium">No subscribers found</p>
            @if($search)
            <p class="text-sm mt-1">Try a different search term.</p>
            @elseif($status !== 'all')
            <p class="text-sm mt-1">No {{ $status }} subscribers yet.</p>
            @else
            <p class="text-sm mt-1">Add a subscribe form to your site to start collecting emails.</p>
            @endif
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Name</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Subscribed</th>
                    <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Source</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($subscribers as $sub)
                @php
                    $badgeClass = match($sub->status) {
                        'active'       => 'bg-green-50 text-green-700 border border-green-200',
                        'pending'      => 'bg-amber-50 text-amber-700 border border-amber-200',
                        'unsubscribed' => 'bg-red-50 text-red-700 border border-red-200',
                        default        => 'bg-gray-100 text-gray-600',
                    };
                    $badgeLabel = match($sub->status) {
                        'active'       => 'Active',
                        'pending'      => 'Pending',
                        'unsubscribed' => 'Unsubscribed',
                        default        => $sub->status,
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $sub->email }}</td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $sub->name ?: '-' }}</td>
                    <td class="px-5 py-3.5">
                        <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">
                        {{ $sub->created_at->format('M j, Y') }}
                    </td>
                    <td class="px-5 py-3.5 text-gray-400 capitalize">{{ $sub->source ?: '-' }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <form method="POST" action="{{ route('contensio-newsletter.subscriber.delete', $sub->id) }}"
                              onsubmit="return confirm('Delete {{ $sub->email }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-gray-400 hover:text-red-600 transition-colors"
                                    title="Delete subscriber">
                                <i class="bi bi-trash text-base"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($subscribers->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $subscribers->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
