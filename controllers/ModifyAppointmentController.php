<?php
session_start();
require '../config/config.php';
require '../models/Appointment.php';
require '../models/AvailableSlot.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to modify appointments.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("❌ Invalid request.");
}

$appointment_id = $_POST['appointment_id'] ?? null;
$new_service_id = $_POST['service_id'] ?? null;
$new_slot_id = $_POST['slot_id'] ?? null;

if (!$appointment_id || !$new_service_id || !$new_slot_id) {
    die("❌ All fields are required.");
}

try {
    $pdo->beginTransaction();

    // 📝 1️⃣ Load appointment & validate ownership
    $appointment = Appointment::findByIdAndUser($pdo, $appointment_id, $_SESSION['user_id']);
    if (!$appointment) {
        throw new Exception("❌ Appointment not found or unauthorized.");
    }

    // 🕒 2️⃣ Check new slot availability
    $newSlot = AvailableSlot::findById($pdo, $new_slot_id);
    if (!$newSlot) {
        throw new Exception("❌ Selected slot does not exist.");
    }
    if ($newSlot->isBooked() && $appointment->getSlotId() !== $new_slot_id) {
        throw new Exception("⚠️ Selected slot is already booked.");
    }

    // 📅 3️⃣ Update appointment details
    $old_slot_id = $appointment->getSlotId();
    $appointment->setServiceId($new_service_id)
                ->setSlotId($new_slot_id)
                ->save($pdo);

    // 🔄 4️⃣ Update slot bookings (if slot changed)
    if ($old_slot_id !== $new_slot_id) {
        $oldSlot = AvailableSlot::findById($pdo, $old_slot_id);
        $oldSlot?->unbook($pdo);  // Safely unbook old slot
        $newSlot->book($pdo);     // Book new slot
    }

    $pdo->commit();

    echo "✅ Appointment modified successfully!";
    echo '<br><a href="../views/my_appointments.php">📅 Back to My Appointments</a>';

} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error: " . htmlspecialchars($e->getMessage());
    echo '<br><a href="../views/my_appointments.php">🔙 Try Again</a>';
}
?>