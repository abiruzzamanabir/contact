@extends('admin.layouts.app')
@section('main')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Contact Types</h4>
                </div>
                @include('validate-main')
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    @if ($form_type == 'create')
                                        <th>Created At</th>
                                    @endif
                                    @if ($form_type == 'edit')
                                        <th>Updated At</th>
                                    @endif
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="contactTypeTableBody">
                                @forelse ($all_contacttypes as $per)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $per->name }}</td>
                                        @if ($form_type == 'create')
                                            <td>{{ $per->created_at->diffForHumans() }}</td>
                                        @endif
                                        @if ($form_type == 'edit')
                                            <td>{{ $per->updated_at->diffForHumans() }}</td>
                                        @endif
                                        <td>
                                            {{-- <a class="btn btn-sm btn-warning"
                                                href="{{ route('contact-type.edit', $per->id) }}"><i
                                                    class="fa fa-edit"></i></a> --}}
                                            <button type="button" class="btn btn-sm btn-warning edit-contact-type-btn"
                                                data-id="{{ $per->id }}" data-name="{{ $per->name }}">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            @if ($form_type == 'create')
                                                <form class="d-inline ajax-delete-form" data-id="{{ $per->id }}">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-danger text-center" colspan="5">No Data Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Side Form --}}
        <div class="col-md-4">
            @if ($form_type == 'create')
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add new contact type</h4>
                    </div>
                    <div class="card-body">
                        <form id="addContactTypeForm" action="{{ route('contact-type.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="text-dark">Name</label>
                                <input name="name" type="text" class="form-control" id="contactTypeName" autofocus>
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
                    <div class="card-body">
                        <form action="{{ route('contact-type.update', $edit->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="text-dark">Name</label>
                                <input name="name" value="{{ $edit->name }}" type="text" class="form-control"
                                    autofocus>
                            </div>

                            <div class="text-right">
                                <a class="btn btn-info" href="{{ route('contact-type.index') }}">Back</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Edit Contact Type Modal -->
    <div class="modal fade" id="editContactTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editContactTypeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit Contact Type</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            &times;
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editContactTypeId">
                        <div class="form-group">
                            <label class="text-dark">Name</label>
                            <input type="text" id="editContactTypeName" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
