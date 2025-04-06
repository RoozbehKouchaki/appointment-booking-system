<?php

class Appointment implements JsonSerializable {
    private int $id;
    private int $user_id;
    private int $slot_id;
    private int $service_id;
    private string $status;

    // 🚀 Getters
    public function getId(): int { return $this->id; }
    public function getUserId(): int { return $this->user_id; }
    public function getSlotId(): int { return $this->slot_id; }
    public function getServiceId(): int { return $this->service_id; }
    public function getStatus(): string { return $this->status; }

    // 🛠️ Setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setUserId(int $user_id): self { $this->user_id = $user_id; return $this; }
    public function setSlotId(int $slot_id): self { $this->slot_id = $slot_id; return $this; }
    public function setServiceId(int $service_id): self { $this->service_id = $service_id; return $this; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    // 📝 Save (Insert or Update)
    public function save(PDO $pdo): void {
        if (isset($this->id)) {
            // 🔄 Update existing appointment
            $stmt = $pdo->prepare("UPDATE appointments SET service_id = ?, slot_id = ?, status = ? WHERE id = ?");
            $stmt->execute([$this->service_id, $this->slot_id, $this->status, $this->id]);
        } else {
            // ➕ Insert new appointment
            $stmt = $pdo->prepare("INSERT INTO appointments (user_id, slot_id, service_id, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->user_id, $this->slot_id, $this->service_id, $this->status]);
            $this->id = (int)$pdo->lastInsertId();
        }
    }

    //  Find by ID
    public static function findById(PDO $pdo, int $id): ?self {
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromArray($data) : null;
    }

    //  Find by ID and User (for user-specific appointments)
    public static function findByIdAndUser(PDO $pdo, int $id, int $user_id): ?self {
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromArray($data) : null;
    }

    //  Helper to create an Appointment object from array
    private static function createFromArray(array $data): self {
        return (new self())
            ->setId($data['id'])
            ->setUserId($data['user_id'])
            ->setSlotId($data['slot_id'])
            ->setServiceId($data['service_id'])
            ->setStatus($data['status']);
    }

    //  For JSON serialization
    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'slot_id' => $this->getSlotId(),
            'service_id' => $this->getServiceId(),
            'status' => $this->getStatus(),
        ];
    }
}
?>