<?php
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

include __DIR__ . '/../views/layouts/header.php'; 
?>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body text-center">
        <h2 class="mb-4">👋 Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>!</h2>
                    <p class="lead">Choose an option:</p>

            <div class="d-grid gap-3 col-6 mx-auto">
                <a href="/appointments" class="btn btn-primary btn-lg">📅 View My Appointments</a>
                <a href="/book" class="btn btn-success btn-lg">📝 Book an Appointment</a>
            </div>

            <hr class="my-4">

            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-danger">🚪 Logout</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/layouts/footer.php'; ?>