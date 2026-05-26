<?php
/**
 * ErrorReport — exception raporları (DB agrega).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class ErrorReport extends Model
{
    protected static string $table = 'error_reports';

    /**
     * Hata için unique hash üret — aynı exception+file+line tek satırda toplanır.
     */
    public static function hashFor(string $exception, string $file, int $line): string
    {
        return hash('sha256', $exception . '|' . $file . '|' . $line);
    }

    /**
     * Hata raporunu kaydet (upsert). Var ise count++ ve last_seen güncelle.
     *
     * @return array{id:int, hash:string, count:int, is_new:bool}
     */
    public static function record(array $data): array
    {
        $db = Database::getInstance();
        $hash = self::hashFor(
            (string) ($data['exception'] ?? 'Unknown'),
            (string) ($data['file'] ?? ''),
            (int)    ($data['line'] ?? 0)
        );

        $now = date('Y-m-d H:i:s');
        $existing = $db->fetch("SELECT id, occurrence_count FROM error_reports WHERE hash = ?", [$hash]);

        if ($existing) {
            $db->execute(
                "UPDATE error_reports SET occurrence_count = occurrence_count + 1, last_seen_at = ?, user_note = COALESCE(NULLIF(?, ''), user_note), is_fixed = 0, fixed_at = NULL WHERE id = ?",
                [$now, (string) ($data['user_note'] ?? ''), $existing['id']]
            );
            return [
                'id'     => (int) $existing['id'],
                'hash'   => $hash,
                'count'  => (int) $existing['occurrence_count'] + 1,
                'is_new' => false,
            ];
        }

        $db->execute(
            "INSERT INTO error_reports
             (hash, exception, message, file, line, trace, request_data, server_data, user_note, reporter_ip,
              occurrence_count, first_seen_at, last_seen_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,1,?,?)",
            [
                $hash,
                substr((string) ($data['exception'] ?? 'Unknown'), 0, 191),
                (string) ($data['message'] ?? ''),
                substr((string) ($data['file'] ?? ''), 0, 500),
                (int) ($data['line'] ?? 0),
                is_array($data['trace'] ?? null) ? json_encode($data['trace']) : (string) ($data['trace'] ?? ''),
                is_array($data['request'] ?? null) ? json_encode($data['request']) : null,
                is_array($data['server'] ?? null) ? json_encode($data['server']) : null,
                substr((string) ($data['user_note'] ?? ''), 0, 500),
                substr((string) ($data['reporter_ip'] ?? ''), 0, 64),
                $now,
                $now,
            ]
        );

        return [
            'id'     => (int) $db->lastInsertId(),
            'hash'   => $hash,
            'count'  => 1,
            'is_new' => true,
        ];
    }

    public static function markFixed(int $id): void
    {
        Database::getInstance()->execute(
            "UPDATE error_reports SET is_fixed = 1, fixed_at = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $id]
        );
    }

    public static function incrementMailSent(int $id): void
    {
        Database::getInstance()->execute(
            "UPDATE error_reports SET mail_sent_count = mail_sent_count + 1 WHERE id = ?",
            [$id]
        );
    }
}
