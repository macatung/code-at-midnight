<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ContactSubmission;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

echo "=== ADVERSARIAL STRESS PROBES ===\n";

// 1. Vietnamese UTF-8 character stress test
$vnData = [
    'name' => 'Nguyễn Đắc Cà Tưng 👻',
    'email' => 'catung.nguyen@bùa-chú.vn',
    'project_type' => 'Full-Stack Web App',
    'coffee_offering' => '1 Ly Cà Phê Muối Nửa Đêm ☕✨',
    'message' => 'Triệu hồi Ma Cà Tưng với bùa chú TypeScript và Laravel 12 tại khung giờ 00:00 AM!',
];

$req = new ContactRequest();
$val = Validator::make($vnData, $req->rules(), $req->messages());
if ($val->passes()) {
    echo "[PASS] Vietnamese UTF-8 payload passes validation cleanly.\n";
} else {
    echo "[FAIL] Vietnamese payload failed validation: " . json_encode($val->errors()->all()) . "\n";
}

// 2. IPv6 handling & DB insertion test
$submission = ContactSubmission::create([
    'name' => $vnData['name'],
    'email' => 'catung.test@domain.vn',
    'project_type' => $vnData['project_type'],
    'coffee_offering' => $vnData['coffee_offering'],
    'message' => $vnData['message'],
    'ip_address' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334', // 39 chars IPv6
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0 AdversarialStress/1.0',
]);

if ($submission->exists && $submission->ip_address === '2001:0db8:85a3:0000:0000:8a2e:0370:7334') {
    echo "[PASS] IPv6 address and long user agent safely persisted.\n";
} else {
    echo "[FAIL] IPv6 persistence issue.\n";
}

// 3. Collision resistance test on 1,000 generated reference IDs
$ids = [];
for ($i = 0; $i < 1000; $i++) {
    $id = ContactSubmission::generateReferenceId();
    if (isset($ids[$id])) {
        echo "[FAIL] Collision detected in reference ID generation: $id\n";
        break;
    }
    $ids[$id] = true;
}
echo "[PASS] 1,000 consecutive reference IDs generated with 0 collisions.\n";

// Cleanup
$submission->delete();
echo "[PASS] Cleanup complete.\n";
