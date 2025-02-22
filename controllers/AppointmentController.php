<?php
session_start();
require '../config/config.php';
require '../models/Appointment.php';
require '../models/AvailableSlot.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to perform this action.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("❌ Invalid request!");
}

$action = $_POST['action'] ?? null;
$user_id = $_SESSION['user_id'];

// ✅ 1️⃣ Handle Appointment Booking
if ($action === 'book') {
    $service_id = $_POST['service_id'] ?? null;
    $slot_id = $_POST['slot_id'] ?? null;

    if (!$service_id || !$slot_id) {
        die("❌ Missing required fields.");
    }

    try {
        $pdo->beginTransaction();

        $slot = AvailableSlot::findById($pdo, $slot_id);
        if (!$slot || $slot->isBooked()) {
            throw new Exception("⚠️ Slot is already booked or doesn't exist.");
        }

        $appointment = new Appointment();
        $appointment->setUserId($user_id)
                    ->setSlotId($slot_id)
                    ->setServiceId($service_id)
                    ->setStatus('Confirmed')
                    ->save($pdo);

        $slot->book($pdo);

        $pdo->commit();
        echo "✅ Appointment booked successfully!";
        echo '<br><a href="../views/my_appointments.php">📅 View My Appointments</a>';
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ Error: " . htmlspecialchars($e->getMessage());
    }
    exit();
}

// ✏️ 2️⃣ Handle Appointment Modification
if ($action === 'modify') {
    $appointment_id = $_POST['appointment_id'] ?? null;
    $new_service_id = $_POST['service_id'] ?? null;
    $new_slot_id = $_POST['slot_id'] ?? null;

    if (!$appointment_id || !$new_service_id || !$new_slot_id) {
        die("❌ All fields are required.");
    }

    try {
        $pdo->beginTransaction();

        $appointment = Appointment::findByIdAndUser($pdo, $appointment_id, $user_id);
        if (!$appointment) throw new Exception("❌ Unauthorized appointment modification.");

        $newSlot = AvailableSlot::findById($pdo, $new_slot_id);
        if (!$newSlot || ($newSlot->isBooked() && $appointment->getSlotId() !== $new_slot_id)) {
            throw new Exception("⚠️ Selected slot is unavailable.");
        }

        $old_slot_id = $appointment->getSlotId();

        $appointment->setServiceId($new_service_id)
                    ->setSlotId($new_slot_id)
                    ->save($pdo);

        if ($old_slot_id !== $new_slot_id) {
            $oldSlot = AvailableSlot::findById($pdo, $old_slot_id);
            $oldSlot?->unbook($pdo);
            $newSlot->book($pdo);
        }

        $pdo->commit();
        echo "✅ Appointment updated successfully!";
        echo '<br><a href="../views/my_appointments.php">📅 Back to My Appointments</a>';
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ Error: " . htmlspecialchars($e->getMessage());
    }
    exit();
}

// ❌ 3️⃣ Handle Appointment Cancellation
if ($action === 'cancel') {
    $appointment_id = $_POST['appointment_id'] ?? null;

    if (!$appointment_id) {
        die("❌ Appointment ID is missing.");
    }

    try {
        $pdo->beginTransaction();

        $appointment = Appointment::findByIdAndUser($pdo, $appointment_id, $user_id);
        if (!$appointment) {
            throw new Exception("❌ Appointment not found or unauthorized.");
        }

        if ($appointment->getStatus() === 'Cancelled') {
            throw new Exception("⚠️ Appointment is already cancelled.");
        }

        $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?")
            ->execute([$appointment_id]);

        $slot = AvailableSlot::findById($pdo, $appointment->getSlotId());
        if ($slot) {
            $slot->unbook($pdo);  // Free the slot
        }

        $pdo->commit();
        echo "✅ Appointment cancelled successfully!";
        echo '<br><a href="../views/my_appointments.php">🔙 Back to My Appointments</a>';
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ Error: " . htmlspecialchars($e->getMessage());
        echo '<br><a href="../views/my_appointments.php">🔙 Try Again</a>';
    }
    exit();
}

// 🚫 Catch invalid actions
echo "❌ Invalid action.";
?>