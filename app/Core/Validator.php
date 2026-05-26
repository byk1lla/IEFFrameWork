<?php
/**
 * Validator — kural tabanlı form/input doğrulama.
 *
 * Kullanım:
 *   $v = Validator::make($data, [
 *       'email'    => 'required|email|max:191',
 *       'password' => 'required|min:8|confirmed',
 *       'role'     => 'in:admin,user',
 *   ]);
 *   if ($v->fails()) { ... $v->errors() ... }
 *
 * Custom rule eklemek için extend ile yeni validate<Name>() method'u tanımla.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $customMessages;
    protected array $errors = [];

    protected array $defaultMessages = [
        'required'  => ':field alanı zorunludur.',
        'email'     => ':field geçerli bir e-posta adresi olmalıdır.',
        'min'       => ':field en az :param karakter olmalıdır.',
        'max'       => ':field en fazla :param karakter olmalıdır.',
        'numeric'   => ':field sayısal bir değer olmalıdır.',
        'integer'   => ':field tam sayı olmalıdır.',
        'string'    => ':field metin olmalıdır.',
        'array'     => ':field dizi olmalıdır.',
        'boolean'   => ':field doğru/yanlış olmalıdır.',
        'date'      => ':field geçerli bir tarih olmalıdır.',
        'unique'    => ':field zaten kullanılıyor.',
        'exists'    => ':field geçerli değil.',
        'confirmed' => ':field onayı eşleşmiyor.',
        'regex'     => ':field formatı geçersiz.',
        'in'        => ':field seçilen değer geçersiz.',
        'phone'     => ':field geçerli bir telefon numarası olmalıdır.',
        'url'       => ':field geçerli bir URL olmalıdır.',
        'between'   => ':field :min ile :max arasında olmalıdır.',
        'same'      => ':field değeri eşleşmiyor.',
        'alpha'     => ':field sadece harf içermelidir.',
        'alphanum'  => ':field sadece harf ve rakam içermelidir.',
    ];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data           = $data;
        $this->rules          = $rules;
        $this->customMessages = $messages;
    }

    public static function make(array $data, array $rules, array $messages = []): self
    {
        return new self($data, $rules, $messages);
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $ruleSet = is_string($rules) ? explode('|', $rules) : (array) $rules;
            $value   = $this->getValue($field);
            foreach ($ruleSet as $rule) {
                $rule = trim($rule);
                if ($rule === '') continue;
                $this->applyRule($field, $value, $rule);
            }
        }
        return empty($this->errors);
    }

    public function fails(): bool  { return !$this->validate(); }
    public function passes(): bool { return $this->validate(); }

    public function errors(): array     { return $this->errors; }
    public function firstError(string $f): ?string { return $this->errors[$f][0] ?? null; }

    public function allErrors(): array
    {
        $all = [];
        foreach ($this->errors as $list) {
            foreach ($list as $msg) $all[] = $msg;
        }
        return $all;
    }

    /** Validate edilmiş + rules'da olan key'lerin değerlerini döndür. */
    public function validated(): array
    {
        if ($this->errors) return [];
        return array_intersect_key($this->data, $this->rules);
    }

    // ─── Internal ──────────────────────────────────────────────────
    protected function getValue(string $field): mixed
    {
        $value = $this->data;
        foreach (explode('.', $field) as $seg) {
            if (!is_array($value) || !array_key_exists($seg, $value)) return null;
            $value = $value[$seg];
        }
        return $value;
    }

    protected function applyRule(string $field, mixed $value, string $rule): void
    {
        [$name, $rest] = array_pad(explode(':', $rule, 2), 2, null);
        $params = $rest !== null ? array_map('trim', explode(',', $rest)) : [];

        $method = 'validate' . ucfirst($name);
        if (!method_exists($this, $method)) return;

        if (!$this->$method($value, $params, $field)) {
            $this->addError($field, $name, $params);
        }
    }

    protected function addError(string $field, string $rule, array $params = []): void
    {
        $msg = $this->customMessages["$field.$rule"]
            ?? $this->customMessages[$field]
            ?? $this->defaultMessages[$rule]
            ?? "$field geçersiz.";

        $msg = strtr($msg, [
            ':field' => $field,
            ':param' => $params[0] ?? '',
            ':min'   => $params[0] ?? '',
            ':max'   => $params[1] ?? ($params[0] ?? ''),
        ]);
        $this->errors[$field][] = $msg;
    }

    // ─── Rules ─────────────────────────────────────────────────────
    protected function validateRequired(mixed $v): bool
    {
        if (is_null($v)) return false;
        if (is_string($v) && trim($v) === '') return false;
        if (is_array($v) && count($v) === 0) return false;
        return true;
    }

    protected function validateEmail(mixed $v): bool
    {
        if ($v === null || $v === '') return true;
        return filter_var($v, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin(mixed $v, array $p): bool
    {
        if ($v === null || $v === '') return true;
        $min = (int) ($p[0] ?? 0);
        if (is_array($v)) return count($v) >= $min;
        // min/max her zaman karakter uzunluğu ölçer; sayısal karşılaştırma için gte/lte kullan.
        return mb_strlen((string) $v) >= $min;
    }

    protected function validateMax(mixed $v, array $p): bool
    {
        if ($v === null || $v === '') return true;
        $max = (int) ($p[0] ?? PHP_INT_MAX);
        if (is_array($v)) return count($v) <= $max;
        return mb_strlen((string) $v) <= $max;
    }

    protected function validateGte(mixed $v, array $p): bool
    {
        if ($v === null || $v === '') return true;
        return is_numeric($v) && (float) $v >= (float) ($p[0] ?? 0);
    }

    protected function validateLte(mixed $v, array $p): bool
    {
        if ($v === null || $v === '') return true;
        return is_numeric($v) && (float) $v <= (float) ($p[0] ?? PHP_INT_MAX);
    }

    protected function validateNumeric(mixed $v): bool   { return $v === null || $v === '' || is_numeric($v); }
    protected function validateInteger(mixed $v): bool   { return $v === null || $v === '' || filter_var($v, FILTER_VALIDATE_INT) !== false; }
    protected function validateString(mixed $v): bool    { return $v === null || is_string($v); }
    protected function validateArray(mixed $v): bool     { return $v === null || is_array($v); }
    protected function validateBoolean(mixed $v): bool   { return $v === null || in_array($v, [true,false,0,1,'0','1','true','false'], true); }
    protected function validateDate(mixed $v): bool      { return $v === null || $v === '' || strtotime((string) $v) !== false; }
    protected function validateUrl(mixed $v): bool       { return $v === null || $v === '' || filter_var($v, FILTER_VALIDATE_URL) !== false; }
    protected function validateAlpha(mixed $v): bool     { return $v === null || $v === '' || preg_match('/^[\p{L}]+$/u', (string) $v) === 1; }
    protected function validateAlphanum(mixed $v): bool  { return $v === null || $v === '' || preg_match('/^[\p{L}\p{N}]+$/u', (string) $v) === 1; }

    protected function validateConfirmed(mixed $v, array $p, string $field): bool
    {
        unset($p);
        return $v === $this->getValue($field . '_confirmation');
    }

    protected function validateSame(mixed $v, array $p): bool
    {
        return $v === $this->getValue((string) ($p[0] ?? ''));
    }

    protected function validateRegex(mixed $v, array $p): bool
    {
        return $v === null || $v === '' || preg_match((string) ($p[0] ?? '/.*/'), (string) $v) === 1;
    }

    protected function validateIn(mixed $v, array $p): bool
    {
        return $v === null || $v === '' || in_array((string) $v, $p, true);
    }

    protected function validatePhone(mixed $v): bool
    {
        if ($v === null || $v === '') return true;
        $c = preg_replace('/[^0-9]/', '', (string) $v);
        return strlen($c) >= 10 && strlen($c) <= 15;
    }

    protected function validateBetween(mixed $v, array $p): bool
    {
        if ($v === null || $v === '') return true;
        $min = (int) ($p[0] ?? 0);
        $max = (int) ($p[1] ?? PHP_INT_MAX);
        $len = is_numeric($v) ? (float) $v : mb_strlen((string) $v);
        return $len >= $min && $len <= $max;
    }

    protected function validateUnique(mixed $v, array $p, string $field): bool
    {
        if ($v === null || $v === '') return true;
        $table    = $p[0] ?? '';
        $column   = $p[1] ?? $field;
        $exceptId = $p[2] ?? null;
        if (!$table) return true;

        $sql  = "SELECT COUNT(*) c FROM `$table` WHERE `$column` = ?";
        $args = [$v];
        if ($exceptId !== null) { $sql .= " AND id != ?"; $args[] = $exceptId; }

        $row = Database::getInstance()->fetch($sql, $args);
        return ((int) ($row['c'] ?? 0)) === 0;
    }

    protected function validateExists(mixed $v, array $p): bool
    {
        if ($v === null || $v === '') return true;
        $table  = $p[0] ?? '';
        $column = $p[1] ?? 'id';
        if (!$table) return false;
        $row = Database::getInstance()->fetch("SELECT COUNT(*) c FROM `$table` WHERE `$column` = ?", [$v]);
        return ((int) ($row['c'] ?? 0)) > 0;
    }
}
