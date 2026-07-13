@'
<?php
try {
    new PDO("mysql:host=database;port=3306;charset=utf8mb4", "root", "helpdesk2026");
    echo "OK\n";
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}
