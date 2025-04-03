<?php
$title = "View Logs";
include __DIR__ . '/../inc/header.php';
?>
<div class="view-logs-container __content">
    <div class="log-files-list">
        <h1 class="title">Available Logs:</h1>
        <ul>
            <?php if (!empty($logFiles)): ?>
                <?php foreach ($logFiles as $file): ?>
                    <li>
                        <a href="/admin/viewLogs&file=<?php echo urlencode($file); ?>">
                            <?php echo htmlspecialchars($file); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No log files found.</li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if (!empty($selectedFile)): ?>
        <div class="log-file-contents">
            <h2>Viewing: <?php echo htmlspecialchars($selectedFile); ?></h2>
            <?php if ($fileContents !== null): ?>
                <pre><?php echo htmlspecialchars($fileContents); ?></pre>
            <?php else: ?>
                <p>Unable to read the file or file is empty.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
