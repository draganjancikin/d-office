<?php
# Check environment and include dbConfig file.
if (file_exists(__DIR__ . '/dbConfig.live.php')) {
    include __DIR__ . '/dbConfig.live.php';
}
else if (file_exists(__DIR__ . '/dbConfig.dev.php')) {
    include __DIR__ . '/dbConfig.dev.php';
}
else if (file_exists(__DIR__ . '/dbConfig.local.php')) {
    include __DIR__ . '/dbConfig.local.php';
}
else {
    echo "Environments files don't present!";
}
