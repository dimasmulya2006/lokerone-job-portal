<?php
require_once 'config/config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan Kata Sandi wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            header("Location: index.php");
            exit;
        } else {
            $error = 'Email atau Kata Sandi salah.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 flex items-center justify-center bg-slate-50 relative overflow-hidden">
    <!-- Abstract Background -->
    <div class="absolute top-0 left-0 -translate-y-12 -translate-x-1/3 w-96 h-96 bg-brand-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 z-0"></div>
    <div class="absolute bottom-0 right-0 translate-y-1/3 translate-x-1/3 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 z-0"></div>

    <div class="w-full max-w-md relative z-10 px-4">
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-8 sm:p-10">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Selamat Datang</h1>
                    <p class="text-slate-500 font-medium">Masuk untuk melanjutkan ke LokerOne.</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="space-y-5">
                    <div class="form-input-container">
                        <label class="block text-sm font-bold text-slate-700 mb-2" for="email">Email</label>
                        <input type="email" id="email" name="email" required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:bg-white"
                            placeholder="nama@email.com"
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>

                    <div class="form-input-container">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-slate-700" for="password">Kata Sandi</label>
                            <a href="#" class="text-xs font-bold text-brand-600 hover:text-brand-700 hover:underline">Lupa sandi?</a>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:bg-white"
                            placeholder="••••••••">
                    </div>

                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-brand-600/20 btn-animate mt-4">
                        Masuk
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm font-medium text-slate-500">Belum punya akun? <a href="register.php" class="text-brand-600 hover:text-brand-700 font-bold hover:underline transition-all">Daftar sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
