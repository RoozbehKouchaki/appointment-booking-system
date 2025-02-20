<?php
require '../config/config.php';

try {
    // Fetch users with non-hashed passwords (you can adjust the condition if needed)
    $stmt = $pdo->query("SELECT id, password FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users)) {
        exit("✅ No users found to update.");
    }

    $updatedCount = 0;

    foreach ($users as $user) {
        $password = $user['password'];

        // Check if password is already hashed (Bcrypt hashes start with '$2y$')
        if (strpos($password, '$2y$') !== 0) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Update the password in the database
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->execute([$hashedPassword, $user['id']]);
            $updatedCount++;
        }
    }

    echo "✅ Passwords updated: $updatedCount";
} catch (PDOException $e) {
    echo "❌ Error: " . htmlspecialchars($e->getMessage());
}
?>