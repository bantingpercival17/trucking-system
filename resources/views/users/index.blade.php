@extends('layouts.owner')

@section('title', 'Users')

@section('content')

    {{-- TOAST (SUCCESS / ERROR) --}}
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">


        {{-- SUCCESS TOAST --}}
        @if (session('success'))
            <div id="toastSuccess" class="toast align-items-center text-bg-success border-0 shadow-sm" role="alert"
                aria-live="assertive" aria-atomic="true" data-bs-delay="2500">
                <div class="d-flex">
                    <div class="toast-body py-2">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        {{-- ERROR TOAST --}}
        @if ($errors->any())
            <div id="toastError" class="toast text-bg-danger border-0 shadow-sm" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="4000">
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Please fix the errors</strong>
                    <button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
                <div class="toast-body py-2">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li style="font-size: 0.9rem;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

    </div>


    <div class="ui-hero p-3 p-lg-4 mb-3 mb-lg-4">

        <h4 class="mb-1 fw-bold">Users</h4>

        <div class="text-muted small">
            Manage system users and their roles.
        </div>

    </div>

    <div class="card ui-card border-0">

        {{-- CARD HEADER --}}
        <div class="card-header bg-transparent border-0 pb-0">

            <div
                class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-2">

                {{-- LEFT --}}
                <div>
                    <h6 class="mb-0 fw-semibold">User List</h6>
                    <div class="text-muted small ui-showing">
                        Showing {{ $users->count() }} users
                    </div>
                </div>

                {{-- RIGHT CONTROLS --}}
                <div class="d-flex flex-column flex-md-row gap-2 w-100 justify-content-lg-end align-items-lg-center">

    <form method="GET"
    action="{{ route('owner.users.index') }}"
    class="d-flex gap-2 align-items-center">

        <div class="ui-search" style="width: 350px;">
            <i class="bi bi-search ui-search-icon"></i>

            <input type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control ui-search-input"
                placeholder="Search users...">
        </div>

        @if (request('search'))
            <a href="{{ route('owner.users.index') }}"
                class="btn btn-outline-secondary ui-pill-btn">
                Clear
            </a>
        @endif

    </form>

   <button type="button"
    class="btn btn-success ui-pill-btn ui-btn-wide"
        data-bs-toggle="modal"
        data-bs-target="#addUserModal">

        <i class="bi bi-plus-lg me-1"></i> Add User
    </button>

</div>

            </div>

            <div class="ui-divider mt-3"></div>

        </div>
        
        <div class="d-block d-lg-none">

    @forelse($users as $user)

        <div class="card border-0 shadow-sm mb-3 ui-mobile-person">
<div class="card-body p-3">

                {{-- NAME --}}
               <div class="ui-person-head text-center mb-3">

    <div class="ui-person-name fw-bold">
        {{ $user->name }}
    </div>

    <div class="ui-person-email text-muted small">
        {{ $user->email }}
    </div>

</div>

                {{-- META --}}
                <div class="ui-person-meta-grid mt-2">

    <div>
        <div class="ui-meta-label">Role</div>
        <div class="badge bg-secondary w-100 text-center">
            {{ ucfirst($user->role) }}
        </div>
    </div>

    <div>
        <div class="ui-meta-label">Created</div>
        <div class="text-muted small text-center">
            {{ $user->created_at?->format('Y-m-d') }}
        </div>
    </div>

    <div class="grid-span-2">
        <div class="ui-meta-label">Password</div>
        <div class="badge bg-light text-dark w-100 text-center">
            {{ $user->password ? 'Set' : 'Not set' }}
        </div>
    </div>

</div>
                {{-- ACTIONS --}}
             <div class="ui-person-actions mt-3">

    <button class="btn btn-warning"
        data-bs-toggle="modal"
        data-bs-target="#editUserModal-{{ $user->id }}">
        ✏️ Edit
    </button>

    <form method="POST"
        action="{{ route('owner.users.destroy', $user->id) }}"
        onsubmit="return confirm('Delete this user?')">

        @csrf
        @method('DELETE')

        <button class="btn btn-danger">
            🗑️ Delete
        </button>

    </form>

</div>

            </div>
        </div>

    @empty
        <div class="text-center py-5 text-muted">
            No users found.
        </div>
    @endforelse

</div>
<div class="d-none d-lg-block">
        <div class="card-body pt-3">

            <div class="ui-table-wrap">

                <div class="ui-table-scroll">

                    <table class="table ui-table">

                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Created</th>
                                <th>Password</th>
                                <th style="width:140px;">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($users as $user)
                                <tr>

                                    <td class="fw-semibold">
                                        {{ $user->name }}
                                    </td>

                                    <td>
                                        <span class="ui-badge ui-badge-draft">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>

                                    <td>{{ $user->email }}</td>

                                    <td>
                                        {{ $user->created_at?->format('Y-m-d') }}
                                    </td>

                                    <td>
                                        {{ $user->password ? 'Set' : 'Not set' }}
                                    </td>

                                    <td>

                                        <div class="d-flex gap-1">

                                            {{-- EDIT --}}
                                            <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editUserModal-{{ $user->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            {{-- DELETE --}}
                                            <form method="POST" action="{{ route('owner.users.destroy', $user->id) }}"
                                                onsubmit="return confirm('Delete this user?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>



                            @empty

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
 </div>
    </div>

    @foreach ($users as $user)
        <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            Edit User
                        </h5>

                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <form method="POST" action="{{ route('owner.users.update', $user->id) }}">

                            @csrf
                            @method('PUT')

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input name="name" class="form-control" value="{{ old('name', $user->name) }}"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input name="email" type="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input name="password" type="password" class="form-control"
                                        placeholder="Leave blank to keep current">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input name="password_confirmation" type="password" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Role</label>

                                    <select name="role" class="form-select">

                                        <option value="owner" {{ $user->role == 'owner' ? 'selected' : '' }}>
                                            Owner
                                        </option>

                                        <option value="admin"
                                            {{ $user->role == 'admin' ? 'selected' : '' }}>
                                            Admin
                                        </option>

                                        <option value="secretary"
                                            {{ $user->role == 'secretary' ? 'selected' : '' }}>
                                            Secretary
                                        </option>
                                        
                                        <option value="it"
                                            {{ $user->role == 'it' ? 'selected' : '' }}>
                                            IT
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button class="btn btn-primary">
                                    Update User
                                </button>
                            </div>


                        </form>

                    </div>

                </div>

            </div>

        </div>
    @endforeach




    {{-- ADD USER MODAL --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form method="POST" action="{{ route('owner.users.store') }}">

                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input name="name" type="text" class="form-control" required
                                    value="{{ old('name') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-control" required
                                    value="{{ old('email') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input name="password" type="password" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input name="password_confirmation" type="password" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="owner" @selected(old('role') === 'owner')>Owner</option>
                                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                    <option value="secretary" @selected(old('role') === 'secretary')>Secretary</option>
                                    <option value="it" @selected(old('role') === 'it')>IT</option>
                                </select>

                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button class="btn btn-primary" type="submit">Save User</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // show toast success
            const toastSuccessEl = document.getElementById('toastSuccess');
            if (toastSuccessEl) {
                new bootstrap.Toast(toastSuccessEl).show();
            }

            // show toast error
            const toastErrorEl = document.getElementById('toastError');
            if (toastErrorEl) {
                new bootstrap.Toast(toastErrorEl).show();
            }

            // if there are validation errors, open modal too
            @if ($errors->any())
                var modal = new bootstrap.Modal(document.getElementById('addUserModal'));
                modal.show();
            @endif
        });
    </script>

@endsection

@push('styles')
    <style>
        /* ===== Shipments-like UI ===== */
        .ui-card {
            border-radius: 18px;
            box-shadow: 0 14px 40px rgba(16, 24, 40, .08);
            transition: all .25s ease;
        }

        .ui-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(16, 24, 40, .12);
        }

        .ui-hero {
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, .05);
            background:
                radial-gradient(900px 500px at 10% 10%, rgba(99, 102, 241, .10), transparent 60%),
                radial-gradient(900px 500px at 90% 20%, rgba(16, 185, 129, .10), transparent 60%),
                linear-gradient(135deg, #ffffff, #f9fafb);
            box-shadow: 0 20px 40px rgba(17, 24, 39, .06);
        }

        .ui-divider {
            height: 1px;
            background: #edf0f4;
            width: 100%;
        }

        /* Search */
        .ui-search {
            position: relative;
        }

        .ui-search-input {
            height: 42px;
            border-radius: 999px;
            padding-left: 40px;
            border: 1px solid #e6e8ec;
            background: #fafbfc;
            transition: .2s ease;
        }

        .ui-search-input:focus {
            background: #fff;
            border-color: #cfd6ff;
            box-shadow: 0 0 0 .20rem rgba(13, 110, 253, .10);
        }

        .ui-search-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #98a2b3;
            pointer-events: none;
        }

        /* pills */
        .ui-pill-btn {
            border-radius: 999px;
            padding: .45rem .90rem;
        }

        /* Make header buttons match input height */
        .ui-btn-wide,
        .ui-btn-equal {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.1rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .ui-btn-wide {
            min-width: 140px;
        }

        /* Table */
        .ui-table-wrap {
            border: 1px solid #edf0f4;
            border-radius: 16px;
            background: #fff;
        }

        .ui-table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 16px;
        }

        .ui-table {
            /* min-width removed to prevent horizontal scroll bar */
        }

        .ui-table thead th {
            background: #f8fafc;
            color: #667085;
            font-weight: 600;
            font-size: .80rem;
            letter-spacing: .02em;
            border-bottom: 1px solid #edf0f4 !important;
            padding: 14px 16px;
            white-space: nowrap;
        }

        .ui-table tbody td {
            padding: 14px 16px;
            border-top: 1px solid #f1f3f6 !important;
            vertical-align: middle;
        }

        .ui-table tbody tr:hover {
            background: #fafcff;
        }

        /* top pagination */
        .ui-pager-top {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .ui-pager-top .pagination {
            flex-wrap: nowrap;
            white-space: nowrap;
            margin-bottom: 0;
        }

        .ui-showing {
            white-space: nowrap;
        }

        /* Sort links */
        .table-sort {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            gap: .35rem;
            align-items: center;
            font-weight: 600;
        }

        .table-sort:hover {
            text-decoration: underline;
        }

        /* Status badges */
        .ui-badge {
            display: inline-flex;
            align-items: center;
            padding: .35rem .75rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .ui-badge-draft {
            background: #f2f4f7;
            color: #344054;
            border-color: #eaecf0;
        }

        .ui-badge-dispatched {
            background: #e8f1ff;
            color: #175cd3;
            border-color: #cfe1ff;
        }

        .ui-badge-completed {
            background: #e7f8ef;
            color: #027a48;
            border-color: #bff0d4;
        }

        .ui-badge-cancelled {
            background: #ffeceb;
            color: #b42318;
            border-color: #ffd1cf;
        }

        .ui-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .ui-dot-draft {
            background: #667085;
        }

        .ui-dot-dispatched {
            background: #175cd3;
        }

        .ui-dot-completed {
            background: #027a48;
        }

        .ui-dot-cancelled {
            background: #b42318;
        }

        .ui-dot-pulse {
            position: relative;
        }

        .ui-dot-pulse::after {
            content: "";
            position: absolute;
            inset: -4px;
            border-radius: 999px;
            border: 1px solid rgba(23, 92, 211, .35);
            animation: uiPulse 1.6s ease-out infinite;
        }

        @keyframes uiPulse {
            0% {
                transform: scale(.65);
                opacity: .9;
            }

            100% {
                transform: scale(1.25);
                opacity: 0;
            }
        }

        .ui-action-btn {
            border-radius: 999px;
            padding: .25rem .5rem;
            font-weight: 600;
        }

        .ui-icon-btn {
            border-radius: 12px;
            border: 1px solid #f1f3f6;
            background: #fff;
            padding: .35rem .6rem;
        }

        .ui-icon-btn:hover {
            background: #f8fafc;
        }

        /* Available cards */
        .ui-available-card {
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(16, 24, 40, .06);
            transition: .2s ease;
        }

        .ui-available-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 24, 40, .10);
        }

        .ui-available-number {
            font-size: 30px;
            font-weight: 800;
            line-height: 1;
        }

        .ui-eye-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #d0d5dd;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s ease;
        }

        .ui-eye-btn i {
            font-size: 16px;
            color: #344054;
        }

        .ui-eye-btn:hover {
            background: #f2f4f7;
        }

        .ui-available-dropdown {
            margin-top: 6px;
        }

        .ui-list-controls .btn {
            border-radius: 999px;
            padding: .25rem .7rem;
        }

        .ui-mobile-trip {
            border-radius: 16px;
        }

        .ui-mobile-trip .card-body {
            padding: 14px 14px;
        }

        @media (max-width: 575.98px) {
            .ui-btn-wide {
                width: 100%;
            }

            .ui-btn-equal {
                width: 100%;
            }
        }

        @media (min-width:1200px) {
            .col-xl-5col {
                width: 20%;
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        #newTripModal .select2-container {
            width: 100% !important;
        }

        #newTripModal .select2-container--default .select2-selection--single {
            height: calc(2.375rem + 8px);
            padding: .375rem .75rem;
            border: 1px solid var(--bs-border-color, #ced4da);
            border-radius: .5rem;
            background-color: #fff;
            display: flex;
            align-items: center;
        }

        #newTripModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: 1.5;
            color: var(--bs-body-color, #212529);
        }

        #newTripModal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: .5rem;
        }

        #newTripModal .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }

        #newTripModal .select2-dropdown {
            border: 1px solid var(--bs-border-color, #ced4da);
            border-radius: .5rem;
            overflow: hidden;
        }

        #newTripModal .select2-search__field {
            border-radius: .375rem;
            border: 1px solid var(--bs-border-color, #ced4da) !important;
            padding: .375rem .5rem;
            outline: none;
        }

        .person-stack {
            display: flex;
            align-items: center;
        }

        .person-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            border: 2px solid #fff;
        }

        .person-avatar:not(:first-child) {
            margin-left: -10px;
        }

        .trip-ticket {
            font-weight: 700;
            font-size: 15px;
            color: #4f46e5;
            background: #eef2ff;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-block;
        }

        .trip-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* top icons */
        .trip-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        /* equal icon buttons */
        .trip-icons .btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* dispatch full width */
        .trip-dispatch button {
            width: 100%;
            height: 42px;
            border-radius: 10px;
            font-weight: 600;
        }

        .trip-actions .btn {
            border-radius: 10px;
        }

        .trip-actions .btn-primary {
            padding-left: 14px;
            padding-right: 14px;
        }

        /* mobile optimization */
        @media (max-width:420px) {

            .trip-actions {
                justify-content: space-between;
            }

            .trip-actions .btn-primary {
                flex: 1;
            }

        }

        @media (max-width: 320px) {
            .ui-available-card .card-body {
                padding: 10px 12px;
            }
        }

        .person-avatar {
            position: relative;
            cursor: pointer;
        }

        /* tooltip */
        .person-avatar::after {
            content: attr(data-name);
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background: #111827;
            color: white;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: 0.2s ease;
        }

        /* show on hover */
        .person-avatar:hover::after {
            opacity: 1;
        }

        .trip-status-row {
            display: flex;
            gap: 6px;
            margin-top: 6px;
        }

        .trip-status {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 8px;
            background: #f1f3f6;
            color: #344054;
        }

        /* delivery */
        .trip-status.delivery {
            background: #eef2ff;
            color: #4f46e5;
        }

        /* billing */
        .trip-status.billing {
            background: #fff7ed;
            color: #ea580c;
        }

        /* payment */
        .trip-status.payment {
            background: #ecfdf5;
            color: #16a34a;
        }

        .person-avatar.color-1 {
            background: #fee2e2;
            color: #991b1b;
        }

        .person-avatar.color-2 {
            background: #dbeafe;
            color: #1e3a8a;
        }

        .person-avatar.color-3 {
            background: #dcfce7;
            color: #166534;
        }

        .person-avatar.color-4 {
            background: #fef9c3;
            color: #854d0e;
        }

        .person-avatar.color-5 {
            background: #ede9fe;
            color: #5b21b6;
        }

        .person-avatar.color-6 {
            background: #fce7f3;
            color: #9d174d;
        }

        .person-avatar.color-7 {
            background: #cffafe;
            color: #155e75;
        }

        .person-avatar.color-8 {
            background: #f3f4f6;
            color: #374151;
        }
        
        .ui-person-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.ui-meta-label {
    font-size: 11px;
    color: #98a2b3;
    margin-bottom: 2px;
}

.ui-person-head .ui-person-name {
    font-size: 16px;
}

.ui-person-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.grid-span-2 {
    grid-column: span 2;
}

.ui-meta-label {
    font-size: 11px;
    color: #98a2b3;
    margin-bottom: 4px;
}

.ui-person-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.ui-person-actions .btn {
    min-width: 110px;
    height: 42px;
    border-radius: 12px;
    font-weight: 600;
}
    </style>
@endpush
