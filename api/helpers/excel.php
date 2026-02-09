<?php
// ============================================
// JSchoolAdmin — CSV/Excel Import & Export Helper
// Pure PHP — no Composer/PhpSpreadsheet needed
// ============================================

/**
 * Parse a CSV file and return rows as arrays
 */
function parseCSV(string $filePath): array {
    $rows = [];
    if (($handle = fopen($filePath, 'r')) !== false) {
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }
        // Normalize headers
        $headers = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '_', $h), '_'));
        }, $headers);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $rows[] = array_combine($headers, $row);
            }
        }
        fclose($handle);
    }
    return $rows;
}

/**
 * Export data as CSV download
 */
function exportCSV(array $data, string $filename, array $headers): void {
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Write headers
    fputcsv($output, $headers);

    // Write data rows
    foreach ($data as $row) {
        $line = [];
        foreach ($headers as $header) {
            $key    = strtolower(str_replace(' ', '_', $header));
            $line[] = $row[$key] ?? '';
        }
        fputcsv($output, $line);
    }

    fclose($output);
    exit;
}

/**
 * Import students from CSV and return result summary
 */
function importStudentsFromCSV(string $filePath): array {
    $rows      = parseCSV($filePath);
    $db        = getDB();
    $imported  = 0;
    $duplicates = [];
    $errors    = [];

    $requiredFields = ['admission_no', 'full_name', 'class', 'parent_phone'];

    foreach ($rows as $i => $row) {
        $lineNum = $i + 2; // +1 for header, +1 for 1-indexed

        // Check required
        foreach ($requiredFields as $field) {
            if (empty($row[$field] ?? '')) {
                $errors[] = "Row $lineNum: Missing $field";
                continue 2;
            }
        }

        // Check for duplicate admission_no
        $stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE admission_no = :no");
        $stmt->execute([':no' => trim($row['admission_no'])]);
        if ((int) $stmt->fetchColumn() > 0) {
            $duplicates[] = "Row $lineNum: Admission No {$row['admission_no']} already exists";
            continue;
        }

        try {
            $stmt = $db->prepare(
                "INSERT INTO students (admission_no, name, class, section, roll_no, gender, date_of_birth,
                    blood_group, father_name, mother_name, parent_phone, whatsapp_number, parent_email, address, emergency_contact)
                 VALUES (:adm, :name, :class, :section, :roll, :gender, :dob,
                    :blood, :father, :mother, :phone, :whatsapp, :email, :addr, :emergency)"
            );
            $stmt->execute([
                ':adm'       => trim($row['admission_no']),
                ':name'      => trim($row['full_name']),
                ':class'     => trim($row['class']),
                ':section'   => trim($row['section'] ?? ''),
                ':roll'      => !empty($row['roll_no']) ? (int) $row['roll_no'] : null,
                ':gender'    => strtolower(trim($row['gender'] ?? '')),
                ':dob'       => trim($row['date_of_birth'] ?? '') ?: null,
                ':blood'     => trim($row['blood_group'] ?? ''),
                ':father'    => trim($row['father_name'] ?? ''),
                ':mother'    => trim($row['mother_name'] ?? ''),
                ':phone'     => trim($row['parent_phone']),
                ':whatsapp'  => trim($row['whatsapp'] ?? ''),
                ':email'     => trim($row['email'] ?? ''),
                ':addr'      => trim($row['address'] ?? ''),
                ':emergency' => trim($row['emergency_contact'] ?? ''),
            ]);
            $imported++;
        } catch (PDOException $e) {
            $errors[] = "Row $lineNum: Database error";
        }
    }

    return [
        'total_rows' => count($rows),
        'imported'   => $imported,
        'duplicates' => $duplicates,
        'errors'     => $errors,
    ];
}

/**
 * Import teachers from CSV and return result summary
 */
function importTeachersFromCSV(string $filePath): array {
    $rows      = parseCSV($filePath);
    $db        = getDB();
    $imported  = 0;
    $duplicates = [];
    $errors    = [];

    $requiredFields = ['employee_id', 'full_name', 'phone'];

    foreach ($rows as $i => $row) {
        $lineNum = $i + 2;

        foreach ($requiredFields as $field) {
            if (empty($row[$field] ?? '')) {
                $errors[] = "Row $lineNum: Missing $field";
                continue 2;
            }
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM teachers WHERE employee_id = :eid");
        $stmt->execute([':eid' => trim($row['employee_id'])]);
        if ((int) $stmt->fetchColumn() > 0) {
            $duplicates[] = "Row $lineNum: Employee ID {$row['employee_id']} already exists";
            continue;
        }

        // Parse subjects & classes (comma-separated → JSON)
        $subjects = !empty($row['subjects'])
            ? json_encode(array_map('trim', explode(',', $row['subjects'])))
            : null;
        $classes = !empty($row['classes'])
            ? json_encode(array_map('trim', explode(',', $row['classes'])))
            : null;

        try {
            $stmt = $db->prepare(
                "INSERT INTO teachers (employee_id, name, gender, date_of_birth, phone, whatsapp, email,
                    address, qualification, experience_years, joining_date, subjects, classes_assigned, employment_type)
                 VALUES (:eid, :name, :gender, :dob, :phone, :whatsapp, :email,
                    :addr, :qual, :exp, :join, :subjects, :classes, :emp_type)"
            );
            $stmt->execute([
                ':eid'      => trim($row['employee_id']),
                ':name'     => trim($row['full_name']),
                ':gender'   => strtolower(trim($row['gender'] ?? '')),
                ':dob'      => trim($row['date_of_birth'] ?? '') ?: null,
                ':phone'    => trim($row['phone']),
                ':whatsapp' => trim($row['whatsapp'] ?? ''),
                ':email'    => trim($row['email'] ?? ''),
                ':addr'     => trim($row['address'] ?? ''),
                ':qual'     => trim($row['qualification'] ?? ''),
                ':exp'      => !empty($row['experience__years_']) ? (int) $row['experience__years_'] : 0,
                ':join'     => trim($row['joining_date'] ?? '') ?: null,
                ':subjects' => $subjects,
                ':classes'  => $classes,
                ':emp_type' => strtolower(trim($row['employment_type'] ?? 'full-time')),
            ]);
            $imported++;
        } catch (PDOException $e) {
            $errors[] = "Row $lineNum: Database error";
        }
    }

    return [
        'total_rows' => count($rows),
        'imported'   => $imported,
        'duplicates' => $duplicates,
        'errors'     => $errors,
    ];
}
