<?php
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments (lines starting with #)
        if (strpos(trim($line), '#') === 0) continue;

        // Split by the first '=' found
        list($name, $value) = explode('=', $line, 2);
        
        $name = trim($name);
        $value = trim($value);

        // Put into environment variables and $_ENV superglobal
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
    }
    return true;
}