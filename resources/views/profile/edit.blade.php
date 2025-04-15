@extends('layouts.back')

@section('subtitle', __('Profile'))

@push('css')
    <style>
        /* Styles pour respecter la charte graphique BIM INGENIOUS BTP */
        .profile-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
            background-color: #FFFFFF;
        }

        .card-header {
            background-color: #2A2E45;
            color: #F8F9FA;
            border-bottom: 3px solid #FF6B35;
            padding: 1.5rem;
        }

        .card-header h4 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 2.5rem;
        }

        /* Style pour la photo de profil */
        .profile-picture-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 2rem;
        }

        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #FF6B35;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .profile-picture-input {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 44px;
            height: 44px;
            background-color: #FF6B35;
            color: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-picture-input:hover {
            background-color: #E55A2B;
            transform: scale(1.1);
        }

        .profile-picture-input input {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* Style des formulaires */
        .form-section {
            margin-bottom: 3rem;
            padding: 1.5rem;
            background-color: rgba(42, 46, 69, 0.03);
            border-radius: 8px;
        }

        .form-section h5 {
            font-size: 1.15rem;
            color: #2A2E45;
            font-weight: 600;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #FF6B35;
            padding-bottom: 0.5rem;
        }

        .form-group label {
            font-weight: 600;
            color: #2A2E45;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #6C757D;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #FF6B35;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }

        .invalid-feedback {
            font-size: 0.85rem;
            color: #FF6B35;
        }

        /* Style pour les champs de mot de passe avec toggle */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6C757D;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #FF6B35;
        }

        /* Boutons */
        .btn-primary {
            background-color: #FF6B35;
            border-color: #FF6B35;
            color: #FFFFFF;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #E55A2B;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-danger {
            background-color: #6C757D;
            border-color: #6C757D;
            color: #FFFFFF;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Indicateur de chargement */
        .btn.loading .spinner-border {
            display: inline-block;
        }

        .btn .spinner-border {
            display: none;
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }

        /* Alertes */
        .alert {
            border-radius: 8px;
            margin-bottom: 2rem;
        }
    </style>
@endpush

@section('content')
    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>@lang('Edit Profile')</h4>
                    </div>
                    <div class="card-body">
                        <!-- Section Avatar -->
                        <div class="text-center mb-4">
                            <div class="profile-picture-wrapper">
                                <img id="profile-picture-preview"
                                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('back/img/avatar/avatar-1.png') }}"
                                    alt="Profile Picture" class="profile-picture">
                                <label class="profile-picture-input" for="avatar">
                                    <i class="fas fa-camera"></i>
                                    <input type="file" id="avatar" name="avatar" accept="image/*">
                                </label>
                            </div>
                        </div>

                        <!-- Formulaire de mise à jour du profil -->
                        <form id="profile-form" method="POST" action="{{ route('profile.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <!-- Nom -->
                            <div class="form-group">
                                <label for="name">@lang('Name')</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $user->name) }}">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">@lang('Email')</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Avatar (hidden input pour compatibilité) -->
                            <input type="hidden" name="avatar" id="avatar-hidden">

                            <button type="submit" style="background:rgb(69,132,103)" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                @lang('Update Profile')
                            </button>
                        </form>

                        <hr>

                        <!-- Formulaire de mise à jour du mot de passe -->
                        <form id="password-form" method="POST" {{-- action="{{ route('profile.updatePassword') }}" --}}>
                            @csrf
                            @method('PATCH')

                            <!-- Mot de passe actuel -->
                            <div class="form-group password-wrapper">
                                <label for="current_password">@lang('Current Password')</label>
                                <input type="password" name="current_password" id="current_password" class="form-control"
                                    required>
                                <i class="fas fa-eye password-toggle" data-target="current_password"></i>
                                @error('current_password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nouveau mot de passe -->
                            <div class="form-group password-wrapper">
                                <label for="new_password">@lang('New Password')</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" required>
                                <i class="fas fa-eye password-toggle" data-target="new_password"></i>
                                @error('new_password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Confirmation du mot de passe -->
                            <div class="form-group password-wrapper">
                                <label for="new_password_confirmation">@lang('Confirm New Password')</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                    class="form-control" required>
                                <i class="fas fa-eye password-toggle" data-target="new_password_confirmation"></i>
                            </div>

                            <button type="submit" style="background:rgb(69,132,103)" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                @lang('Update Password')
                            </button>
                        </form>

                        <hr>

                        <!-- Formulaire de suppression du compte -->
                        <form id="delete-form" method="POST" action="{{ route('profile.destroy') }}">
                            @csrf
                            @method('DELETE')

                            <div class="form-group password-wrapper">
                                <label for="delete_password">@lang('Confirm Password to Delete')</label>
                                <input type="password" name="delete_password" id="delete_password" class="form-control"
                                    required>
                                <i class="fas fa-eye password-toggle" data-target="delete_password"></i>
                                @error('delete_password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <button style="background: red" type="submit" class="btn btn-danger">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                @lang('Delete Account')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const form = e.target;
                const button = form.querySelector('button[type="submit"]');

                Swal.fire({
                    title: '@lang('Are you sure?')',
                    text: "@lang('All your data will be permanently deleted!')",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FF6B35',
                    cancelButtonColor: '#6C757D',
                    confirmButtonText: '@lang('Yes, delete!')',
                    cancelButtonText: '@lang('Cancel')'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Prévisualisation de la photo de profil
            const avatarInput = document.getElementById('avatar');
            const avatarPreview = document.getElementById('profile-picture-preview');

            if (avatarInput) {
                avatarInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = (event) => {
                                avatarPreview.src = event.target.result;
                            };
                            reader.readAsDataURL(file);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid file',
                                text: 'Please upload a valid image file.'
                            });
                        }
                    }
                });
            }

            // Gestion des formulaires avec indicateur de chargement
            const forms = document.querySelectorAll('#profile-form, #password-form, #delete-form');
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    const button = form.querySelector('button[type="submit"]');
                    button.classList.add('loading');
                    button.disabled = true;
                });
            });

            // Toggle visibilité des mots de passe
            const passwordToggles = document.querySelectorAll('.password-toggle');
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const targetId = toggle.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    toggle.classList.toggle('fa-eye', !isPassword);
                    toggle.classList.toggle('fa-eye-slash', isPassword);
                });
            });
        });
    </script>
@endpush
