<?php
require_once 'guard.php';

$jobId = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
if (!$jobId) { header("Location: index.php"); exit; }

// Verify this job belongs to this company
$checkJob = $pdo->prepare("SELECT title FROM jobs WHERE id = :jid AND company_id = :cid");
$checkJob->execute([':jid' => $jobId, ':cid' => $companyId]);
$job = $checkJob->fetch();

if (!$job) {
    die("Lowongan tidak ditemukan atau Anda tidak memiliki akses ke lowongan ini.");
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_id'], $_POST['status'])) {
    $appId = (int)$_POST['app_id'];
    $newStatus = $_POST['status'];
    if (in_array($newStatus, ['pending','review','accepted','rejected'])) {
        $pdo->prepare("UPDATE applications SET STATUS = :st WHERE id = :id AND job_id = :jid")
            ->execute([':st' => $newStatus, ':id' => $appId, ':jid' => $jobId]);
    }
    // Redirect to prevent form resubmission
    header("Location: applicants.php?job_id=" . $jobId);
    exit;
}

// Fetch applicants — alias STATUS ke lowercase 'status'
$stmt = $pdo->prepare("SELECT id, job_id, full_name, email, phone, cover_letter, STATUS as status, created_at FROM applications WHERE job_id = :jid ORDER BY created_at DESC");
$stmt->execute([':jid' => $jobId]);
$applicants = $stmt->fetchAll();

$statusColors = [
    'pending'  => 'bg-amber-50 text-amber-700 border-amber-200',
    'review'   => 'bg-blue-50 text-blue-700 border-blue-200',
    'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'rejected' => 'bg-red-50 text-red-700 border-red-200',
];
$statusLabels = [
    'pending'  => 'Menunggu',
    'review'   => 'Sedang Direview',
    'accepted' => 'Diterima',
    'rejected' => 'Ditolak',
];

require_once '../includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8">
            <div>
                <a href="index.php" class="inline-flex items-center text-brand-600 font-bold hover:text-brand-800 transition-colors mb-2">
                    &larr; Kembali ke Dashboard
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900">Pelamar: <?= htmlspecialchars($job['title']) ?></h1>
                <p class="text-slate-500 mt-1">Total <?= count($applicants) ?> kandidat telah melamar untuk posisi ini.</p>
            </div>
        </div>

        <div class="space-y-6">
            <?php if (count($applicants) > 0): ?>
                <?php foreach ($applicants as $app): 
                    $sc = $statusColors[$app['status']] ?? $statusColors['pending'];
                ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row gap-6">
                    <div class="flex-1 space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-slate-900 text-xl"><?= htmlspecialchars($app['full_name']) ?></h3>
                                <div class="flex items-center gap-4 text-sm text-slate-500 mt-1 font-medium">
                                    <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg><?= htmlspecialchars($app['email']) ?></span>
                                    <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg><?= htmlspecialchars($app['phone']) ?></span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $sc ?>">
                                <?= $statusLabels[$app['status']] ?? 'Menunggu' ?>
                            </span>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Cover Letter / Surat Lamaran</h4>
                            <div class="bg-slate-50 p-4 rounded-xl text-slate-600 text-sm leading-relaxed border border-slate-100">
                                <?= nl2br(htmlspecialchars($app['cover_letter'])) ?>
                            </div>
                        </div>
                        <div class="text-xs font-medium text-slate-400">
                            Dikirim pada: <?= date('d M Y H:i', strtotime($app['created_at'])) ?>
                        </div>
                    </div>
                    
                    <div class="md:w-56 flex-shrink-0 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6 flex flex-col justify-center">
                        <p class="text-xs font-bold text-slate-700 mb-3 text-center md:text-left">Ubah Status Lamaran:</p>
                        <form method="POST" class="space-y-2">
                            <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                            <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm font-semibold outline-none focus:border-brand-500 cursor-pointer" onchange="this.form.submit()">
                                <?php foreach ($statusLabels as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= $app['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-16 text-center">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Pelamar</h3>
                    <p class="text-slate-500">Belum ada kandidat yang melamar posisi ini. Bagikan lowongan Anda untuk menjangkau lebih banyak kandidat.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
