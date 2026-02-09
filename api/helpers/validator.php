<?php
// ============================================
// JSchoolAdmin — Input Validation Helper
// ============================================

class Validator {
    private array $errors = [];
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function required(string $field, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? null;
        if ($value === null || (is_string($value) && trim($value) === '')) {
            $this->errors[$field] = "$label is required";
        }
        return $this;
    }

    public function email(string $field, string $label = 'Email'): self {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label is not a valid email";
        }
        return $this;
    }

    public function phone(string $field, string $label = 'Phone'): self {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !preg_match('/^[\+]?[\d\s\-\(\)]{7,20}$/', $value)) {
            $this->errors[$field] = "$label is not a valid phone number";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && mb_strlen($value) < $min) {
            $this->errors[$field] = "$label must be at least $min characters";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && mb_strlen($value) > $max) {
            $this->errors[$field] = "$label must be less than $max characters";
        }
        return $this;
    }

    public function inList(string $field, array $allowed, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !in_array($value, $allowed)) {
            $this->errors[$field] = "$label must be one of: " . implode(', ', $allowed);
        }
        return $this;
    }

    public function date(string $field, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                $this->errors[$field] = "$label must be a valid date (YYYY-MM-DD)";
            }
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_numeric($value)) {
            $this->errors[$field] = "$label must be a number";
        }
        return $this;
    }

    public function unique(string $field, string $table, string $column, ?int $excludeId = null, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '') {
            $db  = getDB();
            $sql = "SELECT COUNT(*) FROM `$table` WHERE `$column` = :val";
            $params = [':val' => $value];
            if ($excludeId !== null) {
                $sql .= " AND id != :id";
                $params[':id'] = $excludeId;
            }
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            if ((int) $stmt->fetchColumn() > 0) {
                $this->errors[$field] = "$label already exists";
            }
        }
        return $this;
    }

    public function hasErrors(): bool {
        return !empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * If validation fails, send error response and exit
     */
    public function validate(): void {
        if ($this->hasErrors()) {
            jsonError('Validation failed', 422, $this->errors);
        }
    }
}

/**
 * Sanitize a string for safe output
 */
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Get JSON body input
 */
function getJsonInput(): array {
    $input = json_decode(file_get_contents('php://input'), true);
    return is_array($input) ? $input : [];
}
