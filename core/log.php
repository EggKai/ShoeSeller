<?php
    const LOG_DIR = __DIR__ . "/../log/";
    function logError($errorMessage) {
        // Get the current year-month (e.g., "2023-03")
        $month = date("Y-m");
        
        // Define the full log file path.
        $logFile = LOG_DIR . "/" . $month . "errors.log";
        
        // Prepare the log entry with a timestamp.
        $date = date("Y-m-d H:i:s");
        $entry = "[" . $date . "] " . $errorMessage . PHP_EOL;
        
        // Append the error message to the log file.
        file_put_contents($logFile, $entry, FILE_APPEND);
    }
    
?>