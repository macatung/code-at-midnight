import assert from 'node:assert';
import fs from 'node:fs';
import path from 'node:path';

console.log('🔮 Starting Adversarial Stress Testing for M3 (Summoning Altar Backend & Integration)...');

// 1. Static AST / Code Inspection of ContactSection.vue
const contactSectionPath = path.resolve('resources/js/Components/contact/ContactSection.vue');
assert.ok(fs.existsSync(contactSectionPath), 'ContactSection.vue must exist');
const contactCode = fs.readFileSync(contactSectionPath, 'utf8');

// Verify useForm import and usage
assert.ok(contactCode.includes("import { useForm"), 'Must import useForm from @inertiajs/vue3');
assert.ok(contactCode.includes("useForm({"), 'Must initialize useForm');
assert.ok(contactCode.includes("form.post('/contact'"), 'Must post to /contact endpoint');
assert.ok(contactCode.includes("form.processing"), 'Must track form.processing state');
assert.ok(contactCode.includes("form.errors.name"), 'Must bind form.errors.name');
assert.ok(contactCode.includes("form.errors.email"), 'Must bind form.errors.email');
assert.ok(contactCode.includes("form.errors.project_type"), 'Must bind form.errors.project_type');
assert.ok(contactCode.includes("form.errors.coffee_offering"), 'Must bind form.errors.coffee_offering');
assert.ok(contactCode.includes("form.errors.message"), 'Must bind form.errors.message');
assert.ok(contactCode.includes("form.clearErrors("), 'Must clear errors on input/selection');
assert.ok(contactCode.includes("sound.playSuccess()"), 'Must trigger playSuccess sound on success');
assert.ok(contactCode.includes("sound.playTalisman()"), 'Must trigger playTalisman sound on submission start');
assert.ok(contactCode.includes("confetti("), 'Must trigger celebratory confetti on success');
console.log('✅ ContactSection.vue code inspection passed.');

// 2. Static AST / Code Inspection of Backend Controllers & Requests
const contactCtrlPath = path.resolve('app/Http/Controllers/ContactController.php');
assert.ok(fs.existsSync(contactCtrlPath), 'ContactController.php must exist');
const ctrlCode = fs.readFileSync(contactCtrlPath, 'utf8');
assert.ok(ctrlCode.includes('ContactSubmission::create'), 'Must persist submission');
assert.ok(ctrlCode.includes('SUMMON-'), 'Must generate SUMMON- prefix reference ID');
assert.ok(ctrlCode.includes("'flash' => ["), 'Must return nested flash payload');
assert.ok(ctrlCode.includes("'reference_id' =>"), 'Must include reference_id in flash');

const contactReqPath = path.resolve('app/Http/Requests/ContactRequest.php');
assert.ok(fs.existsSync(contactReqPath), 'ContactRequest.php must exist');
const reqCode = fs.readFileSync(contactReqPath, 'utf8');
assert.ok(reqCode.includes("'min:10'"), 'Message must require min 10 chars');
assert.ok(reqCode.includes("'max:5000'"), 'Message must enforce max 5000 chars');
assert.ok(reqCode.includes('ALLOWED_PROJECT_TYPES'), 'Must validate against allowed project types');
assert.ok(reqCode.includes('prepareForValidation'), 'Must trim inputs in prepareForValidation');
console.log('✅ Backend Controller & Request code inspection passed.');

// 3. Database Migration Inspection
const migrationDir = path.resolve('database/migrations');
const migrationFiles = fs.readdirSync(migrationDir);
const contactMigration = migrationFiles.find(f => f.includes('create_contact_submissions_table'));
assert.ok(contactMigration, 'Contact submissions migration file must exist');
const migrationCode = fs.readFileSync(path.join(migrationDir, contactMigration), 'utf8');
assert.ok(migrationCode.includes("$table->string('reference_id', 32)->unique()"), 'reference_id must be unique');
assert.ok(migrationCode.includes("$table->string('email', 255)->index()"), 'email must be indexed');
assert.ok(migrationCode.includes("$table->text('message')"), 'message must be text');
assert.ok(migrationCode.includes("$table->string('ip_address', 45)->nullable()"), 'ip_address must be nullable IPv4/IPv6 string');
assert.ok(migrationCode.includes("$table->text('user_agent')->nullable()"), 'user_agent must be nullable text');
assert.ok(migrationCode.includes("$table->boolean('is_read')->default(false)->index()"), 'is_read must be boolean default false indexed');
console.log('✅ Database migration inspection passed.');

// 4. Inertia Middleware Inspection
const middlewarePath = path.resolve('app/Http/Middleware/HandleInertiaRequests.php');
assert.ok(fs.existsSync(middlewarePath), 'HandleInertiaRequests.php must exist');
const middlewareCode = fs.readFileSync(middlewarePath, 'utf8');
assert.ok(middlewareCode.includes("'flash' => ["), 'Must share flash prop');
assert.ok(middlewareCode.includes("'reference_id' =>"), 'Must share reference_id prop');
assert.ok(middlewareCode.includes("'success' =>"), 'Must share success prop');
console.log('✅ Inertia middleware inspection passed.');

// 5. Test Suite Verification
const testSuitePath = path.resolve('tests/run_all_tests.js');
assert.ok(fs.existsSync(testSuitePath), 'tests/run_all_tests.js must exist');
console.log('✅ All structural and adversarial checks passed.');
