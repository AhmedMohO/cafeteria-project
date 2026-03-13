<?php

namespace App\Models;

use Core\Model;

class Room extends Model
{
    protected $table = "rooms";

    private function defaultRooms(): array
    {
        return [
            ['no' => '2010', 'name' => 'Room 2010'],
            ['no' => '2006', 'name' => 'Room 2006'],
            ['no' => '2008', 'name' => 'Room 2008'],
        ];
    }

    private function seedRoomsIfEmpty(): void
    {
        $count = (int) $this->db->query('SELECT COUNT(*) FROM rooms')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $insert = $this->db->prepare('INSERT INTO rooms (no, name) VALUES (:no, :name)');
        foreach ($this->defaultRooms() as $room) {
            $insert->execute([
                ':no' => $room['no'],
                ':name' => $room['name'],
            ]);
        }
    }

    private function normalizeRooms(array $rooms): array
    {
        foreach ($rooms as &$room) {
            $label = trim(((string) ($room['no'] ?? '')) . ((string) ($room['name'] ?? '') !== '' ? ' - ' . (string) ($room['name'] ?? '') : ''));
            $room['label'] = $label !== '' ? $label : ('Room #' . (int) ($room['id'] ?? 0));
            $room['value'] = (string) (int) ($room['id'] ?? 0);
        }
        unset($room);

        return $rooms;
    }

    public function allRooms(): array
    {
        try {
            $stmt = $this->db->query('SELECT id, no, name FROM rooms ORDER BY no ASC, name ASC');
            $rooms = $stmt->fetchAll();

            if (empty($rooms)) {
                $this->seedRoomsIfEmpty();
                $stmt = $this->db->query('SELECT id, no, name FROM rooms ORDER BY no ASC, name ASC');
                $rooms = $stmt->fetchAll();
            }

            return $this->normalizeRooms($rooms);
        } catch (\Throwable $e) {
            return [
                ['id' => 0, 'no' => 'Room 2010', 'name' => '', 'label' => 'Room 2010', 'value' => 'Room 2010'],
                ['id' => 0, 'no' => 'Room 2006', 'name' => '', 'label' => 'Room 2006', 'value' => 'Room 2006'],
                ['id' => 0, 'no' => 'Room 2008', 'name' => '', 'label' => 'Room 2008', 'value' => 'Room 2008'],
            ];
        }
    }
}
