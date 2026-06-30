<?php
require_once 'config/config.php';
session_start();

if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = in_array($_POST['role'] ?? '', ['user','company']) ? $_POST['role'] : 'user';

    // Company-specific fields
    $companyName     = trim($_POST['company_name'] ?? '');
    $companyLocation = trim($_POST['company_location'] ?? '');
    $companyDesc     = trim($_POST['company_desc'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Nama, email, dan kata sandi wajib diisi.';
    } elseif ($role === 'company' && empty($companyName)) {
        $error = 'Nama perusahaan wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar. Silakan login.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            try {
                $pdo->beginTransaction();

                // Insert user
                $pdo->prepare("INSERT INTO users (NAME, email, PASSWORD, role) VALUES (:name, :email, :password, :role)")
                    ->execute([':name'=>$name, ':email'=>$email, ':password'=>$hashedPassword, ':role'=>$role]);
                $newUserId = $pdo->lastInsertId();

                // If company role, create company record
                if ($role === 'company') {
                    $initial = strtoupper(substr($companyName, 0, 1) . (substr($companyName, strpos($companyName.' ',' ')+1, 1) ?: substr($companyName,1,1)));
                    $colors  = ['blue','indigo','purple','emerald','orange','red'];
                    $color   = $colors[array_rand($colors)];

                    $pdo->prepare("INSERT INTO companies (NAME, logo_initial, color, description, location, user_id) VALUES (:name,:init,:color,:desc,:loc,:uid)")
                        ->execute([':name'=>$companyName,':init'=>$initial,':color'=>$color,':desc'=>$companyDesc,':loc'=>$companyLocation,':uid'=>$newUserId]);
                    $companyId = $pdo->lastInsertId();

                    // Link company_id back to user
                    $pdo->prepare("UPDATE users SET company_id=:cid WHERE id=:id")->execute([':cid'=>$companyId,':id'=>$newUserId]);
                }

                $pdo->commit();
                $success = 'Pendaftaran berhasil! Silakan login.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 flex items-center justify-center bg-slate-50 relative overflow-hidden">
    <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-96 h-96 bg-brand-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 z-0"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 z-0"></div>

    <div class="w-full max-w-lg relative z-10 px-4">
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-8 sm:p-10">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Buat Akun</h1>
                    <p class="text-slate-500 font-medium">Bergabunglah dan mulai perjalanan kariermu.</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 text-sm font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <?= htmlspecialchars($success) ?> <a href="login.php" class="underline ml-1 font-bold">Login sekarang &rarr;</a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php" class="space-y-5" id="registerForm">

                    <!-- Role Selector -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Daftar sebagai</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="role-card relative cursor-pointer">
                                <input type="radio" name="role" value="user" class="sr-only peer" <?= (($_POST['role']??'user')==='user')?'checked':'' ?>>
                                <div class="peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:ring-2 peer-checked:ring-brand-500 border-2 border-slate-200 rounded-xl p-4 text-center transition-all duration-200 hover:border-brand-300">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <p class="font-bold text-slate-900 text-sm">Pencari Kerja</p>
                                    <p class="text-slate-400 text-xs mt-0.5">Lamar & temukan karier</p>
                                </div>
                            </label>
                            <label class="role-card relative cursor-pointer">
                                <input type="radio" name="role" value="company" class="sr-only peer" <?= (($_POST['role']??'')==='company')?'checked':'' ?>>
                                <div class="peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:ring-2 peer-checked:ring-brand-500 border-2 border-slate-200 rounded-xl p-4 text-center transition-all duration-200 hover:border-brand-300">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <p class="font-bold text-slate-900 text-sm">Perusahaan</p>
                                    <p class="text-slate-400 text-xs mt-0.5">Pasang & kelola lowongan</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Common Fields -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2" id="nameLabel">Nama Lengkap Anda</label>
                        <input type="text" id="name" name="name" required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:bg-white"
                            placeholder="John Doe"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:bg-white"
                            placeholder="nama@email.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kata Sandi</label>
                        <input type="password" name="password" required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:bg-white"
                            placeholder="••••••••">
                    </div>

                    <!-- Company-specific Fields (shown via JS) -->
                    <div id="companyFields" class="<?= (($_POST['role']??'')==='company')?'':'hidden' ?> space-y-4 border-t border-slate-100 pt-5">
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Informasi Perusahaan</p>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Perusahaan <span class="text-red-500">*</span></label>
                            <input type="text" name="company_name"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:bg-white"
                                placeholder="PT. Nama Perusahaan"
                                value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Perusahaan</label>
                            <input type="text" name="company_location"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:bg-white"
                                placeholder="Jakarta, Indonesia"
                                value="<?= htmlspecialchars($_POST['company_location'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Singkat</label>
                            <textarea name="company_desc" rows="3"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:bg-white resize-none"
                                placeholder="Ceritakan tentang perusahaan Anda..."><?= htmlspecialchars($_POST['company_desc'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-brand-600/20 btn-animate mt-2">
                        Daftar Sekarang
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm font-medium text-slate-500">Sudah punya akun? <a href="login.php" class="text-brand-600 hover:text-brand-700 font-bold hover:underline">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide company fields based on role selection
document.querySelectorAll('input[name="role"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const cf = document.getElementById('companyFields');
        if (this.value === 'company') {
            cf.classList.remove('hidden');
            document.getElementById('nameLabel').textContent = 'Nama Anda (PIC / HR)';
        } else {
            cf.classList.add('hidden');
            document.getElementById('nameLabel').textContent = 'Nama Lengkap Anda';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
