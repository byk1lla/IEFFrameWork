<?php
/**
 * Schema — driver-aware tablo oluşturma/silme.
 *
 * Migration'lar bunu kullanır. Blueprint ile akıcı API sunar.
 *
 *   Schema::create('users', function (Blueprint $t) {
 *       $t->id();
 *       $t->string('email')->unique();
 *       $t->timestamps();
 *   });
 *
 *   Schema::drop('users');
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Schema
{
    public static function create(string $table, callable $callback): void
    {
        $db = Database::getInstance();
        $bp = new Blueprint($table, $db->getDriver());
        $callback($bp);
        foreach (self::splitStatements($bp->toSql()) as $sql) {
            if (trim($sql) !== '') $db->exec($sql);
        }
    }

    public static function drop(string $table): void
    {
        Database::getInstance()->exec("DROP TABLE IF EXISTS `$table`");
    }

    public static function dropIfExists(string $table): void
    {
        self::drop($table);
    }

    public static function hasTable(string $table): bool
    {
        $db     = Database::getInstance();
        $driver = $db->getDriver();
        if ($driver === 'sqlite') {
            $row = $db->fetch("SELECT name FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
            return $row !== null;
        }
        $row = $db->fetch(
            "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
            [$table]
        );
        return $row !== null;
    }

    public static function rename(string $from, string $to): void
    {
        $db = Database::getInstance();
        if ($db->getDriver() === 'sqlite') {
            $db->exec("ALTER TABLE \"$from\" RENAME TO \"$to\"");
        } else {
            $db->exec("RENAME TABLE `$from` TO `$to`");
        }
    }

    /**
     * Birden fazla statement'lı SQL'i (CREATE TABLE + CREATE INDEX ...) ayır.
     * Basit splitter — string içindeki ';' karakterlerini kontrol etmez ama
     * Schema Builder'ımız onları üretmediği için yeterli.
     */
    protected static function splitStatements(string $sql): array
    {
        $parts = array_filter(array_map('trim', explode(";\n", trim($sql, "; \n\r\t"))));
        return array_values($parts);
    }
}
