@extends('laradium::layouts.main', ['title' => 'Access denied'])

@section('content')
    <div class="alert alert-danger">You don't have permissions to access this page.</div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <h4>Accessible routes</h4>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Routes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(auth()->user()->role->getRoutes() as $route)
                        <tr>
                            <td>{{ $route }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection