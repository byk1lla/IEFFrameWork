<?php
/**
 * Validasyon Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

class Validator
{
    protected array $data = [];
    protected array $rules = [];
    protected array $errors = [];
    protected array $customMessages = [];

    protected array $defaultMessages = [
        'required' => ':field alanı zorunludur.',
        'email' => ':field geçerli bir e-posta adresi olmalıdır.',
        'min' => ':field en az :param karakter olmalıdır.',
        'max' => ':field en fazla :param karakter olmalıdır.',
        'numeric' => ':field sayısal bir değer olmalıdır.',
        'integer' => ':field tam sayı olmalıdır.',
        'string' => ':field metin olmalıdır.',
        'array' => ':field dizi olmalıdır.',
        'date' => ':field geçerli bir tarih olmalıdır.',
        'unique' => ':field zaten kullanılıyor.',
        'exists' => ':field geçerli değil.',
        'confirmed' => ':field onayı eşleşmiyor.',
        'regex' => ':field formatı geçersiz.',
        'in' => ':field seçilen değer geçersiz.',
        'phone' => ':field geçerli bir telefon numarası olmalıdır.',
        'url' => ':field geçerli bir URL olmalıdır.',
        'between' => ':field :min ile :max arasında olmalıdır.',
    ];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
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
            $rulesArray = is_string($rules) ? explode('|', $rules) : $rules;
            $value = $this->getValue($field);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->validate();
    }

    public function passes(): bool
    {
        return $this->validate();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function allErrors(): array
    {
        $all = [];
        foreach ($this->errors as $fieldErrors) {
            $all = array_merge($all, $fieldErrors);
        }
        return $all;
    }

    protected function getValue(string $field)
    {
        $keys = explode('.', $field);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!isset($value[$key])) return null;
            $value = $value[$key];
        }

        return $value;
    }

    protected function applyRule(string $field, $value, string $rule): void
    {
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        
        if (empty($ruleName)) {
            return;
        }

        $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

        $method = 'validate' . ucfirst($ruleName);
        
        if (method_exists($this, $method)) {
            if (!$this->$method($value, $params, $field)) {
                $this->addError($field, $ruleName, $params);
            }
        }
    }

    protected function addError(string $field, string $rule, array $params = []): void
    {
        $message = $this->customMessages["{$field}.{$rule}"] 
            ?? $this->customMessages[$field] 
            ?? $this->defaultMessages[$rule] 
            ?? 'Geçersiz değer.';

        $message = str_replace(':field', $field, $message);
        $message = str_replace(':param', $params[0] ?? '', $message);
        $message = str_replace(':min', $params[0] ?? '', $message);
        $message = str_replace(':max', $params[1] ?? $params[0] ?? '', $message);

        $this->errors[$field][] = $message;
    }

    // Validation Rules
    protected function validateRequired($value): bool
    {
        if (is_null($value)) return false;
        if (is_string($value) && trim($value) === '') return false;
        if (is_array($value) && count($value) === 0) return false;
        return true;
    }

    protected function validateEmail($value): bool
    {
        if (empty($value)) return true;
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin($value, array $params): bool
    {
        if (empty($value)) return true;
        $min = (int) ($params[0] ?? 0);
        
        // Array için count kontrolü yap
        if (is_array($value)) {
            return count($value) >= $min;
        }
        
        return mb_strlen((string) $value) >= $min;
    }

    protected function validateMax($value, array $params): bool
    {
        if (empty($value)) return true;
        $max = (int) ($params[0] ?? PHP_INT_MAX);
        
        // Array için count kontrolü yap
        if (is_array($value)) {
            return count($value) <= $max;
        }
        
        return mb_strlen((string) $value) <= $max;
    }

    protected function validateNumeric($value): bool
    {
        if (empty($value)) return true;
        return is_numeric($value);
    }

    protected function validateInteger($value): bool
    {
        if (empty($value)) return true;
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validateString($value): bool
    {
        if (is_null($value)) return true;
        return is_string($value);
    }

    protected function validateArray($value): bool
    {
        if (is_null($value)) return true;
        return is_array($value);
    }

    protected function validateDate($value): bool
    {
        if (empty($value)) return true;
        return strtotime($value) !== false;
    }

    protected function validateConfirmed($value, array $params, string $field): bool
    {
        $confirmValue = $this->getValue($field . '_confirmation');
        return $value === $confirmValue;
    }

    protected function validateRegex($value, array $params): bool
    {
        if (empty($value)) return true;
        return preg_match($params[0] ?? '/.*/', $value) === 1;
    }

    protected function validateIn($value, array $params): bool
    {
        if (empty($value)) return true;
        return in_array($value, $params);
    }

    protected function validatePhone($value): bool
    {
        if (empty($value)) return true;
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        return strlen($cleaned) >= 10 && strlen($cleaned) <= 15;
    }

    protected function validateUrl($value): bool
    {
        if (empty($value)) return true;
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    protected function validateBetween($value, array $params): bool
    {
        if (empty($value)) return true;
        $min = (int) ($params[0] ?? 0);
        $max = (int) ($params[1] ?? PHP_INT_MAX);
        $length = mb_strlen((string) $value);
        return $length >= $min && $length <= $max;
    }

    protected function validateUnique($value, array $params, string $field): bool
    {
        if (empty($value)) return true;
        
        $table = $params[0] ?? '';
        $column = $params[1] ?? $field;
        $exceptId = $params[2] ?? null;

        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $bindings = [$value];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $bindings[] = $exceptId;
        }

        $result = $db->fetch($sql, $bindings);
        return (int) $result['count'] === 0;
    }

    protected function validateExists($value, array $params): bool
    {
        if (empty($value)) return true;

        $table = $params[0] ?? '';
        $column = $params[1] ?? 'id';

        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $result = $db->fetch($sql, [$value]);
        
        return (int) $result['count'] > 0;
    }
}
