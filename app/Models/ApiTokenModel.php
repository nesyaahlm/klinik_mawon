<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiTokenModel extends Model
{
    protected $table = 'api_tokens';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'token_hash',
        'name',
        'last_used_at',
        'expires_at',
        'revoked_at',
    ];
    protected $useTimestamps = true;

    public function issueToken(int $userId, ?string $name = null): string
    {
        $plainToken = bin2hex(random_bytes(32));

        $this->insert([
            'user_id'    => $userId,
            'token_hash' => hash('sha256', $plainToken),
            'name'       => $name,
            'expires_at' => date('Y-m-d H:i:s', time() + (30 * DAY)),
        ]);

        return $plainToken;
    }

    public function findActiveToken(string $plainToken): ?array
    {
        $token = $this->where('token_hash', hash('sha256', $plainToken))
            ->where('revoked_at', null)
            ->first();

        if ($token === null) {
            return null;
        }

        if (! empty($token['expires_at']) && strtotime($token['expires_at']) < time()) {
            return null;
        }

        return $token;
    }

    public function markUsed(int $id): void
    {
        $this->update($id, ['last_used_at' => date('Y-m-d H:i:s')]);
    }

    public function revoke(string $plainToken): bool
    {
        $token = $this->findActiveToken($plainToken);

        if ($token === null) {
            return false;
        }

        return $this->update($token['id'], ['revoked_at' => date('Y-m-d H:i:s')]);
    }
}
