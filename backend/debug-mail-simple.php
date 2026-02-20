<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $class = new ReflectionClass(App\Mail\Transport\GmailTransport::class);
    echo "Class loaded successfully\n";
    echo "Parent class: " . $class->getParentClass()->getName() . "\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
