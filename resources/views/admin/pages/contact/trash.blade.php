@extends('admin.layouts.app')
@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Trash Users</h4>
                    <a class="btn btn-sm btn-success" href="{{ route('contact.index') }}">Active Contacts <i
                            class="fa fa-arrow-right ml-2" aria-hidden="true"></i></a>
                </div>
                @include('validate-main')
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Designation</th>
                                    <th>Organization</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($all_admin as $user)
                                    @if ($user->name !== 'Provider')
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->designation }}</td>
                                            <td>{{ $user->organization }}</td>
                                            <td>{{ $user->address }}</td>
                                            <td>
                                                {{-- <a class="btn btn-sm btn-info" href=""><i class="fa fa-eye"
                                            aria-hidden="true"></i></a> --}}
                                                <a class="btn btn-sm btn-info"
                                                    href="{{ route('contact.trash.update', $user->id) }}">Restore user</a>
                                                @if ($form_type == 'trash')
                                                    <form class="d-inline delete-form"
                                                        action="{{ route('contact.destroy', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">Delete forever</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td class="text-danger text-center" colspan="3">No Data Found</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
