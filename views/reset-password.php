<?php include __DIR__ . '/layouts/header.php'; ?>

<?php
$token = $_GET['token'] ?? '';
if (!$token) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid or missing token.</div></div>";
    include __DIR__ . '/layouts/footer.php';
    exit;
}
?>

<div class="container mt-5">
    <div class="card shadow-lg p-4 mx-auto" style="max-width: 500px;">
        <h2 class="mb-4 text-center">🔐 Reset Password</h2>

        <form id="resetForm">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="mb-3">
                <label class="form-label">New Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">✅ Reset Password</button>
        </form>

        <div id="resetMsg" class="text-center mt-3"></div>
    </div>
</div>

<script>
document.getElementById('resetForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = e.target;
    const payload = {
        token: form.token.value,
        password: form.password.value
    };

    const res = await fetch('/api/auth/reset-password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    const result = await res.json();
    document.getElementById('resetMsg').innerHTML = result.success
        ? `<span class="text-success">${result.message}</span>`
        : `<span class="text-danger">${result.message}</span>`;

    if (result.success) {
        setTimeout(() => window.location.href = '/login', 1500);
    }
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>