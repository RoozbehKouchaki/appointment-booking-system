<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-center align-items-center" style="height: calc(90vh - 112px);">
  <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
    <h2 class="text-center mb-4">User Registration</h2>

    <form id="registerForm" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" id="username" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Register</button>
      </div>
    </form>

    <div id="register-msg" class="text-center mt-3"></div>

    <hr class="my-4">
    <p class="text-center">Already have an account? <a href="/login">Login here</a></p>
  </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const username = document.getElementById('username').value.trim();
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value.trim();
  const msgBox = document.getElementById('register-msg');

  const res = await fetch('/api/auth/register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, email, password })
  });

  const data = await res.json();
  msgBox.innerHTML = data.success
    ? `<span class="text-success">${data.message}</span>`
    : `<span class="text-danger">${data.message}</span>`;

  if (data.success) {
    setTimeout(() => window.location.href = '/login', 1000);
  }
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>