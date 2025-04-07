@extends('admin.layouts.app')
@section('main')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Contact</h4>
                    <a class="btn btn-sm btn-danger" href="{{ route('contact.trash') }}">Trash Contacts <i
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
                                    @if ($form_type == 'create')
                                        <th>Created At</th>
                                    @endif
                                    @if ($form_type == 'edit')
                                        <th>Updated At</th>
                                    @endif
                                    <th>Created By</th>
                                    <th>Updated By</th>
                                    {{-- <th>Status</th> --}}
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
                                            @if ($form_type == 'create')
                                                <td>{{ $user->created_at->diffForHumans() }}</td>
                                            @endif
                                            @if ($form_type == 'edit')
                                                <td>{{ $user->updated_at->diffForHumans() }}</td>
                                            @endif
                                            <td>{{ $user->created_by }}</td>
                                            <td>{{ $user->updated_by }}</td>

                                            {{-- <td>
                                                @if ($user->status)
                                                    <span class="badge badge-success">Active User</span>
                                                    @if (Auth::guard('admin')->user()->role->name == 'Admin')
                                                        <a class="text-danger"
                                                            href="{{ route('admin.status.update', $user->id) }}"><i
                                                                class="fa fa-times" aria-hidden="true"></i></a>
                                                    @else
                                                    @endif
                                                @else
                                                    <span class="badge badge-danger">Blocked User</span>
                                                    @if (Auth::guard('admin')->user()->role->name == 'Admin')
                                                        <a class="text-success"
                                                            href="{{ route('admin.status.update', $user->id) }}"><i
                                                                class="fa fa-check" aria-hidden="true"></i></a>
                                                    @else
                                                    @endif
                                                @endif
                                            </td> --}}
                                            <td>
                                                {{-- <a class="btn btn-sm btn-info" href=""><i class="fa fa-eye"
                                            aria-hidden="true"></i></a> --}}
                                                <a class="btn btn-sm btn-warning"
                                                    href="{{ route('contact.edit', $user->id) }}"><i class="fa fa-edit"
                                                        aria-hidden="true"></i></a>
                                                @if ($form_type == 'create')
                                                    {{-- <form class="d-inline delete-form"
                                        action="{{ route('admin-user.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"
                                                aria-hidden="true"></i></button>
                                    </form> --}}
                                                    <a class="btn btn-sm btn-danger"
                                                        href="{{ route('contact.trash.update', $user->id) }}"><i
                                                            class="fa fa-trash" aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td class="text-danger text-center" colspan="9">No Data Found</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if ($form_type == 'create')
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add new contact</h4>
                    </div>
                    @include('validate')
                    <div class="card-body">
                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="form-group order">
                                <label>Name</label>
                                <input name="name" type="text" value="{{ old('name') }}" class="form-control"
                                    autofocus>
                            </div>
                            <div class="form-group order">
                                <label>Email</label>
                                <input name="email" type="email" value="{{ old('email') }}" class="form-control"
                                    autofocus>
                            </div>
                            <div class="form-group order">
                                <label>Phone</label>
                                <input name="phone" type="text" value="{{ old('phone') }}" class="form-control"
                                    autofocus>
                            </div>
                            <div class="form-group order">
                                <label>Designation</label>
                                <input name="designation" type="text" value="{{ old('designation') }}"
                                    class="form-control" autofocus>
                            </div>
                            <div class="form-group order">
                                <label>Organization</label>
                                <input name="organization" type="text" value="{{ old('organization') }}"
                                    class="form-control" autofocus>
                            </div>
                            <div class="form-group order">
                                <label>Address</label>
                                <input name="address" type="text" value="{{ old('address') }}" class="form-control"
                                    autofocus>
                            </div>


                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
            @if ($form_type == 'edit')
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit contact</h4>
                    </div>
                    @include('validate')
                    <div class="card-body">
                        <form action="{{ route('contact.update', $edit->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" value="{{ $edit->name }}" type="text" class="form-control"
                                    autofocus>
                            </div>
                            <div class="form-group">
                                <label>Email <small class="text-danger"></label>
                                <input name="email" value="{{ $edit->email }}" type="text" class="form-control"
                                    autofocus>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input name="phone" value="{{ $edit->phone }}" type="text" class="form-control"
                                    autofocus>
                            </div>
                            <div class="form-group">
                                <label>Designation</label>
                                <input name="designation" value="{{ $edit->designation }}" type="text"
                                    class="form-control" autofocus>
                            </div>
                            <div class="form-group">
                                <label>Organiation</label>
                                <input name="organization" value="{{ $edit->organization }}" type="text"
                                    class="form-control" autofocus>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input name="address" value="{{ $edit->address }}" type="text" class="form-control"
                                    autofocus>
                            </div>

                            <div class="text-right">
                                <a class="btn btn-info" href="{{ route('contact.index') }}">Back</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
