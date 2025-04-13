@extends('admin.layouts.app')

@push('styles')
    <!-- Font Awesome 4.7 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .hover-shadow:hover {
            box-shadow: 0 0.75rem 1.25rem rgba(0, 0, 0, .1);
            transform: translateY(-5px);
        }

        .transition {
            transition: all 0.3s ease-in-out;
        }

        .card h2 {
            font-weight: 700;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #343a40;
        }

        .icon-title {
            display: inline-block;
            margin-right: 0.5rem;
        }
    </style>
@endpush

@section('main')
    @php
        use App\Models\ContactTypes;
        use App\Models\Admin;

        $contactTypes = ContactTypes::withCount('contacts')->orderBy('name')->get();
        $adminCount = Admin::count();
        $activeAdmins = Admin::where('status', true)->count();
        $blockedAdmins = Admin::where('status', false)->count();

        $hasAdminAccess = hasPermission('admin-user') || hasPermission('role') || hasPermission('permission');
        $hasContactAccess = hasPermission('contact');
    @endphp

    @if ($hasAdminAccess || $hasContactAccess)
        {{-- 👤 Admin Summary Section --}}
        @if ($hasAdminAccess)
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="section-title">
                        <i class="fa fa-user icon-title"></i> Admin Overview
                    </h4>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="{{ route('admin-user.index') }}" class="text-decoration-none text-reset">
                        <div class="card hover-shadow transition border-0 text-center">
                            <div class="card-body py-4">
                                <i class="fa fa-users fa-2x text-primary mb-3"></i>
                                <h5 class="mb-2">Total Admins</h5>
                                <h2 class="text-primary">{{ $adminCount }}</h2>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="{{ route('admin-user.index') }}" class="text-decoration-none text-reset">
                        <div class="card hover-shadow transition border-0 text-center">
                            <div class="card-body py-4">
                                <i class="fa fa-check-circle fa-2x text-success mb-3"></i>
                                <h5 class="mb-2">Active Admins</h5>
                                <h2 class="text-success">{{ $activeAdmins }}</h2>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="{{ route('admin.trash') }}" class="text-decoration-none text-reset">
                        <div class="card hover-shadow transition border-0 text-center">
                            <div class="card-body py-4">
                                <i class="fa fa-ban fa-2x text-danger mb-3"></i>
                                <h5 class="mb-2">Blocked Admins</h5>
                                <h2 class="text-danger">{{ $blockedAdmins }}</h2>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif

        {{-- 📊 Contact Types Section --}}
        @if ($hasContactAccess)
            <div class="row mb-5">
                <div class="col-12">
                    <h4 class="section-title">
                        <i class="fa fa-address-book icon-title"></i> Contacts by Type
                    </h4>
                </div>

                @if ($contactTypes->count() > 0)
                    @foreach ($contactTypes as $contactType)
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('contact.index', ['type' => $contactType->name]) }}"
                                class="text-decoration-none text-reset">
                                <div class="card hover-shadow transition border-0 text-center">
                                    <div class="card-body py-4">
                                        <i class="fa fa-address-book fa-2x text-dark mb-3"></i>
                                        <h5 class="mb-2 text-dark">{{ $contactType->name }}</h5>
                                        <h2 class="text-primary">{{ $contactType->contacts_count }}</h2>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center text-muted">
                        <i class="fa fa-info-circle"></i> No contact types found.
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="alert alert-warning text-center">
            <i class="fa fa-exclamation-triangle text-warning mr-2"></i>
            🚫 You don't have permission to view the dashboard content. Please contact the admin for access.
        </div>
    @endif
@endsection
