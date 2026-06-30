<?php
require_once 'config/config.php';

// Handle Search & Filter
$searchKeyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$locationFilter = isset($_GET['location']) ? $_GET['location'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Perhatikan "j.TYPE as type" untuk menghindari Undefined array key "type"
$query = "SELECT j.*, j.TYPE as type, c.NAME as company_name, c.logo_initial, c.logo_path, c.color 
          FROM jobs j 
          LEFT JOIN companies c ON j.company_id = c.id 
          WHERE j.STATUS = 'active'";
$params = [];

if (!empty($searchKeyword)) {
    $query .= " AND (j.title LIKE :kw1 OR c.NAME LIKE :kw2)";
    $params[':kw1'] = '%' . $searchKeyword . '%';
    $params[':kw2'] = '%' . $searchKeyword . '%';
}

if (!empty($locationFilter)) {
    $query .= " AND j.location_key = :location";
    $params[':location'] = $locationFilter;
}

if (!empty($categoryFilter)) {
    $query .= " AND j.category = :category";
    $params[':category'] = $categoryFilter;
}

$query .= " ORDER BY j.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

require_once 'includes/header.php';
?>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden bg-white">
        <!-- Abstract Background Decoration -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] opacity-10 pointer-events-none z-0" style="background: radial-gradient(circle, rgba(37,99,235,0.8) 0%, rgba(255,255,255,0) 70%);"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-flex items-center py-1 px-3 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-sm font-semibold mb-6">
                <span class="flex w-2 h-2 rounded-full bg-brand-600 mr-2 animate-pulse"></span>
                Lebih dari 10.000+ lowongan aktif hari ini
            </span>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                Temukan Pekerjaan Impianmu <br class="hidden md:block" />
                Bersama <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">LOKERONE</span>
            </h1>
            
            <p class="mt-4 max-w-2xl text-lg text-slate-500 mx-auto mb-10 leading-relaxed">
                Platform pencarian kerja terpercaya di Indonesia. Hubungkan bakatmu dengan perusahaan-perusahaan terkemuka dan bangun karier masa depanmu dengan mudah.
            </p>

            <!-- Search Box Component -->
            <form method="GET" action="index.php#job-list" class="max-w-4xl mx-auto bg-white p-2.5 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row">
                <div class="flex-1 flex items-center px-4 py-3 md:border-r border-slate-200">
                    <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="keyword" value="<?= htmlspecialchars($searchKeyword) ?>" placeholder="Posisi, keahlian, atau perusahaan..." class="w-full focus:outline-none text-slate-700 placeholder-slate-400 bg-transparent font-medium">
                </div>
                <div class="flex-1 flex items-center px-4 py-3 border-t md:border-t-0 border-slate-200">
                    <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <select name="location" class="w-full focus:outline-none text-slate-700 bg-transparent cursor-pointer appearance-none font-medium">
                        <option value="">Semua Lokasi</option>
                        <option value="jakarta" <?= $locationFilter == 'jakarta' ? 'selected' : '' ?>>Jakarta Raya</option>
                        <option value="bandung" <?= $locationFilter == 'bandung' ? 'selected' : '' ?>>Bandung</option>
                        <option value="surabaya" <?= $locationFilter == 'surabaya' ? 'selected' : '' ?>>Surabaya</option>
                        <option value="remote" <?= $locationFilter == 'remote' ? 'selected' : '' ?>>Remote (WFA)</option>
                    </select>
                </div>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-8 py-4 md:py-3 rounded-xl font-bold transition-colors mt-2 md:mt-0 w-full md:w-auto shadow-md shadow-brand-600/20 btn-animate">
                    Cari Lowongan
                </button>
            </form>
            
            <div class="mt-8 text-sm text-slate-500 font-medium">
                Pencarian Populer: 
                <a href="index.php?keyword=Frontend#job-list" class="text-brand-600 hover:underline">Frontend</a>, 
                <a href="index.php?keyword=Data#job-list" class="text-brand-600 hover:underline">Data</a>, 
                <a href="index.php?keyword=Marketing#job-list" class="text-brand-600 hover:underline">Marketing</a>
            </div>
        </div>
    </div>

    <!-- Featured Jobs Section -->
    <section class="py-24 bg-slate-50" id="job-list">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">
                        <?= !empty($categoryFilter) ? 'Kategori: ' . htmlspecialchars($categoryFilter) : 'Lowongan Terbaru & Tersorot' ?>
                    </h2>
                    <p class="text-slate-500 text-lg">Peluang karier terbaik yang baru saja dibuka untukmu.</p>
                </div>
                <?php if (!empty($searchKeyword) || !empty($locationFilter) || !empty($categoryFilter)): ?>
                    <a href="index.php#job-list" class="text-brand-600 font-bold hover:text-brand-800 transition-colors">Tampilkan Semua</a>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (count($jobs) > 0): ?>
                    <?php foreach ($jobs as $job): ?>
                        <?php 
                        // Determine theme color
                        $colorClass = 'bg-blue-100 text-blue-600';
                        if ($job['color'] == 'purple') $colorClass = 'bg-purple-100 text-purple-600';
                        if ($job['color'] == 'orange') $colorClass = 'bg-amber-100 text-amber-600';
                        if ($job['color'] == 'emerald') $colorClass = 'bg-emerald-100 text-emerald-600';
                        if ($job['color'] == 'red') $colorClass = 'bg-red-100 text-red-600';
                        if ($job['color'] == 'indigo') $colorClass = 'bg-indigo-100 text-indigo-600';
                        ?>
                        <div class="job-card bg-white p-6 rounded-2xl border border-slate-200 cursor-pointer flex flex-col h-full relative group" onclick="window.location.href='job-detail.php?id=<?= $job['id'] ?>'">
                            <div class="flex items-center mb-5">
                                <div class="w-14 h-14 rounded-xl <?= $colorClass ?> flex items-center justify-center font-bold text-xl mr-4 shadow-sm flex-shrink-0 overflow-hidden relative">
                                    <?php if (!empty($job['logo_path'])): ?>
                                        <img src="<?= htmlspecialchars($job['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= htmlspecialchars($job['logo_initial']) ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-lg group-hover:text-brand-600 transition-colors"><?= htmlspecialchars($job['title']) ?></h3>
                                    <p class="text-slate-500 text-sm font-medium"><?= htmlspecialchars($job['company_name']) ?></p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-3 py-1 bg-brand-50 text-brand-700 text-xs font-bold rounded-full"><?= htmlspecialchars($job['type'] ?? 'Full-time') ?></span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full"><?= htmlspecialchars($job['location_label']) ?></span>
                                <?php if ($job['is_remote']): ?>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full">Remote OK</span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-auto pt-5 border-t border-slate-100 flex justify-between items-center">
                                <span class="text-slate-800 font-bold text-lg">
                                    <?php if ($job['salary_visible']): ?>
                                        Rp <?= number_format($job['salary_min'], 0, ',', '.') ?> - <?= number_format($job['salary_max'], 0, ',', '.') ?>
                                        <span class="text-xs font-medium text-slate-400">/bln</span>
                                    <?php else: ?>
                                        Rahasia
                                    <?php endif; ?>
                                </span>
                                <?php 
                                    $date = new DateTime($job['created_at']);
                                    $now = new DateTime();
                                    $diff = $now->diff($date);
                                    if ($diff->d > 0) {
                                        $timeStr = $diff->d . " hr lalu";
                                    } elseif ($diff->h > 0) {
                                        $timeStr = $diff->h . " jam lalu";
                                    } else {
                                        $timeStr = "Baru saja";
                                    }
                                ?>
                                <span class="text-slate-400 text-xs font-medium bg-slate-50 px-2 py-1 rounded"><?= $timeStr ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-10 text-slate-500 font-medium">Tidak ada lowongan yang sesuai dengan pencarian Anda.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Eksplorasi Kategori Pekerjaan</h2>
                <p class="text-slate-500 text-lg max-w-2xl mx-auto">Telusuri berbagai kategori bidang pekerjaan yang paling banyak dicari saat ini dan temukan yang sesuai dengan keahlianmu.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Cat 1 -->
                <a href="index.php?category=IT %26 Software#job-list" class="block border border-slate-100 bg-slate-50 hover:bg-white hover:border-brand-300 hover:shadow-lg rounded-2xl p-8 text-center transition-all duration-300 group btn-animate">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-brand-600 group-hover:border-brand-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-brand-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">IT & Software</h3>
                </a>
                <!-- Cat 2 -->
                <a href="index.php?category=Data %26 Analytics#job-list" class="block border border-slate-100 bg-slate-50 hover:bg-white hover:border-brand-300 hover:shadow-lg rounded-2xl p-8 text-center transition-all duration-300 group btn-animate">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-brand-600 group-hover:border-brand-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-brand-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Data & Analytics</h3>
                </a>
                <!-- Cat 3 -->
                <a href="index.php?category=Human Resources#job-list" class="block border border-slate-100 bg-slate-50 hover:bg-white hover:border-brand-300 hover:shadow-lg rounded-2xl p-8 text-center transition-all duration-300 group btn-animate">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-brand-600 group-hover:border-brand-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-brand-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Human Resources</h3>
                </a>
                <!-- Cat 4 -->
                <a href="index.php?category=Sales %26 Marketing#job-list" class="block border border-slate-100 bg-slate-50 hover:bg-white hover:border-brand-300 hover:shadow-lg rounded-2xl p-8 text-center transition-all duration-300 group btn-animate">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-brand-600 group-hover:border-brand-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-brand-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 11V9a2 2 0 00-2-2m2 4v4a2 2 0 104 0v-1m-4-3H9m2 0h4m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Sales & Marketing</h3>
                </a>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>

<?php if (!empty($searchKeyword) || !empty($locationFilter) || !empty($categoryFilter)): ?>
<script>
    // Smooth scroll ke daftar lowongan setelah filter/search
    window.addEventListener('load', function() {
        const jobList = document.getElementById('job-list');
        if (jobList) {
            setTimeout(function(){
                jobList.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 200);
        }
    });
</script>
<?php endif; ?>
