@extends('admin.layouts.app')

@section('main')
    <div class="container mt-4">
        <div class="card shadow-sm rounded">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Import Contacts</h4>
                    <a href="{{ route('contacts.downloadTemplate') }}" class="btn btn-outline-primary">
                        Download Demo Excel
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="file">Select Excel File:</label>
                        <input type="file" name="file" id="file" class="form-control-file" required>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        Start Import
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
