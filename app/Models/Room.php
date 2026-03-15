<?php

namespace App\Models;

use Core\Model;

class Room extends Model
{
    protected $table = "rooms";

    public function allRooms(): array
    {
        $rooms = $this->query()
            ->select('id, no, name')
            ->orderBy('no', 'ASC')
            ->get();

        foreach ($rooms as &$room) {
            $no    = trim((string) ($room['no']   ?? ''));
            $name  = trim((string) ($room['name'] ?? ''));
            $label = $no . ($name !== '' ? ' - ' . $name : '');
            $room['label'] = $label !== '' ? $label : 'Room #' . (int) $room['id'];
            $room['value'] = (string) (int) $room['id'];
        }
        unset($room);

        return $rooms;
    }
}
