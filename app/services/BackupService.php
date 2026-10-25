<?php
/**
 * Database Backup Service
 */

class BackupService {
    public static function createBackup(): array {
        $backupDir = STORAGE_PATH . '/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup_' . date('Y_m_d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $pdo = Database::getConnection();
        $tables = ['admins', 'markets', 'results', 'scheduled_publications', 'api_configs', 'settings', 'announcements'];

        $sql = "-- Satka Matka Automated Database Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $sql .= "-- Table: {$table}\n";
            $rows = $pdo->query("SELECT * FROM {$table}")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $cols = array_keys($row);
                $vals = array_map(function($v) use ($pdo) {
                    return $v === null ? 'NULL' : $pdo->quote((string)$v);
                }, array_values($row));
                $sql .= "INSERT INTO {$table} (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
            }
            $sql .= "\n";
        }

        file_put_contents($filepath, $sql);
        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath),
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    public static function listBackups(): array {
        $backupDir = STORAGE_PATH . '/backups';
        if (!is_dir($backupDir)) return [];
        $files = glob($backupDir . '/*.sql');
        $list = [];
        foreach ($files as $file) {
            $list[] = [
                'filename' => basename($file),
                'size' => filesize($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        usort($list, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        return $list;
    }
}
