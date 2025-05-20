@extends('Plantilla.Layouts.app')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Locked Accounts</h4>
                </div>
                <div class="card-body">
                    @if(count($lockedAccounts) === 0)
                        <div class="alert alert-info">
                            No locked accounts found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>IP Address</th>
                                        <th>Attempts</th>
                                        <th>Last Attempt</th>
                                        <th>Locked Until</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lockedAccounts as $account)
                                        <tr>
                                            <td>{{ $account->username }}</td>
                                            <td>{{ $account->ip_address }}</td>
                                            <td>{{ $account->total_attempts }}</td>
                                            <td>{{ $account->last_attempt_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $account->locked_until->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                <form action="{{ route('auth.unlock') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="username" value="{{ $account->username }}">
                                                    <input type="hidden" name="ip_address" value="{{ $account->ip_address }}">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to unlock this account?')">
                                                        Unlock
                                                    </button>
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
    </div>
</div>
@endsection
