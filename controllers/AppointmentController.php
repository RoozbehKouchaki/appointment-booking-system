<?php

require '../config/config.php';
require '../models/Appointment.php';
require '../models/AvailableSlot.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Invalid request method.";
    exit();
}

$action = $_POST['action'] ?? null;
$user_id = $_SESSION['user_id'];

// 1. Appointment Booking
if ($action === 'book') {
    $service_id = $_POST['service_id'] ?? null;
    $slot_id = $_POST['slot_id'] ?? null;

    if (!$service_id || !$slot_id) {
        $_SESSION['flash_message'] = "Missing required fields.";
        header('Location: /book');
        exit();
    }

    try {
        $pdo->beginTransaction();

        $slot = AvailableSlot::findById($pdo, $slot_id);
        if (!$slot || $slot->isBooked()) {
            throw new Exception("Slot is already booked or doesn't exist.");
        }

        $appointment = new Appointment();
        $appointment->setUserId($user_id)
                    ->setSlotId($slot_id)
                    ->setServiceId($service_id)
                    ->setStatus('Confirmed')
                    ->save($pdo);

        $slot->book($pdo);

        $pdo->commit();
        $_SESSION['flash_message'] = "Appointment booked successfully!";
        header('Location: /appointments');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Error: " . htmlspecialchars($e->getMessage());
        header('Location: /book');
        exit();
    }
}

// 2. Appointment Modification
if ($action === 'modify') {
    $appointment_id = $_POST['appointment_id'] ?? null;
    $new_service_id = $_POST['service_id'] ?? null;
    $new_slot_id = $_POST['slot_id'] ?? null;

    if (!$appointment_id || !$new_service_id || !$new_slot_id) {
        $_SESSION['flash_message'] = "All fields are required.";
        header("Location: /appointments");
        exit();
    }

    try {
        $pdo->beginTransaction();

        $appointment = Appointment::findByIdAndUser($pdo, $appointment_id, $user_id);
        if (!$appointment) {
            throw new Exception("Unauthorized appointment modification.");
        }

        $newSlot = AvailableSlot::findById($pdo, $new_slot_id);
        if (!$newSlot || ($newSlot->isBooked() && $appointment->getSlotId() !== $new_slot_id)) {
            throw new Exception("Selected slot is unavailable.");
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
        $_SESSION['flash_message'] = "Appointment updated successfully!";
        header("Location: /appointments");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Error: " . htmlspecialchars($e->getMessage());
        header("Location: /appointments");
        exit();
    }
}

// 3. Appointment Cancellation
if ($action === 'cancel') {
    $appointment_id = $_POST['appointment_id'] ?? null;

    if (!$appointment_id) {
        $_SESSION['flash_message'] = "Appointment ID is missing.";
        header('Location: /appointments');
        exit();
    }

    try {
        $pdo->beginTransaction();

        $appointment = Appointment::findByIdAndUser($pdo, $appointment_id, $user_id);
        if (!$appointment) {
            throw new Exception("Appointment not found or unauthorized.");
        }

        if ($appointment->getStatus() === 'Cancelled') {
            throw new Exception("Appointment is already cancelled.");
        }

        $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?")
            ->execute([$appointment_id]);

        $slot = AvailableSlot::findById($pdo, $appointment->getSlotId());
        if ($slot) {
            $slot->unbook($pdo);
        }

        $pdo->commit();
        $_SESSION['flash_message'] = "Appointment cancelled successfully!";
        header('Location: /appointments');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Error: " . htmlspecialchars($e->getMessage());
        header('Location: /appointments');
        exit();
    }
}

// 4. Invalid Action
$_SESSION['flash_message'] = "Invalid action.";
header('Location: /appointments');
exit();