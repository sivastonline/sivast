<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

$action = $_POST['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $action;

if ($action === 'backup') {
    try {
        // Set headers for SQL download
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="sivast_backup_' . date('Y-m-d_H-i-s') . '.sql"');
        header('Cache-Control: max-age=0');
        
        // Get all tables
        $tables = [];
        $result = $db->fetchAll("SHOW TABLES");
        foreach ($result as $row) {
            $tables[] = array_values($row)[0];
        }
        
        $output = "-- SIVAST Database Backup\n";
        $output .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        
        foreach ($tables as $table) {
            // Get table structure
            $createTable = $db->fetch("SHOW CREATE TABLE `$table`");
            $output .= "-- Table structure for `$table`\n";
            $output .= "DROP TABLE IF EXISTS `$table`;\n";
            $output .= $createTable['Create Table'] . ";\n\n";
            
            // Get table data
            $rows = $db->fetchAll("SELECT * FROM `$table`");
            if (!empty($rows)) {
                $output .= "-- Data for table `$table`\n";
                
                foreach ($rows as $row) {
                    $values = array_map(function($value) use ($db) {
                        return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                    }, array_values($row));
                    
                    $columns = array_keys($row);
                    $output .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $output .= "\n";
            }
        }
        
        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        
        echo $output;
        
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error creating backup: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Invalid action";
}
?>