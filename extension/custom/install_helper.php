#!/usr/bin/env php
<?php
/**
 * Registration Upload Extension - Manual Installation Helper
 * 
 * This script helps install/verify the registration upload extension
 * Run from OpenCart root: php extension/custom/install_helper.php
 */

// Check if running from correct directory
if (!file_exists('./config.php') || !file_exists('./admin/config.php')) {
    die("ERROR: Please run this script from OpenCart root directory\n");
}

echo "=== Registration Upload Extension - Installation Helper ===\n\n";

// Load OpenCart config
require_once('./admin/config.php');

// Database connection
try {
    $db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
    if ($db->connect_error) {
        die("Database connection failed: " . $db->connect_error . "\n");
    }
    
    echo "✓ Database connected\n";
} catch (Exception $e) {
    die("Database error: " . $e->getMessage() . "\n");
}

// Check if extension files exist
$files_to_check = [
    'extension/custom/install.json',
    'extension/custom/admin/controller/other/registration_upload.php',
    'extension/custom/admin/model/other/registration_upload.php',
    'extension/custom/catalog/controller/event/registration_upload.php'
];

echo "\n--- Checking Extension Files ---\n";
$files_ok = true;
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✓ $file\n";
    } else {
        echo "✗ MISSING: $file\n";
        $files_ok = false;
    }
}

if (!$files_ok) {
    die("\nERROR: Some extension files are missing!\n");
}

// Check oc_extension table
echo "\n--- Checking Extension Registration ---\n";
$result = $db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `type` = 'other' AND `code` = 'registration_upload'");
if ($result && $result->num_rows > 0) {
    echo "✓ Extension registered in oc_extension table\n";
} else {
    echo "✗ Extension NOT registered in oc_extension table\n";
    echo "  Installing extension record...\n";
    $db->query("INSERT INTO `" . DB_PREFIX . "extension` SET `extension` = 'custom', `type` = 'other', `code` = 'registration_upload'");
    echo "✓ Extension record added\n";
}

// Check oc_extension_install table
$result = $db->query("SELECT * FROM `" . DB_PREFIX . "extension_install` WHERE `code` = 'custom'");
if ($result && $result->num_rows > 0) {
    echo "✓ Extension install record exists\n";
} else {
    echo "✗ Extension install record missing\n";
    echo "  Adding extension install record...\n";
    $db->query("INSERT INTO `" . DB_PREFIX . "extension_install` SET 
        `extension_id` = '0',
        `extension_download_id` = '0',
        `name` = 'Registration Upload Field',
        `description` = 'Adds file upload to registration form',
        `code` = 'custom',
        `version` = '1.0.0',
        `author` = 'Custom',
        `link` = '',
        `status` = '1',
        `date_added` = NOW()
    ");
    echo "✓ Extension install record added\n";
}

// Check settings
$result = $db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `code` = 'other_registration_upload'");
if ($result && $result->num_rows > 0) {
    echo "✓ Extension settings found (" . $result->num_rows . " settings)\n";
} else {
    echo "⚠ No extension settings found (normal if not yet installed via admin)\n";
}

// Check events
$result = $db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE `code` = 'custom_registration_upload'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Event registered: " . $row['trigger'] . " → " . $row['action'] . "\n";
    echo "  Status: " . ($row['status'] ? 'ENABLED' : 'DISABLED') . "\n";
} else {
    echo "⚠ No event found (will be created during installation)\n";
}

$db->close();

echo "\n=== Summary ===\n";
echo "Extension is ready to use!\n\n";
echo "Next steps:\n";
echo "1. Go to Admin → Extensions → Other\n";
echo "2. Find 'Registration Upload Field'\n";
echo "3. Click Install (green + icon) if not installed\n";
echo "4. Click Edit (pencil icon) to view uploads\n\n";
echo "If you still get 'page not found', try:\n";
echo "- Clear browser cache and cookies\n";
echo "- Clear OpenCart cache (Dashboard → Settings → Developer)\n";
echo "- Logout and login again\n\n";
