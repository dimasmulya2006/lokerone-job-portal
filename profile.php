<?php
require_once 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$successMsg = '';
$errorMsg = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT id, name, email, phone, headline FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $headline = trim($_POST['headline'] ?? '');

    if (empty($name)) {
        $errorMsg = 'Nama tidak boleh kosong.';
    } else {
        // Handle password change (optional)
        if (!empty($_POST['new_password'])) {
            $currentPass = $_POST['current_password'] ?? '';
            $stmt2 = $pdo->prepare("SELECT password FROM users WHERE id = :id");
            $stmt2->execute([':id' => $userId]);
            $row = $stmt2->fetch();
            if (!password_verify($currentPass, $row['password'])) {
                $errorMsg = 'Kata sandi lama salah.';
            } else {
                $newHashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET name=:name, phone=:phone, headline=:headline, password=:password WHERE id=:id")
                    ->execute([':name'=>$name,':phone'=>$phone,':headline'=>$headline,':password'=>$newHashed,':id'=>$userId]);
                $successMsg = 'Profil & kata sandi berhasil diperbarui!';
            }
        } else {
            $pdo->prepare("UPDATE users SET name=:name, phone=:phone, headline=:headline WHERE id=:id")
                ->execute([':name'=>$name,':phone'=>$phone,':headline'=>$headline,':id'=>$userId]);
            $successMsg = 'Profil berhasil diperbarui!';
        }

        // Refresh session name
        if (empty($errorMsg)) {
            $_SESSION['user_name'] = $name;
            $stmt->execute([':id' => $userId]);
            $user = $stmt->fetch();
        }
    }
}

require_once 'includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Profil Saya</h1>
                <p class="text-slate-500 mt-1">Kelola informasi pribadi dan akun Anda.</p>
            </div>
            <a href="index.php" class="inline-flex items-center text-brand-600 font-bold hover:text-brand-800 transition-colors">
                &larr; Beranda
            </a>
        </div>

        <?php if ($successMsg): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl mb-6 flex items-center font-semibold">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center font-semibold">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php endif; ?>

        <!-- Profile Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Avatar Section -->
            <div class="bg-gradient-to-r from-brand-600 to-indigo-600 p-10 flex items-center gap-6">
                <div class="w-24 h-24 rounded-full bg-white/20 border-4 border-white/50 flex items-center justify-center text-white font-bold text-4xl shadow-lg flex-shrink-0">
                    <?= strtoupper(substr(htmlspecialchars($user['name']), 0, 1)) ?>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-white"><?= htmlspecialchars($user['name']) ?></h2>
                    <p class="text-brand-100 font-medium mt-1"><?= htmlspecialchars($user['email']) ?></p>
                    <?php if ($user['headline']): ?>
                        <p class="text-brand-200 text-sm mt-1 italic"><?= htmlspecialchars($user['headline']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Section -->
            <form method="POST" action="profile.php" class="p-8 sm:p-10 space-y-8">

                <!-- Informasi Dasar -->
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-5 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium focus:bg-white"
                                value="<?= htmlspecialchars($user['name']) ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                            <input type="email" disabled
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 font-medium cursor-not-allowed"
                                value="<?= htmlspecialchars($user['email']) ?>">
                            <p class="text-xs text-slate-400 mt-1">Email tidak dapat diubah.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Telepon</label>
                            <input type="tel" name="phone"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium focus:bg-white"
                                placeholder="Contoh: 08123456789"
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Headline Profil</label>
                            <input type="text" name="headline"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium focus:bg-white"
                                placeholder="Contoh: Software Engineer | 3 Tahun Pengalaman"
                                value="<?= htmlspecialchars($user['headline'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- Ubah Kata Sandi -->
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-5 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        Ubah Kata Sandi <span class="text-sm font-normal text-slate-400 ml-2">(opsional)</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium focus:bg-white"
                                placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kata Sandi Baru</label>
                            <input type="password" name="new_password"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium focus:bg-white"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-10 rounded-xl shadow-md shadow-brand-600/20 btn-animate">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
