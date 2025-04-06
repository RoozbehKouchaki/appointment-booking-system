<?php
require '../config/config.php';
include '../views/layouts/header.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your appointments.");
}
?>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h2 class="text-center mb-4">My Appointments</h2>

            <!-- Search Form -->
            <div class="d-flex mb-4">
                <input type="text" id="searchInput" class="form-control me-2"
                       placeholder="Search by service or doctor...">
                <button class="btn btn-primary me-2" onclick="triggerSearch()">Search</button>
                <button class="btn btn-outline-secondary" onclick="resetSearch()">Show Full List</button>
            </div>

            <!-- Appointments Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Service</th>
                            <th>Doctor</th>
                            <th>Appointment Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentTable">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <a href="/" class="btn btn-secondary">Back to Dashboard</a>
                <a href="/logout" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
async function loadAppointments(search = '') {
    const url = search
        ? `/api/appointments/index.php?search=${encodeURIComponent(search)}`
        : '/api/appointments/index.php';

    const res = await fetch(url);
    const result = await res.json();

    if (!result.success) return alert(result.message);

    const table = document.getElementById('appointmentTable');
    table.innerHTML = '';

    result.data.forEach((appt, index) => {
        const apptTime = new Date(appt.appointment_time);
        const now = new Date();
        const isFuture = apptTime > now;
        const isNotCancelled = appt.status.toLowerCase() !== 'cancelled';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${appt.service_name}</td>
            <td>${appt.doctor_name}</td>
            <td>${apptTime.toLocaleString()}</td>
            <td><span class="badge ${isNotCancelled ? 'bg-success' : 'bg-secondary'}">${appt.status}</span></td>
            <td>
                ${isFuture && isNotCancelled ? `
                    <button class="btn btn-danger btn-sm" onclick="cancelAppointment(${appt.appointment_id})">Cancel</button>
                    <a href="/appointments/modify/${appt.appointment_id}" class="btn btn-warning btn-sm">Modify</a>
                ` : '<span class="text-muted">N/A</span>'}
            </td>
        `;
        table.appendChild(row);
    });
}

function cancelAppointment(id) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        fetch('/api/appointments/cancel.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'appointment_id=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) loadAppointments();
        })
        .catch(() => alert('Request failed'));
    }
}

function triggerSearch() {
    const value = document.getElementById('searchInput').value.trim();
    loadAppointments(value);
}

function resetSearch() {
    document.getElementById('searchInput').value = '';
    loadAppointments();
}

// Initial load
window.addEventListener('DOMContentLoaded', () => loadAppointments());
</script>

<?php include '../views/layouts/footer.php'; ?>
