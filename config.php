<?php
/**
 * Database connection settings.
 *
 * Local (XAMPP/WAMP): the defaults below usually work as-is.
 * On shared hosting (e.g. InfinityFree, Hostinger): your host's control
 * panel gives you the real DB_HOST, DB_NAME, DB_USER and DB_PASS —
 * replace the values below with those.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'garment_payroll');
define('DB_USER', 'root');
define('DB_PASS', 'dqsdqs');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Don't leak connection details to the browser — log them instead.
    error_log('DB connection failed: ' . $e->getMessage());
    die('Could not connect to the database. Please check config.php and make sure the "garment_payroll" database has been imported (see sql/schema.sql).');
}
