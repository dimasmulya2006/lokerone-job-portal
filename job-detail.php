<?php
require_once 'config/config.php';
session_start();

$jobId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($jobId === 0) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT j.*, j.TYPE as type, c.name as company_name, c.logo_initial, c.logo_path, c.color, c.description as company_desc, c.location as company_location, c.website as company_website
                        FROM jobs j LEFT JOIN companies c ON j.company_id = c.id WHERE j.id = :id");
$stmt->execute([':id' => $jobId]);
$job = $stmt->fetch();
if (!$job) { echo "Lowongan tidak ditemukan."; exit; }

// Check if user already applied
$alreadyApplied = false;
if (isset($_SESSION['user_id'])) {
    $chk = $pdo->prepare("SELECT id FROM applications WHERE job_id=:jid AND user_id=:uid");
    $chk->execute([':jid'=>$jobId, ':uid'=>$_SESSION['user_id']]);
    $alreadyApplied = (bool)$chk->fetch();
}

// Handle apply form POST
$applySuccess = false;
$applyError   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    if (!isset($_SESSION['user_id'])) {
        $applyError = 'Anda harus login untuk melamar.';
    } elseif ($alreadyApplied) {
        $applyError = 'Anda sudah melamar lowongan ini.';
    } else {
        $fn  = trim($_POST['full_name'] ?? '');
        $em  = trim($_POST['email'] ?? '');
        $ph  = trim($_POST['phone'] ?? '');
        $cl  = trim($_POST['cover_letter'] ?? '');
        if (empty($fn)||empty($em)||empty($ph)||empty($cl)) {
            $applyError = 'Semua kolom wajib diisi.';
        } else {
            $pdo->prepare("INSERT INTO applications (job_id, user_id, full_name, email, phone, cover_letter, status) VALUES (:jid,:uid,:fn,:em,:ph,:cl,'pending')")
                ->execute([':jid'=>$jobId,':uid'=>$_SESSION['user_id'],':fn'=>$fn,':em'=>$em,':ph'=>$ph,':cl'=>$cl]);
            $applySuccess  = true;
            $alreadyApplied = true;
        }
    }
}

// Color class
$colorClass = 'bg-blue-100 text-blue-600';
if ($job['color'] == 'purple')  $colorClass = 'bg-purple-100 text-purple-600';
if ($job['color'] == 'orange')  $colorClass = 'bg-amber-100 text-amber-600';
if ($job['color'] == 'emerald') $colorClass = 'bg-emerald-100 text-emerald-600';
if ($job['color'] == 'red')     $colorClass = 'bg-red-100 text-red-600';
if ($job['color'] == 'indigo')  $colorClass = 'bg-indigo-100 text-indigo-600';

require_once 'includes/header.php';
?>

<div class="pt-32 pb-24 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="index.php" class="inline-flex items-center text-brand-600 font-bold hover:text-brand-800 transition-colors mb-8">
            &larr; Kembali ke Pencarian
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT: Job Details -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Header Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-5">
                        <div class="w-20 h-20 rounded-2xl <?= $colorClass ?> flex items-center justify-center font-bold text-4xl mr-6 shadow-sm flex-shrink-0 overflow-hidden relative">
                        <?php if (!empty($job['logo_path'])): ?>
                            <img src="<?= htmlspecialchars($job['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= htmlspecialchars($job['logo_initial']) ?>
                        <?php endif; ?>
                    </div>
                        <div class="flex-1">
                            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-1"><?= htmlspecialchars($job['title']) ?></h1>
                            <p class="text-slate-500 font-medium mb-3"><?= htmlspecialchars($job['company_name']) ?></p>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-brand-50 text-brand-700 text-xs font-bold rounded-full"><?= htmlspecialchars($job['type'] ?? 'Full-time') ?></span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full"><?= htmlspecialchars($job['location_label']) ?></span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full"><?= htmlspecialchars($job['category']) ?></span>
                                <?php if ($job['is_remote']): ?>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full">Remote OK</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($job['salary_visible'] && $job['salary_min']): ?>
                        <div class="mt-6 pt-6 border-t border-slate-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-bold text-slate-900">Rp <?= number_format($job['salary_min'],0,',','.') ?> – Rp <?= number_format($job['salary_max'],0,',','.') ?> <span class="text-slate-400 font-normal text-sm">/bulan</span></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Description Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Deskripsi Pekerjaan</h2>
                    <div class="text-slate-600 leading-relaxed space-y-2">
                        <?= nl2br(htmlspecialchars($job['description'])) ?>
                    </div>
                </div>

                <!-- Requirements Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Persyaratan</h2>
                    <div class="text-slate-600 leading-relaxed space-y-2">
                        <?= nl2br(htmlspecialchars($job['requirements'])) ?>
                    </div>
                </div>

                <!-- Company Card -->
                <?php if ($job['company_desc']): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Tentang <?= htmlspecialchars($job['company_name']) ?></h2>
                    <p class="text-slate-600 leading-relaxed"><?= htmlspecialchars($job['company_desc']) ?></p>
                    <?php if ($job['company_website']): ?>
                        <a href="<?= htmlspecialchars($job['company_website']) ?>" target="_blank" class="inline-flex items-center text-brand-600 font-bold hover:underline mt-4 text-sm">
                            Kunjungi Website
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Apply Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-28">
                    <?php if ($applySuccess): ?>
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-lg mb-2">Lamaran Terkirim!</h3>
                            <p class="text-slate-500 text-sm mb-5">Tim perekrut akan menghubungi Anda segera.</p>
                            <a href="applications.php" class="inline-block text-brand-600 font-bold hover:underline text-sm">Lihat Lamaran Saya &rarr;</a>
                        </div>

                    <?php elseif ($alreadyApplied): ?>
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-lg mb-2">Sudah Dilamar</h3>
                            <p class="text-slate-500 text-sm mb-5">Anda telah melamar posisi ini sebelumnya.</p>
                            <a href="applications.php" class="inline-block text-brand-600 font-bold hover:underline text-sm">Pantau Status &rarr;</a>
                        </div>

                    <?php else: ?>
                        <h2 class="text-lg font-extrabold text-slate-900 mb-1">Lamar Sekarang</h2>
                        <p class="text-slate-400 text-sm mb-5">Isi formulir di bawah untuk melamar posisi ini.</p>

                        <?php if ($applyError): ?>
                            <div class="bg-red-50 text-red-700 p-3 rounded-xl mb-4 text-xs font-semibold"><?= htmlspecialchars($applyError) ?></div>
                        <?php endif; ?>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="bg-brand-50 border border-brand-100 rounded-xl p-4 text-center mb-4">
                                <p class="text-sm text-brand-700 font-semibold mb-3">Untuk melamar pekerjaan ini, silakan buat akun atau masuk terlebih dahulu.</p>
                                <a href="login.php" class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 px-6 rounded-xl text-sm btn-animate">Login / Daftar Sekarang</a>
                            </div>
                        <?php elseif (($_SESSION['user_role'] ?? 'user') === 'company'): ?>
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 text-center mb-4">
                                <p class="text-sm text-amber-700 font-semibold">Anda login sebagai <b>Perusahaan</b>. Hanya akun pencari kerja yang dapat melamar pekerjaan.</p>
                            </div>
                        <?php else: ?>
                            <form method="POST" class="space-y-4">
                                <input type="hidden" name="apply" value="1">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                                    <input type="text" name="full_name" required
                                        value="<?= htmlspecialchars($_SESSION['user_name']) ?>"
                                        class="form-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm font-medium focus:bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Email</label>
                                    <input type="email" name="email" required
                                        class="form-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm font-medium focus:bg-white"
                                        placeholder="email@kamu.com">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Telepon</label>
                                    <input type="tel" name="phone" required
                                        class="form-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm font-medium focus:bg-white"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Surat Lamaran / Cover Letter</label>
                                    <textarea name="cover_letter" rows="5" required
                                        class="form-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm font-medium focus:bg-white resize-none"
                                        placeholder="Ceritakan kenapa Anda cocok untuk posisi ini..."></textarea>
                                </div>
                                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-brand-600/20 btn-animate">
                                    Kirim Lamaran
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
