<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-center align-items-center" style="height: calc(90vh - 112px);">
  <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
    <h2 class="text-center mb-4">🔐 Forgot Password</h2>

    <form id="forgotForm">
      <div class="mb-3">
        <label for="email" class="form-label">Enter your email address:</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
      </div>
    </form>

    <div id="forgotMsg" class="mt-3 text-center"></div>

    <hr class="my-4">
    <p class="text-center">
      <a href="/login">Back to Login</a>
    </p>
  </div>
</div>

<script>
document.getElementById('forgotForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const email = document.getElementById('email').value;
  const msgBox = document.getElementById('forgotMsg');

  const res = await fetch('/api/auth/forgot-password.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email })
  });

  const result = await res.json();
  console.log(result);
  // 🪵 Log the full response to console
  console.log(result);

  msgBox.innerHTML = result.success
    ? `<span class="text-success">${result.message}</span>`
    : `<span class="text-danger">${result.message}</span>`;
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>