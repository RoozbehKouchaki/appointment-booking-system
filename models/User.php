<?php

class User implements JsonSerializable {
    private int $id;
    private string $username;
    private string $email;
    private string $password;

    // Getters & Setters
    public function getId(): int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): self { $this->username = $username; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPassword(): string { return $this->password; }

    // Set password (hashed for new users)
    public function setPassword(string $password): self {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    // Set existing hashed password (for fetched users)
    public function setHashedPassword(string $hashedPassword): self {
        $this->password = $hashedPassword;
        return $this;
    }

    // Find user by email
    public static function findByEmail(PDO $pdo, string $email): ?self {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return (new self())
            ->setId($data['id'])
            ->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setHashedPassword($data['password']);
    }

    //  Save user (insert or update)
    public function save(PDO $pdo): void {
        if (isset($this->id)) {
            // Update existing user
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$this->username, $this->email, $this->password, $this->id]);
        } else {
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$this->username, $this->email, $this->password]);
            $this->id = (int)$pdo->lastInsertId(); // Set ID after insertion
        }
    }

    //  Verify user password
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    //  Serialize for JSON (hides password)
    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
        ];
    }
}
?>