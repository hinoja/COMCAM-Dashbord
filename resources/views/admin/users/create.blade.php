@extends('layouts.back')

@section('subtitle', __('Add User'))

@section('content')
     
    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center" style="background-color: #2A2E45; color: #F8F9FA; border-bottom: 2px solid #FF6B35;">
                        <i class="fas fa-user-plus mr-2 fa-fw"></i>
                        <h4 class="mb-0">@lang('New User Form')</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-4 p-md-5">
                            <!-- Form Progress -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">@lang('Form completion')</small>
                                    <small class="text-muted" id="progressText">0%</small>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="formProgress"></div>
                                </div>
                            </div>

                            <form action="{{ route('admin.users.store') }}" method="POST" class="needs-validation" novalidate>
                                @csrf
                                
                                <!-- User Information Section -->
                                <div class="mb-5">
                                    <h5 class="text-primary mb-4">
                                        <i class="fas fa-user-circle"></i> @lang('User Information')
                                    </h5>
                                    
                                    <div class="row">
                                        <!-- Name Field -->
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group position-relative">
                                                <label for="name" class="form-label">
                                                    @lang('Name') <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </span>
                                                    <input type="text" id="name" name="name" placeholder="@lang('Enter full name')" 
                                                        value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror"
                                                        required autofocus>
                                                </div>
                                                @error('name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">@lang('Enter the user\'s full name as it will appear in the system.')</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Email Field -->
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group position-relative">
                                                <label for="email" class="form-label">
                                                    @lang('Email') <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-envelope text-primary"></i>
                                                    </span>
                                                    <input type="email" id="email" name="email" placeholder="@lang('user@example.com')"
                                                        value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror"
                                                        required>
                                                </div>
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">@lang('This email will be used for login and notifications.')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Access Control Section -->
                                <div class="mb-5">
                                    <h5 class="text-primary mb-4">
                                        <i class="fas fa-user-shield"></i> @lang('Access Control')
                                    </h5>
                                    
                                    <div class="row">
                                        <!-- Role Field -->
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group position-relative">
                                                <label for="role_id" class="form-label">
                                                    @lang('Role') <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-user-tag text-primary"></i>
                                                    </span>
                                                    <select id="role_id" name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                                                        <option value="">@lang('Select a role')</option>
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('role_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">@lang('The role determines what permissions the user will have.')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Security Section -->
                                <div class="mb-5">
                                    <h5 class="text-primary mb-4">
                                        <i class="fas fa-shield-alt"></i> @lang('Security')
                                    </h5>
                                    
                                    <div class="row">
                                        <!-- Password Field -->
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group position-relative">
                                                <label for="password" class="form-label d-flex justify-content-between align-items-center">
                                                    <span>@lang('Password') <span class="text-danger">*</span></span>
                                                    <button type="button" id="generatePassword" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-magic mr-1"></i> @lang('Generate')
                                                    </button>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-key text-primary"></i>
                                                    </span>
                                                    <input type="password" id="password" name="password" 
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        required>
                                                    <button type="button" class="btn btn-outline-secondary toggle-password" 
                                                        data-target="password">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="password-strength mt-2 d-none" id="passwordStrength">
                                                    <div class="progress" style="height: 5px;">
                                                        <div class="progress-bar" role="progressbar" style="width: 0%;" id="passwordStrengthBar"></div>
                                                    </div>
                                                    <small class="form-text" id="passwordStrengthText">@lang('Password strength'): @lang('Weak')</small>
                                                </div>
                                                <div class="password-requirements mt-2">
                                                    <small class="form-text text-muted">@lang('Password must contain'):</small>
                                                    <ul class="mt-1 mb-0">
                                                        <li id="length-check">@lang('At least 8 characters')</li>
                                                        <li id="uppercase-check">@lang('At least one uppercase letter')</li>
                                                        <li id="lowercase-check">@lang('At least one lowercase letter')</li>
                                                        <li id="number-check">@lang('At least one number')</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Confirm Password Field -->
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group position-relative">
                                                <label for="password_confirmation" class="form-label">
                                                    @lang('Confirm Password') <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-check-double text-primary"></i>
                                                    </span>
                                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                                        required>
                                                    <button type="button" class="btn btn-outline-secondary toggle-password" 
                                                        data-target="password_confirmation">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <div class="invalid-feedback" id="password_match_feedback" style="display: none;">
                                                    @lang('Passwords do not match')
                                                </div>
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex justify-content-between align-items-center mt-5">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i> @lang('Cancel')
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4 py-2">
                                        <i class="fas fa-user-plus mr-1"></i> @lang('Create User')
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    /* Form styling */
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        padding: 1.2rem 1.5rem;
    }
    
    .form-control {
        border-radius: 0.375rem;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #FF6B35;
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }
    
    .input-group-text {
        border-radius: 0.375rem 0 0 0.375rem;
        border: 1px solid #e2e8f0;
        border-right: none;
    }
    
    .form-label {
        font-weight: 500;
        color: #2A2E45;
        margin-bottom: 0.5rem;
    }
    
    .text-primary {
        color: #2A2E45 !important;
    }
    
    .btn-primary {
        background-color: #FF6B35;
        border-color: #FF6B35;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #e55a29;
        border-color: #e55a29;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
    }
    
    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Password strength meter */
    .password-strength {
        margin-top: 0.5rem;
    }
    
    .password-requirements ul {
        list-style-type: none;
        padding-left: 0;
    }
    
    .password-requirements li {
        margin-bottom: 0.25rem;
        position: relative;
        padding-left: 1.5rem;
        font-size: 0.75rem;
        color: #718096;
    }
    
    .password-requirements li:before {
        content: "•";
        position: absolute;
        left: 0.5rem;
    }
    
    .password-requirements li.text-success:before {
        content: "✓";
        left: 0;
    }
    
    /* Section styling */
    h5.text-primary {
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0.75rem;
    }
    
    /* Progress bar */
    .progress {
        border-radius: 10px;
        overflow: hidden;
        background-color: #f1f5f9;
        height: 5px;
    }
    
    .progress-bar {
        transition: width 0.5s ease;
        border-radius: 10px;
    }
    
    /* Form sections */
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card {
        animation: fadeIn 0.5s ease-out;
    }
    
    /* Toggle password button */
    .toggle-password {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: none;
    }
    
    .toggle-password:focus {
        box-shadow: none;
    }
    
    /* Form validation styling */
    .is-valid {
        border-color: #28a745 !important;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Small helper text */
    .form-text {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
        
        .col-md-6 {
            margin-bottom: 1rem;
        }
    }
    
    /* Focus styles for accessibility */
    input:focus, select:focus, button:focus {
        outline: none;
    }
    
    /* Hover effects */
    .form-control:hover, .btn:hover {
        transition: all 0.2s ease;
    }
    
    /* Required field indicator */
    .text-danger {
        color: #FF6B35 !important;
    }
    
    /* Generate password button */
    #generatePassword {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');
        const strengthContainer = document.getElementById('passwordStrength');
        const lengthCheck = document.getElementById('length-check');
        const uppercaseCheck = document.getElementById('uppercase-check');
        const lowercaseCheck = document.getElementById('lowercase-check');
        const numberCheck = document.getElementById('number-check');
        const matchFeedback = document.getElementById('password_match_feedback');
        const formProgress = document.getElementById('formProgress');
        const progressText = document.getElementById('progressText');
        
        // Form fields for progress calculation
        const formFields = [
            document.getElementById('name'),
            document.getElementById('email'),
            document.getElementById('role_id'),
            passwordInput,
            confirmInput
        ];
        
        // Update form progress
        function updateFormProgress() {
            let filledFields = 0;
            formFields.forEach(field => {
                if (field.value.trim() !== '') {
                    filledFields++;
                }
            });
            
            const progressPercentage = Math.round((filledFields / formFields.length) * 100);
            formProgress.style.width = progressPercentage + '%';
            formProgress.setAttribute('aria-valuenow', progressPercentage);
            progressText.textContent = progressPercentage + '%';
            
            // Change progress bar color based on completion
            if (progressPercentage < 40) {
                formProgress.className = 'progress-bar bg-danger';
            } else if (progressPercentage < 80) {
                formProgress.className = 'progress-bar bg-warning';
            } else {
                formProgress.className = 'progress-bar bg-success';
            }
        }
        
        // Check password strength
        function checkPasswordStrength(password) {
            let strength = 0;
            
            // Length check
            if (password.length >= 8) {
                strength += 25;
                lengthCheck.classList.add('text-success');
            } else {
                lengthCheck.classList.remove('text-success');
            }
            
            // Uppercase check
            if (/[A-Z]/.test(password)) {
                strength += 25;
                uppercaseCheck.classList.add('text-success');
            } else {
                uppercaseCheck.classList.remove('text-success');
            }
            
            // Lowercase check
            if (/[a-z]/.test(password)) {
                strength += 25;
                lowercaseCheck.classList.add('text-success');
            } else {
                lowercaseCheck.classList.remove('text-success');
            }
            
            // Number check
            if (/[0-9]/.test(password)) {
                strength += 25;
                numberCheck.classList.add('text-success');
            } else {
                numberCheck.classList.remove('text-success');
            }
            
            // Update strength bar
            strengthBar.style.width = strength + '%';
            
            // Update strength text
            if (strength < 25) {
                strengthText.textContent = '@lang("Password strength"): @lang("Very Weak")';
                strengthBar.className = 'progress-bar bg-danger';
            } else if (strength < 50) {
                strengthText.textContent = '@lang("Password strength"): @lang("Weak")';
                strengthBar.className = 'progress-bar bg-danger';
            } else if (strength < 75) {
                strengthText.textContent = '@lang("Password strength"): @lang("Medium")';
                strengthBar.className = 'progress-bar bg-warning';
            } else if (strength < 100) {
                strengthText.textContent = '@lang("Password strength"): @lang("Strong")';
                strengthBar.className = 'progress-bar bg-success';
            } else {
                strengthText.textContent = '@lang("Password strength"): @lang("Very Strong")';
                strengthBar.className = 'progress-bar bg-success';
            }
            
            return strength;
        }
        
        // Check if passwords match
        function checkPasswordsMatch() {
            if (confirmInput.value === '') {
                matchFeedback.style.display = 'none';
                confirmInput.classList.remove('is-valid', 'is-invalid');
                return false;
            }
            
            if (passwordInput.value === confirmInput.value) {
                matchFeedback.style.display = 'none';
                confirmInput.classList.remove('is-invalid');
                confirmInput.classList.add('is-valid');
                return true;
            } else {
                matchFeedback.style.display = 'block';
                confirmInput.classList.remove('is-valid');
                confirmInput.classList.add('is-invalid');
                return false;
            }
        }
        
        // Password input event
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password) {
                strengthContainer.classList.remove('d-none');
                checkPasswordStrength(password);
                checkPasswordsMatch();
            } else {
                strengthContainer.classList.add('d-none');
                lengthCheck.classList.remove('text-success');
                uppercaseCheck.classList.remove('text-success');
                lowercaseCheck.classList.remove('text-success');
                numberCheck.classList.remove('text-success');
            }
            
            updateFormProgress();
        });
        
        // Confirm password input event
        confirmInput.addEventListener('input', function() {
            checkPasswordsMatch();
            updateFormProgress();
        });
        
        // Generate password button
        document.getElementById('generatePassword').addEventListener('click', function() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+';
            let password = '';
            
            // Ensure at least one uppercase, one lowercase, and one number
            password += chars.charAt(Math.floor(Math.random() * 26)); // Uppercase
            password += chars.charAt(26 + Math.floor(Math.random() * 26)); // Lowercase
            password += chars.charAt(52 + Math.floor(Math.random() * 10)); // Number
            
            // Fill the rest randomly
            for (let i = 0; i < 9; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            
            // Shuffle the password
            password = password.split('').sort(() => 0.5 - Math.random()).join('');
            
            // Set the password
            passwordInput.type = 'text';
            passwordInput.value = password;
            confirmInput.value = password;
            
            // Trigger input events
            passwordInput.dispatchEvent(new Event('input'));
            confirmInput.dispatchEvent(new Event('input'));
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: '@lang("Password Generated")',
                text: '@lang("A strong password has been generated and filled in for you.")',
                timer: 3000,
                showConfirmButton: false
            });
            
            // Change eye icon
            document.querySelector('.toggle-password[data-target="password"] i').classList.remove('fa-eye');
            document.querySelector('.toggle-password[data-target="password"] i').classList.add('fa-eye-slash');
            document.querySelector('.toggle-password[data-target="password_confirmation"] i').classList.remove('fa-eye');
            document.querySelector('.toggle-password[data-target="password_confirmation"] i').classList.add('fa-eye-slash');
            
            // After 2 seconds, hide the password
            setTimeout(() => {
                passwordInput.type = 'password';
                confirmInput.type = 'password';
                document.querySelector('.toggle-password[data-target="password"] i').classList.remove('fa-eye-slash');
                document.querySelector('.toggle-password[data-target="password"] i').classList.add('fa-eye');
                document.querySelector('.toggle-password[data-target="password_confirmation"] i').classList.remove('fa-eye-slash');
                document.querySelector('.toggle-password[data-target="password_confirmation"] i').classList.add('fa-eye');
            }, 2000);
        });
        
        // Monitor all form fields for progress
        formFields.forEach(field => {
            field.addEventListener('input', updateFormProgress);
        });
        
        // Form validation
        const form = document.querySelector('form.needs-validation');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: '@lang("Form Validation Error")',
                    text: '@lang("Please fill in all required fields correctly.")',
                    confirmButtonColor: '#FF6B35'
                });
            } else if (passwordInput.value !== confirmInput.value) {
                event.preventDefault();
                event.stopPropagation();
                
                // Show error message for password mismatch
                Swal.fire({
                    icon: 'error',
                    title: '@lang("Password Mismatch")',
                    text: '@lang("The password and confirmation do not match.")',
                    confirmButtonColor: '#FF6B35'
                });
            }
            
            form.classList.add('was-validated');
        });
        
        // Initialize form progress
        updateFormProgress();
    });
</script>
@endpush


