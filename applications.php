<?php
require_once 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$userId = $_SESSION['user_id'];

// Fetch applications — alias STATUS → status, TYPE → type, NAME → company_name
$stmt = $pdo->prepare("
    SELECT a.id, a.job_id, a.full_name, a.email, a.phone, a.cover_letter,
           a.STATUS as status, a.created_at,
           j.title as job_title, j.location_label, j.TYPE as type,
           c.NAME as company_name, c.logo_initial, c.logo_path, c.color
    FROM applications a
    LEFT JOIN jobs j ON a.job_id = j.id
    LEFT JOIN companies c ON j.company_id = c.id
    WHERE a.user_id = :uid
    ORDER BY a.created_at DESC
");
$stmt->execute([':uid' => $userId]);
$applications = $stmt->fetchAll();

$statusColors = [
    'pending'  => 'bg-amber-50 text-amber-700 border-amber-200',
    'review'   => 'bg-blue-50 text-blue-700 border-blue-200',
    'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'rejected' => 'bg-red-50 text-red-700 border-red-200',
];
$statusLabels = [
    'pending'  => 'Menunggu',
    'review'   => 'Sedang Direview',
    'accepted' => 'Diterima ✓',
    'rejected' => 'Ditolak',
];

require_once 'includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Lamaran Saya</h1>
                <p class="text-slate-500 mt-1">Riwayat semua pekerjaan yang telah Anda lamar.</p>
            </div>
            <a href="index.php" class="inline-flex items-center text-brand-600 font-bold hover:text-brand-800 transition-colors">
                &larr; Cari Lowongan
            </a>
        </div>

        <!-- Stats Bar -->
        <?php
        $counts = ['pending'=>0,'review'=>0,'accepted'=>0,'rejected'=>0];
        foreach ($applications as $a) {
            $s = $a['status'] ?? 'pending';
            if (isset($counts[$s])) $counts[$s]++;
        }
        $statItems = [
            ['label'=>'Total Lamaran','val'=>count($applications),'grad'=>'from-brand-600 to-indigo-600'],
            ['label'=>'Menunggu','val'=>$counts['pending'],'grad'=>'from-amber-500 to-orange-500'],
            ['label'=>'Diterima','val'=>$counts['accepted'],'grad'=>'from-emerald-500 to-teal-500'],
            ['label'=>'Ditolak','val'=>$counts['rejected'],'grad'=>'from-red-500 to-rose-500'],
        ];
        ?>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <?php foreach ($statItems as $s): ?>
                <div class="bg-gradient-to-br <?= $s['grad'] ?> rounded-2xl p-5 text-white shadow-md">
                    <p class="text-3xl font-extrabold"><?= $s['val'] ?></p>
                    <p class="text-sm font-semibold opacity-80 mt-1"><?= $s['label'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Application List -->
        <?php if (count($applications) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($applications as $app):
                    $colorClass = 'bg-blue-100 text-blue-600';
                    if (($app['color']??'') == 'purple')  $colorClass = 'bg-purple-100 text-purple-600';
                    if (($app['color']??'') == 'orange')  $colorClass = 'bg-amber-100 text-amber-600';
                    if (($app['color']??'') == 'emerald') $colorClass = 'bg-emerald-100 text-emerald-600';
                    if (($app['color']??'') == 'red')     $colorClass = 'bg-red-100 text-red-600';
                    if (($app['color']??'') == 'indigo')  $colorClass = 'bg-indigo-100 text-indigo-600';
                    $appStatus = $app['status'] ?? 'pending';
                    $sc = $statusColors[$appStatus] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                    $sl = $statusLabels[$appStatus] ?? 'Tidak Diketahui';
                    $dateStr = date('d M Y', strtotime($app['created_at']));
                    $initial = $app['logo_initial'] ?? substr($app['company_name'] ?? '?', 0, 2);
                ?>
                <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col sm:flex-row sm:items-center gap-4 job-card">
                    <div class="w-14 h-14 rounded-xl <?= $colorClass ?> flex items-center justify-center font-bold text-2xl flex-shrink-0 shadow-sm overflow-hidden relative">
                        <?php if (!empty($app['logo_path'])): ?>
                            <img src="<?= htmlspecialchars($app['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= htmlspecialchars($initial) ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-900 text-lg"><?= htmlspecialchars($app['job_title'] ?? 'Lowongan Dihapus') ?></h3>
                        <p class="text-slate-500 text-sm font-medium"><?= htmlspecialchars($app['company_name'] ?? '-') ?> &bull; <?= htmlspecialchars($app['location_label'] ?? '-') ?></p>
                        <p class="text-slate-400 text-xs mt-1">Dikirim: <?= $dateStr ?></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold border <?= $sc ?>"><?= $sl ?></span>
                        <a href="job-detail.php?id=<?= $app['job_id'] ?>" class="text-brand-600 hover:text-brand-800 text-sm font-bold transition-colors whitespace-nowrap">Lihat &rarr;</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-slate-200 p-16 text-center">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Lamaran</h3>
                <p class="text-slate-500 mb-8">Anda belum melamar pekerjaan apapun. Yuk temukan karier impianmu!</p>
                <a href="index.php" class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-xl shadow-md shadow-brand-600/20 btn-animate">Jelajahi Lowongan</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
