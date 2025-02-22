<?php

class Doctor implements JsonSerializable {
    private int $id;
    private string $name;
    private string $specialization;

    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSpecialization(): string { return $this->specialization; }

    // Setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function setSpecialization(string $specialization): self {
        $this->specialization = $specialization; 
        return $this; 
    }

    // JSON Serialization (explicit for better control)
    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'specialization' => $this->getSpecialization(),
        ];
    }
}

?>