<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected $table = "users";
    public function softDelete(int $userId): void
    {
        $this->updateWhere('id', $userId, ['is_active' => 0]);
    }

    public function hardDelete(int $userId): void
    {
        $this->deleteWhere('id', $userId);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $q = $this->query()->where('email', $email);

        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*) as cnt FROM users WHERE email = ? AND id != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email, $excludeId]);
            return (int) $stmt->fetch()['cnt'] > 0;
        }

        return (int) $q->count() > 0;
    }

    public function getUsers(string $search = '', string $status = 'all', int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $perPage = (int) $perPage;
        $offset  = (int) $offset;

        $sql = "SELECT u.*, r.name AS room_name, r.no AS room_no
                FROM users u
                LEFT JOIN rooms r ON u.room_id = r.id
                WHERE u.role = 'user'";

        if ($status === 'active') {
            $sql .= " AND u.is_active = 1";
        } elseif ($status === 'inactive') {
            $sql .= " AND u.is_active = 0";
        }

        $bindings = [];

        if ($search !== '') {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $like = "%{$search}%";
            $bindings[] = $like;
            $bindings[] = $like;
        }

        $sql .= " ORDER BY u.created_at DESC LIMIT $perPage OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    public function countUsers(string $search = '', string $status = 'all'): int
    {
        if ($search === '') {
            $q = $this->query()->where('role', 'user');
            if ($status === 'active') {
                $q->where('is_active', 1);
            } elseif ($status === 'inactive') {
                $q->where('is_active', 0);
            }
            return (int) $q->count();
        }

        $like = "%{$search}%";
        $sql = "SELECT COUNT(*) as cnt
              FROM users
              WHERE role = 'user'
                AND (name LIKE ? OR email LIKE ?)";

        if ($status === 'active') {
            $sql .= " AND is_active = 1";
        } elseif ($status === 'inactive') {
            $sql .= " AND is_active = 0";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$like, $like]);
        return (int) $stmt->fetch()['cnt'];
    }

    public function hasOrders(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as cnt FROM orders WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetch()['cnt'] > 0;
    }

    public function activate(int $userId): void
    {
        $this->updateWhere('id', $userId, ['is_active' => 1]);
    }
}
