<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected $table = 'users';

    private const SOFT_DELETE_EMAIL_PREFIX = 'deleted-user-';
    private const SOFT_DELETE_EMAIL_DOMAIN = '@local.invalid';

    private function softDeleteEmailLikePattern(): string
    {
        return self::SOFT_DELETE_EMAIL_PREFIX . '%' . self::SOFT_DELETE_EMAIL_DOMAIN;
    }

    private function buildSoftDeleteEmail(int $userId): string
    {
        return self::SOFT_DELETE_EMAIL_PREFIX . $userId . '-' . time() . self::SOFT_DELETE_EMAIL_DOMAIN;
    }

    public function softDelete(int $userId): void
    {
        $user = $this->find($userId);
        if (!$user) {
            return;
        }

        $randomSecret = uniqid('deleted_', true);

        $this->updateWhere('id', $userId, [
            'name' => 'Deleted User',
            'email' => $this->buildSoftDeleteEmail($userId),
            'password' => password_hash($randomSecret, PASSWORD_BCRYPT),
            'pic' => null,
            'room_id' => null,
            'ext' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function hardDelete(int $userId): void
    {
        $this->deleteWhere('id', $userId);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt
                FROM users
                WHERE email = ?
                  AND email NOT LIKE ?";

        $bindings = [$email, $this->softDeleteEmailLikePattern()];

        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $bindings[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);

        return (int) ($stmt->fetch()['cnt'] ?? 0) > 0;
    }

    public function getUsers(string $search = '', int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $perPage = (int) $perPage;
        $offset  = (int) $offset;

        $sql = "SELECT u.*, r.name AS room_name, r.no AS room_no
                FROM users u
                LEFT JOIN rooms r ON u.room_id = r.id
            WHERE u.role = 'user'
              AND u.email NOT LIKE ?";

        $bindings = [$this->softDeleteEmailLikePattern()];

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

    public function countUsers(string $search = ''): int
    {
        $sql = "SELECT COUNT(*) as cnt
                FROM users
                WHERE role = 'user'
                  AND email NOT LIKE ?";

        $bindings = [$this->softDeleteEmailLikePattern()];

        if ($search === '') {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($bindings);

            return (int) ($stmt->fetch()['cnt'] ?? 0);
        }

        $like = "%{$search}%";
        $sql .= ' AND (name LIKE ? OR email LIKE ?)';
        $bindings[] = $like;
        $bindings[] = $like;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);

        return (int) ($stmt->fetch()['cnt'] ?? 0);
    }

    public function hasOrders(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as cnt FROM orders WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetch()['cnt'] > 0;
    }
}
