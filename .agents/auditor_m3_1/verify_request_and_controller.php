<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Requests\ContactRequest;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo "=== 1. FORM REQUEST VALIDATION PROBE ===\n";

$allowedTypes = ContactRequest::ALLOWED_PROJECT_TYPES;
echo "Allowed project types count: " . count($allowedTypes) . "\n";

// Test 1: Valid payload
$validData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'project_type' => 'Full-Stack Web App',
    'coffee_offering' => 'Espresso',
    'message' => 'This is a valid message that is longer than 10 characters.',
];
$request = new ContactRequest();
$validator = Validator::make($validData, $request->rules(), $request->messages());
if ($validator->passes()) {
    echo "[PASS] Valid payload passes validation.\n";
} else {
    echo "[FAIL] Valid payload failed validation: " . json_encode($validator->errors()->all()) . "\n";
}

// Test 2: Invalid email & Short message & Invalid project type
$invalidData = [
    'name' => '',
    'email' => 'not-an-email',
    'project_type' => 'Bogus Quest',
    'coffee_offering' => '',
    'message' => 'Short',
];
$validator2 = Validator::make($invalidData, $request->rules(), $request->messages());
if ($validator2->fails()) {
    $errors = $validator2->errors()->toArray();
    echo "[PASS] Invalid payload rejected. Errors found for: " . implode(', ', array_keys($errors)) . "\n";
    if (isset($errors['name']) && isset($errors['email']) && isset($errors['project_type']) && isset($errors['coffee_offering']) && isset($errors['message'])) {
        echo "[PASS] All 5 fields produced expected validation errors.\n";
    } else {
        echo "[FAIL] Some expected validation errors are missing.\n";
    }
} else {
    echo "[FAIL] Invalid payload unexpectedly passed.\n";
}

echo "\n=== 2. ROUTE PROBE ===\n";
$routes = Route::getRoutes();
$hasContact = false;
$hasSummon = false;

foreach ($routes as $route) {
    if (in_array('POST', $route->methods()) && $route->uri() === 'contact') {
        $hasContact = true;
        echo "[PASS] Found POST /contact -> " . $route->getActionName() . "\n";
    }
    if (in_array('POST', $route->methods()) && $route->uri() === 'summon') {
        $hasSummon = true;
        echo "[PASS] Found POST /summon -> " . $route->getActionName() . "\n";
    }
}

if ($hasContact && $hasSummon) {
    echo "[PASS] Both required routes are registered.\n";
} else {
    echo "[FAIL] Missing route(s).\n";
}
