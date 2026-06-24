<?php
header('Content-Type: text/plain');
$start = microtime(true);
$hash = password_hash('test', PASSWORD_BCRYPT, ['cost' => 12]);
$elapsed = (microtime(true) - $start) * 1000;
echo "Hash: $hash\n";
echo "Time: " . round($elapsed, 2) . "ms\n";
