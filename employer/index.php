<?php
require_once 'guard.php';

// Fetch jobs created by this company
$stmtJobs = $pdo->prepare("
    SELECT j.*, COUNT(a.id) as applicant_count
    FROM jobs j
    LEFT JOIN applications a ON j.id = a.job_id
    WHERE j.company_id = :cid
    GROUP BY j.id
    ORDER BY j.created_at DESC
");
$stmtJobs->execute([':cid' => $companyId]);
$jobs = $stmtJobs->fetchAll();

// Get total stats
$totalJobs = count($jobs);
$totalApplicants = 0;
foreach ($jobs as $j) $totalApplicants += $j['applicant_count'];

require_once '../includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Dashboard Header -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8 flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center gap-6 mb-6 md:mb-0">
                <div class="w-20 h-20 rounded-2xl bg-brand-100 flex items-center justify-center font-bold text-4xl text-brand-600 shadow-sm flex-shrink-0 overflow-hidden relative">
                    <?php if (!empty($company['logo_path'])): ?>
                        <img src="../<?= htmlspecialchars($company['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= htmlspecialchars($company['logo_initial']) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($company['name']) ?></h1>
                    <p class="text-slate-500 font-medium">Dashboard Perusahaan</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="bg-slate-50 border border-slate-200 px-6 py-3 rounded-xl text-center">
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Lowongan</p>
                    <p class="text-2xl font-bold text-slate-900"><?= $totalJobs ?></p>
                </div>
                <div class="bg-slate-50 border border-slate-200 px-6 py-3 rounded-xl text-center">
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Pelamar</p>
                    <p class="text-2xl font-bold text-slate-900"><?= $totalApplicants ?></p>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Kelola Lowongan</h2>
            <a href="create-job.php" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md btn-animate inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Lowongan
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <?php if (count($jobs) > 0): ?>
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Posisi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pelamar</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($jobs as $job): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= htmlspecialchars($job['title']) ?></div>
                                <div class="text-sm text-slate-500"><?= htmlspecialchars($job['category']) ?> • <?= htmlspecialchars($job['TYPE'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($job['STATUS'] == 'active'): ?>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">Aktif</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full border border-slate-200">Ditutup</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?= $job['applicant_count'] ?></div>
                                <a href="applicants.php?job_id=<?= $job['id'] ?>" class="text-xs font-semibold text-brand-600 hover:underline">Lihat Detail &rarr;</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                <?= date('d M Y', strtotime($job['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="../job-detail.php?id=<?= $job['id'] ?>" target="_blank" class="text-slate-400 hover:text-brand-600 mx-2" title="Lihat Lowongan">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada lowongan</h3>
                    <p class="text-slate-500 mb-6">Mulai publikasikan lowongan pertama Anda untuk mencari talenta terbaik.</p>
                    <a href="create-job.php" class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md btn-animate">Buat Lowongan Sekarang</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
