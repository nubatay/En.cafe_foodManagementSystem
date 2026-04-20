@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : (auth()->user()->role === 'kitchen' ? 'layouts.kitchen' : 'layouts.customer'))

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 fw-bold">Profile</h2>

    {{-- UPDATE PROFILE --}}
    <div class="card mb-4 shadow-sm border-0 rounded-4">
        <div class="card-header bg-white fw-semibold">
            Update Profile Information
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                Update your account's profile information and email address.
            </p>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success">
                    Profile updated successfully.
                </div>
            @endif

            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        required
                        autofocus
                        autocomplete="name"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="username"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-dark">
                    Save
                </button>
            </form>
        </div>
    </div>

    {{-- UPDATE PASSWORD --}}
    <div class="card mb-4 shadow-sm border-0 rounded-4">
        <div class="card-header bg-white fw-semibold">
            Update Password
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                Ensure your account is using a long, random password to stay secure.
            </p>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success">
                    Password updated successfully.
                </div>
            @endif

            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="mb-3">
                    <label for="update_password_current_password" class="form-label fw-semibold">Current Password</label>
                    <input
                        id="update_password_current_password"
                        name="current_password"
                        type="password"
                        class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                        autocomplete="current-password"
                    >
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="update_password_password" class="form-label fw-semibold">New Password</label>
                    <input
                        id="update_password_password"
                        name="password"
                        type="password"
                        class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                        autocomplete="new-password"
                    >
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="update_password_password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                    <input
                        id="update_password_password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                        autocomplete="new-password"
                    >
                    @error('password_confirmation', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-dark">
                    Save
                </button>
            </form>
        </div>
    </div>

    {{-- DELETE ACCOUNT --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white fw-semibold text-danger">
            Delete Account
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted.
            </p>

            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                Delete Account
            </button>
        </div>
    </div>
</div>

{{-- DELETE ACCOUNT MODAL --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-3">
                        Are you sure you want to delete your account? This action cannot be undone.
                    </p>

                    <div class="mb-3">
                        <label for="delete_password" class="form-label fw-semibold">Password</label>
                        <input
                            id="delete_password"
                            name="password"
                            type="password"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            placeholder="Enter your password to confirm"
                        >
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 