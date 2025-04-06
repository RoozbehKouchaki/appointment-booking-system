<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require __DIR__ . '/../config/config.php';
include __DIR__ . '/layouts/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$service_id = $_POST['service_id'] ?? null;
$doctor_id = $_POST['doctor_id'] ?? null;

$serviceStmt = $pdo->query("SELECT DISTINCT name, MIN(id) AS id FROM services GROUP BY name");
$services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);

$doctors = [];
if ($service_id) {
    $doctorStmt = $pdo->prepare("
        SELECT d.id, d.name
        FROM doctors d
        JOIN doctor_services ds ON d.id = ds.doctor_id
        WHERE ds.service_id = ?
    ");
    $doctorStmt->execute([$service_id]);
    $doctors = $doctorStmt->fetchAll(PDO::FETCH_ASSOC);
}

$slots = [];
if ($doctor_id) {
    $slotStmt = $pdo->prepare("
        SELECT id, slot_datetime
        FROM available_slots
        WHERE doctor_id = ? AND is_booked = 0
        ORDER BY slot_datetime ASC
    ");
    $slotStmt->execute([$doctor_id]);
    $slots = $slotStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Book Appointment</h2>

    <form method="POST" action="" class="mb-3">
        <div class="mb-3">
            <label class="form-label">Choose Service:</label>
            <select name="service_id" class="form-select" required onchange="this.form.submit()">
                <option value="">-- Select Service --</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= $service['id'] ?>" <?= $service_id == $service['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($service['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if ($service_id): ?>
        <form method="POST" action="" class="mb-3">
            <input type="hidden" name="service_id" value="<?= $service_id ?>">
            <div class="mb-3">
                <label class="form-label">Choose Doctor:</label>
                <select name="doctor_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Select Doctor --</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= $doctor['id'] ?>" <?= $doctor_id == $doctor['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($doctor['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    <?php endif; ?>

    <?php if ($doctor_id): ?>
        <div class="card p-3 shadow-sm">
            <div class="mb-3">
                <label class="form-label">Choose Available Slot:</label>
                <select id="slot_id" class="form-select" required>
                    <option value="">-- Select Time Slot --</option>
                    <?php foreach ($slots as $slot): ?>
                        <option value="<?= $slot['id'] ?>">
                            <?= date('Y-m-d H:i', strtotime($slot['slot_datetime'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn btn-success w-100" onclick="submitBooking()">Book Appointment</button>
            <div id="booking-msg" class="mt-3 text-center"></div>
        </div>

        <script>
        function submitBooking() {
            const slotId = document.getElementById('slot_id').value;
            const serviceId = <?= $service_id ?>;
            const msgBox = document.getElementById('booking-msg');

            if (!slotId) {
                msgBox.innerHTML = '<div class="alert alert-warning">Please select a time slot.</div>';
                return;
            }

            fetch('/api/appointments/book.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ service_id: serviceId, slot_id: slotId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    msgBox.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    setTimeout(() => window.location.href = '/appointments', 1500);
                } else {
                    msgBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                msgBox.innerHTML = '<div class="alert alert-danger">Error communicating with the server.</div>';
            });
        }
        </script>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="/" class="btn btn-secondary me-2">Back to Dashboard</a>
        <a href="/logout" class="btn btn-danger">Logout</a>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
