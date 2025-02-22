<?php

class DoctorService implements JsonSerializable {
    private int $id;
    private int $doctor_id;
    private int $service_id;

    // Getters
    public function getId(): int { return $this->id; }
    public function getDoctorId(): int { return $this->doctor_id; }
    public function getServiceId(): int { return $this->service_id; }

    // Setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setDoctorId(int $doctor_id): self { $this->doctor_id = $doctor_id; return $this; }
    public function setServiceId(int $service_id): self { $this->service_id = $service_id; return $this; }

    // Explicit JSON serialization
    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'doctor_id' => $this->getDoctorId(),
            'service_id' => $this->getServiceId(),
        ];
    }
}

?>