@extends('admin.layouts.app')

@section('main')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Contact</h4>
                    <a class="btn btn-sm btn-danger" href="{{ route('contact.trash') }}">
                        Trash Contacts <i class="fa fa-arrow-right ml-2" aria-hidden="true"></i>
                    </a>
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
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="contactTableBody">
                                @forelse ($all_admin as $user)
                                    @if ($user->name !== 'Provider')
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
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
                                            <td>
                                                @foreach ($user->contactTypes as $contactType)
                                                    <span class="badge badge-info">{{ $contactType->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <!-- View Button to Open Modal -->
                                                <button class="btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#contactModal{{ $user->id }}">
                                                    <i class="fa fa-eye"></i>
                                                </button>

                                                <a class="btn btn-sm btn-warning"
                                                    href="{{ route('contact.edit', $user->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                @if ($form_type == 'create')
                                                    <a class="btn btn-sm btn-danger"
                                                        href="{{ route('contact.trash.update', $user->id) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                @endif

                                                <a class="btn btn-sm btn-success"
                                                    href="{{ route('contact.logs', $user->id) }}" target="_blank"
                                                    @if (Auth::guard('admin')->user()->role->name !== 'Super Admin') style="display:none;" @endif>
                                                    <i class="fa fa-file-text"></i>
                                                </a>

                                                <a class="btn btn-sm btn-primary"
                                                    href="{{ route('contact.print', $user->id) }}" target="_blank">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        @include('admin.pages.contact.modal')
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-danger">No Data Found</td>
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
                        <form id="addContactForm" action="{{ route('contact.store') }}" method="POST">
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

                            <!-- Display existing Contact Types with checkboxes -->
                            <div id="contactTypeCheckboxList" class="d-flex flex-wrap align-items-start">
                                @foreach ($contactTypes as $type)
                                    <div class="form-check mr-3">
                                        <input type="checkbox" name="contact_type_id[]" value="{{ $type->id }}"
                                            class="form-check-input" id="type_{{ $type->id }}">
                                        <label class="form-check-label"
                                            for="type_{{ $type->id }}">{{ $type->name }}</label>
                                    </div>
                                @endforeach
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
                        <h4 class="card-title">Edit Contact</h4>
                    </div>
                    @include('validate')
                    <div class="card-body">
                        <form action="{{ route('contact.update', $edit->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Other contact fields like name, email, etc. -->
                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" value="{{ $edit->name }}" type="text" class="form-control"
                                    autofocus>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input name="email" value="{{ $edit->email }}" type="email" class="form-control"
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
                                <label>Organization</label>
                                <input name="organization" value="{{ $edit->organization }}" type="text"
                                    class="form-control" autofocus>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input name="address" value="{{ $edit->address }}" type="text" class="form-control"
                                    autofocus>
                            </div>

                            <!-- Contact Types -->
                            <div class="form-group">
                                <label>Contact Types</label>
                                <div class="d-flex flex-wrap">
                                    @foreach ($contactTypes as $type)
                                        <div class="form-check mr-3">
                                            <input type="checkbox" name="contact_type_id[]" value="{{ $type->id }}"
                                                class="form-check-input" id="type_{{ $type->id }}"
                                                {{ $edit->contactTypes->contains($type) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="type_{{ $type->id }}">
                                                {{ $type->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="text-right">
                                <a class="btn btn-info" href="{{ route('contact.index') }}">Back</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif


            <!-- Add new contact type -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add New Contact Type</h4>
                </div>
                <div class="card-body">
                    <form id="addContactTypeForm" action="{{ route('contact-type-ajax') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="contactTypeName">New Type Name</label>
                            <input name="name" id="contactTypeName" type="text" class="form-control" 
                                autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Type</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
