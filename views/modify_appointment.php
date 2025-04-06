<?php
require '../config/config.php';
include '../views/layouts/header.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to modify an appointment.");
}

// Get appointment ID from clean URL
$appointment_id = null;
if (preg_match('#/appointments/modify/(\d+)#', $_SERVER['REQUEST_URI'], $matches)) {
    $appointment_id = $matches[1];
}

if (!$appointment_id) {
    die("No appointment selected.");
}
?>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h2 class="mb-4 text-center">Modify Appointment</h2>

            <form id="modifyForm">
                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_id) ?>">

                <!-- Service -->
                <div class="mb-3">
                    <label class="form-label">Service:</label>
                    <select id="serviceSelect" name="service_id" class="form-select" required></select>
                </div>

                <!-- Doctor -->
                <div class="mb-3">
                    <label class="form-label">Doctor:</label>
                    <select id="doctorSelect" name="doctor_id" class="form-select" required></select>
                </div>

                <!-- Slot -->
                <div class="mb-4">
                    <label class="form-label">Available Slots:</label>
                    <select id="slotSelect" name="slot_id" class="form-select" required></select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Update Appointment</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="/appointments" class="btn btn-secondary">Back to My Appointments</a>
            </div>
        </div>
    </div>
</div>

<script>
const appointmentId = <?= json_encode($appointment_id) ?>;

async function loadData() {
    const response = await fetch(`/api/appointments/show.php?id=${appointmentId}`);
    const result = await response.json();
    if (!result.success) return alert(result.message);

    const data = result.data;

    // Services
    const servicesRes = await fetch('/api/services.php');
    const servicesData = await servicesRes.json();
    const serviceSelect = document.getElementById('serviceSelect');
    servicesData.data.forEach(s => {
        const option = new Option(s.name, s.id);
        if (s.id == data.service_id) option.selected = true;
        serviceSelect.append(option);
    });

    // Doctors
    await loadDoctors(data.service_id, data.doctor_id);

    // Slots
    if (data.doctor_id) {
    await loadSlots(data.doctor_id, data.slot_id);
}

    // Events
    serviceSelect.addEventListener('change', async (e) => {
        await loadDoctors(e.target.value);
        document.getElementById('slotSelect').innerHTML = '<option value="">-- Select Time Slot --</option>';
    });

    document.getElementById('doctorSelect').addEventListener('change', (e) => {
        loadSlots(e.target.value);
    });
}

async function loadDoctors(serviceId, selected = null) {
    const res = await fetch(`/api/doctors.php?service_id=${serviceId}`);
    const data = await res.json();
    const doctorSelect = document.getElementById('doctorSelect');
    doctorSelect.innerHTML = '';
    data.data.forEach(d => {
        const option = new Option(d.name, d.id);
        if (d.id == selected) option.selected = true;
        doctorSelect.append(option);
    });
}

async function loadSlots(doctorId, selected = null) {
    const res = await fetch(`/api/slots.php?doctor_id=${doctorId}`);
    const data = await res.json();
    const slotSelect = document.getElementById('slotSelect');
    slotSelect.innerHTML = '';
    data.data.forEach(s => {
        const option = new Option(s.slot_datetime, s.id);
        if (s.id == selected) option.selected = true;
        slotSelect.append(option);
    });
}

loadData();

// Form Submit
const form = document.getElementById('modifyForm');
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const payload = {
        appointment_id: appointmentId,
        service_id: form.service_id.value,
        doctor_id: form.doctor_id.value,
        slot_id: form.slot_id.value
    };

    const res = await fetch('/api/appointments/update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    const result = await res.json();

    if (result.success) {
        alert(result.message);
        window.location.href = '/appointments';
    } else {
        alert(result.message);
    }
});
</script>

<?php include '../views/layouts/footer.php'; ?>
