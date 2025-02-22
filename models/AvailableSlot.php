<?php

class AvailableSlot implements JsonSerializable {
    private int $id;
    private int $doctor_id;
    private string $slot_datetime;
    private bool $is_booked;

    // 🚀 Getters
    public function getId(): int { return $this->id; }
    public function getDoctorId(): int { return $this->doctor_id; }
    public function getSlotDatetime(): string { return $this->slot_datetime; }
    public function isBooked(): bool { return $this->is_booked; }

    // 🛠️ Setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setDoctorId(int $doctor_id): self { $this->doctor_id = $doctor_id; return $this; }
    public function setSlotDatetime(string $slot_datetime): self { $this->slot_datetime = $slot_datetime; return $this; }
    public function setIsBooked(bool $is_booked): self { $this->is_booked = $is_booked; return $this; }

    // 📝 Save slot changes
    public function save(PDO $pdo): void {
        if (isset($this->id)) {
            $stmt = $pdo->prepare("UPDATE available_slots SET doctor_id = ?, slot_datetime = ?, is_booked = ? WHERE id = ?");
            $stmt->execute([$this->doctor_id, $this->slot_datetime, $this->is_booked, $this->id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO available_slots (doctor_id, slot_datetime, is_booked) VALUES (?, ?, ?)");
            $stmt->execute([$this->doctor_id, $this->slot_datetime, $this->is_booked]);
            $this->id = (int)$pdo->lastInsertId();
        }
    }

    // 🔎 Find slot by ID
    public static function findById(PDO $pdo, int $id): ?self {
        $stmt = $pdo->prepare("SELECT * FROM available_slots WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromArray($data) : null;
    }

    // 🔄 Mark slot as booked
    public function book(PDO $pdo): void {
        $this->setIsBooked(true)->save($pdo);
    }

    // 🆓 Unbook slot
    public function unbook(PDO $pdo): void {
        $pdo->prepare("UPDATE available_slots SET is_booked = 0 WHERE id = ?")
            ->execute([$this->id]);
        $this->is_booked = false;
    }

    // 🔨 Helper to create an AvailableSlot object from array
    private static function createFromArray(array $data): self {
        return (new self())
            ->setId($data['id'])
            ->setDoctorId($data['doctor_id'])
            ->setSlotDatetime($data['slot_datetime'])
            ->setIsBooked((bool)$data['is_booked']);
    }

    // 📤 For JSON serialization
    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'doctor_id' => $this->getDoctorId(),
            'slot_datetime' => $this->getSlotDatetime(),
            'is_booked' => $this->isBooked(),
        ];
    }
}
?>