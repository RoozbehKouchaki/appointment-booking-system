<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-center align-items-center" style="height: calc(90vh - 112px);">
  <div class="card login-card shadow-lg p-4 w-60">
    <h2 class="mb-4 text-center">User Login</h2>

    <form id="loginForm">
      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <div id="loginMsg" class="mt-3 text-center"></div>

    <hr class="my-4">
    <p class="text-center"><a href="/forgot-password">Forgot Password?</a></p>
    <p class="text-center">Don't have an account? <a href="/register">Register here</a></p>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value.trim();
  const msgBox = document.getElementById('loginMsg');

  try {
    const res = await fetch('/api/auth/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ email, password })
    });

    const result = await res.json();

    msgBox.innerHTML = result.success
      ? `<span class="text-success">${result.message}</span>`
      : `<span class="text-danger">${result.message}</span>`;

    if (result.success) {
      setTimeout(() => window.location.href = '/', 1000);
    }

  } catch (err) {
    msgBox.innerHTML = `<span class="text-danger">Something went wrong</span>`;
  }
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>