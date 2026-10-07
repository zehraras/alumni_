<?php

class UserModel {
    private string $dataFile;

    public function __construct() {
        // Path to the JSON file where data is stored
        $this->dataFile = __DIR__ . '/../data/users.json';
    }

    /**
     * READ: Get all users
     */
    public function getAllUsers(): array {
        if (!file_exists($this->dataFile)) {
            return [];
        }
        
        $json = file_get_contents($this->dataFile);
        $users = json_decode($json, true);
        
        return is_array($users) ? $users : [];
    }

    /**
     * READ: Get a single user by ID
     */
    public function getUserById(int $id): ?array {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if (isset($user['id']) && $user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }

    /**
     * CREATE: Add a new user
     */
    public function createUser(array $userData): array {
        $users = $this->getAllUsers();
        
        // Generate new ID
        $maxId = 0;
        foreach ($users as $u) {
            if (isset($u['id']) && $u['id'] > $maxId) {
                $maxId = $u['id'];
            }
        }
        
        $newUser = array_merge(['id' => $maxId + 1], $userData);
        $users[] = $newUser;
        
        $this->saveUsers($users);
        return $newUser;
    }

    /**
     * UPDATE: Update an existing user
     */
    public function updateUser(int $id, array $updateData, bool $isPatch = false): ?array {
        $users = $this->getAllUsers();
        $updatedUser = null;
        $found = false;

        foreach ($users as $key => $user) {
            if (isset($user['id']) && $user['id'] == $id) {
                if ($isPatch) {
                    // PATCH: Merge existing data with new data
                    $users[$key] = array_merge($user, $updateData);
                } else {
                    // PUT: Replace data entirely, keeping only the ID
                    $users[$key] = array_merge(['id' => $id], $updateData);
                }
                
                $users[$key]['updatedAt'] = date('Y-m-d\TH:i:s\Z');
                $updatedUser = $users[$key];
                $found = true;
                break;
            }
        }

        if ($found) {
            $this->saveUsers($users);
            return $updatedUser;
        }

        return null;
    }

    /**
     * DELETE: Remove a user by ID
     */
    public function deleteUser(int $id): bool {
        $users = $this->getAllUsers();
        $initialCount = count($users);
        
        // Filter out the user with the given ID
        $users = array_filter($users, function($user) use ($id) {
            return isset($user['id']) && $user['id'] != $id;
        });
        
        if (count($users) < $initialCount) {
            // Re-index array to prevent JSON object conversion
            $this->saveUsers(array_values($users));
            return true;
        }
        
        return false;
    }

    /**
     * Helper to write back to the JSON file
     */
    private function saveUsers(array $users): void {
        $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($this->dataFile, $json);
    }
}
