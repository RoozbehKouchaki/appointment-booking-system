<?php
require __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'book') {
    require __DIR__ . '/../controllers/AppointmentController.php';
    exit();
}

include __DIR__ . '/layouts/header.php';

$service_id = $_POST['service_id'] ?? null;
$doctor_id = $_POST['doctor_id'] ?? null;

// Fetch services
$serviceStmt = $pdo->query("SELECT DISTINCT name, MIN(id) AS id FROM services GROUP BY name");
$services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch doctors
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

// Fetch slots
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
    <h2 class="text-center mb-4">📝 Book Appointment</h2>

    <!-- Step 1: Select Service -->
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

    <!-- Step 2: Select Doctor -->
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

    <!-- Step 3: Book Slot -->
    <?php if ($doctor_id): ?>
        <form method="POST" action="/book" class="card p-3 shadow-sm">
            <input type="hidden" name="service_id" value="<?= $service_id ?>">
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
            <input type="hidden" name="action" value="book">

            <div class="mb-3">
                <label class="form-label">Choose Available Slot:</label>
                <select name="slot_id" class="form-select" required>
                    <option value="">-- Select Time Slot --</option>
                    <?php foreach ($slots as $slot): ?>
                        <option value="<?= $slot['id'] ?>">
                            <?= date('Y-m-d H:i', strtotime($slot['slot_datetime'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success w-100">✅ Book Appointment</button>
        </form>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="/" class="btn btn-secondary me-2">🔙 Back to Dashboard</a>
        <a href="/logout" class="btn btn-danger">🚪 Logout</a>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>