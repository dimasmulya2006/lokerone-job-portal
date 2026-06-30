<?php
require_once 'config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Calculate average salary per category from DB
$salaryStats = $pdo->query("
    SELECT category,
           COUNT(*) as total_jobs,
           AVG(salary_min) as avg_min,
           AVG(salary_max) as avg_max,
           MIN(salary_min) as floor_salary,
           MAX(salary_max) as ceiling_salary
    FROM jobs
    WHERE STATUS = 'active' AND salary_visible = 1 AND salary_min IS NOT NULL
    GROUP BY category
    ORDER BY avg_max DESC
")->fetchAll();

// Hardcoded career tips
$tips = [
    ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'Optimalkan CV Kamu','desc'=>'Sesuaikan setiap CV dengan deskripsi pekerjaan. Gunakan kata kunci yang relevan dan tonjolkan pencapaian terukur (bukan hanya tugas).'],
    ['icon'=>'M13 10V3L4 14h7v7l9-11h-7z','title'=>'Bangun Personal Branding','desc'=>'Aktif di LinkedIn, buat portofolio online, dan bagikan pengetahuan Anda. Rekruter sering mencari kandidat secara aktif.'],
    ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z','title'=>'Perluas Jaringan','desc'=>'80% lowongan kerja tidak dipublikasikan. Rajin menghadiri acara industri, seminar, dan webinar untuk memperluas koneksi profesional Anda.'],
    ['icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','title'=>'Terus Belajar & Upgrade Skill','desc'=>'Ambil sertifikasi online yang relevan (Google, AWS, HubSpot, dll). Keterampilan baru bisa meningkatkan gaji Anda hingga 20-30%.'],
];

require_once 'includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="text-center mb-14">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-3">Gaji & Karier</h1>
            <p class="text-slate-500 text-lg max-w-xl mx-auto">Riset rata-rata gaji per bidang dan temukan tips untuk memaksimalkan karier Anda.</p>
        </div>

        <!-- Salary Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-14">
            <div class="p-8 border-b border-slate-100">
                <h2 class="text-2xl font-bold text-slate-900">Rata-Rata Gaji per Kategori</h2>
                <p class="text-slate-500 mt-1 text-sm">Data diambil dari lowongan aktif di LokerOne.</p>
            </div>

            <?php if (count($salaryStats) > 0): ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($salaryStats as $stat):
                    $avgMid = ($stat['avg_min'] + $stat['avg_max']) / 2;
                    // Color mapping per category
                    $catColors = [
                        'IT & Software'    => 'brand',
                        'Data & Analytics' => 'indigo',
                        'Human Resources'  => 'purple',
                        'Sales & Marketing'=> 'rose',
                        'Finance'          => 'emerald',
                        'Design'           => 'amber',
                        'Engineering'      => 'blue',
                        'Education'        => 'teal',
                        'Healthcare'       => 'green',
                        'Other'            => 'slate',
                    ];
                    $barColor = $catColors[$stat['category']] ?? 'brand';
                    $barWidths = ['brand'=>'w-4/5','indigo'=>'w-3/4','purple'=>'w-2/3','rose'=>'w-3/5','emerald'=>'w-4/5','amber'=>'w-1/2','blue'=>'w-3/4','teal'=>'w-2/3','green'=>'w-3/5','slate'=>'w-2/5'];
                    $bw = $barWidths[$barColor] ?? 'w-1/2';
                ?>
                <div class="px-8 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="sm:w-48 flex-shrink-0">
                        <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($stat['category']) ?></p>
                        <p class="text-xs text-slate-400 font-medium mt-0.5"><?= $stat['total_jobs'] ?> lowongan</p>
                    </div>
                    <div class="flex-1">
                        <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-600 rounded-full <?= $bw ?>"></div>
                        </div>
                    </div>
                    <div class="sm:w-60 text-right flex-shrink-0">
                        <p class="font-bold text-slate-900 text-sm">
                            Rp <?= number_format($stat['avg_min'], 0, ',', '.') ?>
                            &ndash;
                            Rp <?= number_format($stat['avg_max'], 0, ',', '.') ?>
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Maks: Rp <?= number_format($stat['ceiling_salary'], 0, ',', '.') ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="p-12 text-center text-slate-400">Data gaji belum tersedia.</div>
            <?php endif; ?>
        </div>

        <!-- Career Tips -->
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Tips Mengembangkan Karier</h2>
            <p class="text-slate-500 mb-8">Langkah-langkah praktis untuk membantu Anda maju lebih cepat.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($tips as $i => $tip): ?>
            <div class="job-card bg-white rounded-2xl border border-slate-200 p-6 flex gap-5">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $tip['icon'] ?>"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-2"><?= $tip['title'] ?></h3>
                    <p class="text-slate-500 text-sm leading-relaxed"><?= $tip['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
