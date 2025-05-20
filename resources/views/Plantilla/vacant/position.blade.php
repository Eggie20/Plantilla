@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Vacant Positions</h1>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item No</th>
                        <th>Office</th>
                        <th>Position</th>
                        <th>SG</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Unfunded</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions as $position)
                    <tr>
                        <td>{{ $position->item_no }}</td>
                        <td>{{ $position->office }}</td>
                        <td>{{ $position->position }}</td>
                        <td>{{ $position->sg }}</td>
                        <td>{{ $position->code }}</td>
                        <td>{{ $position->type }}</td>
                        <td>{{ $position->level }}</td>
                        <td>{{ $position->status }}</td>
                        <td>{{ $position->unfunded ? 'Yes' : 'No' }}</td>
                        <td class="text-center">
                            <a href="{{ route('positions.edit', $position->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('positions.destroy', $position->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection