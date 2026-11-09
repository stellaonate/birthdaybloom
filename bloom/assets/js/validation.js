function showError(id, message) {
  const element = document.getElementById(id);
  if (element) element.textContent = message;
}
function validEmail(email) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); }
const registerForm = document.getElementById('registerForm');
if (registerForm) {
  registerForm.addEventListener('submit', (event) => {
    const name = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirmPassword').value;
    ['nameError','emailError','passwordError','confirmError'].forEach(id => showError(id, ''));
    let valid = true;
    if (name.length < 2 || name.length > 100) { showError('nameError','Enter 2–100 characters.'); valid = false; }
    if (!validEmail(email)) { showError('emailError','Enter a valid email address.'); valid = false; }
    if (password.length < 8) { showError('passwordError','Use at least 8 characters.'); valid = false; }
    if (confirm !== password) { showError('confirmError','Passwords must match.'); valid = false; }
    if (!valid) { event.preventDefault(); document.getElementById('formMessage').textContent = 'Please correct the highlighted fields.'; }
  });
}
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', (event) => {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    showError('loginEmailError',''); showError('loginPasswordError','');
    let valid = true;
    if (!validEmail(email)) { showError('loginEmailError','Enter a valid email address.'); valid = false; }
    if (!password) { showError('loginPasswordError','Enter your password.'); valid = false; }
    if (!valid) { event.preventDefault(); document.getElementById('loginMessage').textContent = 'Please correct the highlighted fields.'; }
  });
}
