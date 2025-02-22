<?php

class Service implements JsonSerializable {
    private int $id;
    private string $name;
    private int $duration;  // Duration in minutes
    private int $slots;     // Number of available slots

    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDuration(): int { return $this->duration; }
    public function getSlots(): int { return $this->slots; }

    // Setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function setDuration(int $duration): self { $this->duration = $duration; return $this; }
    public function setSlots(int $slots): self { $this->slots = $slots; return $this; }

    // Explicit JSON serialization
    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'duration' => $this->getDuration(),
            'slots' => $this->getSlots(),
        ];
    }
}

?>