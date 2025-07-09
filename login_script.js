document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const togglePassword = document.querySelector('.toggle-password i'); // Ambil ikon mata

    if (passwordInput && togglePassword) {
        togglePassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle the eye icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash'); // Ganti ikon menjadi mata tertutup
        });
    } else {
        console.warn("Input password atau ikon toggle tidak ditemukan.");
    }

    // Optional: Auto-focus on the first input field
    const firstInputField = document.querySelector('.login-form input:not([type="hidden"])');
    if (firstInputField) {
        firstInputField.focus();
    }
});