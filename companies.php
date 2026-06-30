<?php
require_once 'config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Kolom di DB: NAME (kapital), jadi kita alias ke 'name'
$companies = $pdo->query("
    SELECT c.id, c.NAME as name, c.logo_initial, c.logo_path, c.color, c.description, c.website, c.location,
           COUNT(j.id) as job_count
    FROM companies c
    LEFT JOIN jobs j ON j.company_id = c.id AND j.STATUS = 'active'
    GROUP BY c.id
    ORDER BY job_count DESC
")->fetchAll();

require_once 'includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="text-center mb-14">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-3">Perusahaan Terpercaya</h1>
            <p class="text-slate-500 text-lg max-w-xl mx-auto">Jelajahi perusahaan-perusahaan terkemuka yang aktif membuka lowongan kerja di LokerOne.</p>
        </div>

        <!-- Company Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($companies as $company):
                $cn = $company['name'] ?? 'Tidak Diketahui';
                $colorClass = 'bg-blue-100 text-blue-600';
                if ($company['color'] == 'purple')  $colorClass = 'bg-purple-100 text-purple-600';
                if ($company['color'] == 'orange')  $colorClass = 'bg-amber-100 text-amber-600';
                if ($company['color'] == 'emerald') $colorClass = 'bg-emerald-100 text-emerald-600';
                if ($company['color'] == 'red')     $colorClass = 'bg-red-100 text-red-600';
                if ($company['color'] == 'indigo')  $colorClass = 'bg-indigo-100 text-indigo-600';
                $initial = $company['logo_initial'] ?? substr($cn, 0, 2);
            ?>
            <div class="job-card bg-white rounded-2xl border border-slate-200 p-6 flex flex-col">
                <div class="flex items-center mb-4">
                    <div class="w-14 h-14 rounded-xl <?= $colorClass ?> flex items-center justify-center font-bold text-xl mr-4 shadow-sm flex-shrink-0 overflow-hidden relative">
                        <?php if (!empty($company['logo_path'])): ?>
                            <img src="<?= htmlspecialchars($company['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= htmlspecialchars($initial) ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-lg"><?= htmlspecialchars($cn) ?></h2>
                        <p class="text-slate-400 text-sm font-medium flex items-center mt-0.5">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <?= htmlspecialchars($company['location'] ?? '-') ?>
                        </p>
                    </div>
                </div>
                <p class="text-slate-500 text-sm leading-relaxed mb-5 flex-1">
                    <?= htmlspecialchars($company['description'] ?? 'Tidak ada deskripsi.') ?>
                </p>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-brand-600 font-bold text-sm">
                        <?= $company['job_count'] ?> Lowongan Aktif
                    </span>
                    <?php if (!empty($company['website'])): ?>
                    <a href="<?= htmlspecialchars($company['website']) ?>" target="_blank"
                       class="inline-flex items-center text-xs text-slate-500 hover:text-brand-600 font-semibold transition-colors">
                        Website
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
