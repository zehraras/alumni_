<?php

class AnnouncementModel {
    private string $dataFile;

    public function __construct() {
        $this->dataFile = __DIR__ . '/../data/announcements.json';
        if (!file_exists($this->dataFile)) {
            $dir = dirname($this->dataFile);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            file_put_contents($this->dataFile, '[]');
        }
    }

    public function getAll(): array {
        $json = file_get_contents($this->dataFile);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    public function getById(int $id): ?array {
        $items = $this->getAll();
        foreach ($items as $item) {
            if (isset($item['id']) && $item['id'] == $id) return $item;
        }
        return null;
    }

    public function create(array $data): array {
        $items = $this->getAll();
        $maxId = 0;
        foreach ($items as $u) {
            if (isset($u['id']) && $u['id'] > $maxId) $maxId = $u['id'];
        }
        
        $newItem = array_merge([
            'id' => $maxId + 1,
            'createdAt' => date('Y-m-d\TH:i:s\Z')
        ], $data);
        
        array_unshift($items, $newItem);
        $this->save($items);
        return $newItem;
    }

    public function update(int $id, array $updateData, bool $isPatch = false): ?array {
        $items = $this->getAll();
        $updated = null;

        foreach ($items as $key => $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                if ($isPatch) {
                    $items[$key] = array_merge($item, $updateData);
                } else {
                    $items[$key] = array_merge(['id' => $id, 'createdAt' => $item['createdAt'] ?? date('c')], $updateData);
                }
                
                $items[$key]['updatedAt'] = date('Y-m-d\TH:i:s\Z');
                $updated = $items[$key];
                break;
            }
        }

        if ($updated) {
            $this->save($items);
            return $updated;
        }
        return null;
    }

    public function delete(int $id): bool {
        $items = $this->getAll();
        $initialCount = count($items);
        
        $items = array_filter($items, function($item) use ($id) {
            return isset($item['id']) && $item['id'] != $id;
        });
        
        if (count($items) < $initialCount) {
            $this->save(array_values($items));
            return true;
        }
        return false;
    }

    private function save(array $items): void {
        file_put_contents($this->dataFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
