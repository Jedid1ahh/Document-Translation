// Example for button hover effect
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('mouseenter', () => {
        button.style.transform = 'scale(1.05)';
    });
    button.addEventListener('mouseleave', () => {
        button.style.transform = 'scale(1)';
    });
});

document.getElementById('loginBtn').addEventListener('click', function () {
    document.getElementById('loginForm').classList.remove('hidden');
    document.getElementById('registerForm').classList.add('hidden');
});

document.getElementById('registerBtn').addEventListener('click', function () {
    document.getElementById('registerForm').classList.remove('hidden');
    document.getElementById('loginForm').classList.add('hidden');
});

