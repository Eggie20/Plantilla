@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Locked Accounts</h1>
        
        <div class="bg-white rounded-lg shadow p-6">
            @if(count($lockedAccounts) === 0)
                <p class="text-gray-600">No accounts are currently locked.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Locked Until</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($lockedAccounts as $account)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->username }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->ip_address }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($account->locked_until)->format('F j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('auth.unlock') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="username" value="{{ $account->username }}">
                                        <input type="hidden" name="ip_address" value="{{ $account->ip_address }}">
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900">Unlock</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
