<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== 1. SCHEMA CHECK ===\n";
$columns = Schema::getColumnListing('contact_submissions');
echo "Columns: " . implode(', ', $columns) . "\n";

$expectedColumns = [
    'id', 'reference_id', 'name', 'email', 'project_type',
    'coffee_offering', 'message', 'ip_address', 'user_agent',
    'is_read', 'created_at', 'updated_at'
];
$missing = array_diff($expectedColumns, $columns);
if (empty($missing)) {
    echo "[PASS] All expected columns exist in contact_submissions table.\n";
} else {
    echo "[FAIL] Missing columns: " . implode(', ', $missing) . "\n";
}

echo "\n=== 2. ELOQUENT MODEL CRUD & HOOKS ===\n";
$submission = ContactSubmission::create([
    'name' => 'Auditor Forensic Probe',
    'email' => 'auditor@macatung.dev',
    'project_type' => 'Full-Stack Web App',
    'coffee_offering' => 'Espresso Double Shot',
    'message' => 'Testing empirical database persistence and reference ID generation.',
    'ip_address' => '127.0.0.1',
    'user_agent' => 'ForensicAuditor/1.0',
]);

echo "Created ID: " . $submission->id . "\n";
echo "Generated Ref: " . $submission->reference_id . "\n";

if (preg_match('/^SUMMON-[A-Z0-9]{6}$/', $submission->reference_id)) {
    echo "[PASS] Reference ID format valid (SUMMON-XXXXXX).\n";
} else {
    echo "[FAIL] Reference ID format mismatch: " . $submission->reference_id . "\n";
}

// Check scopes
$unreadCount = ContactSubmission::unread()->where('id', $submission->id)->count();
echo "Unread scope count: " . $unreadCount . "\n";
if ($unreadCount === 1) {
    echo "[PASS] unread scope works.\n";
}

$byTypeCount = ContactSubmission::byProjectType('Full-Stack Web App')->where('id', $submission->id)->count();
if ($byTypeCount === 1) {
    echo "[PASS] byProjectType scope works.\n";
}

// Clean up probe record
$submission->delete();
echo "[PASS] Test probe record successfully deleted.\n";
