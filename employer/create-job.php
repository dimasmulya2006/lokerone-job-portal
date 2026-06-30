<?php
require_once 'guard.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? '';
    $type = $_POST['type'] ?? '';
    $location_label = trim($_POST['location_label'] ?? '');
    $location_key = strtolower(str_replace(' ', '-', $location_label));
    $is_remote = isset($_POST['is_remote']) ? 1 : 0;
    $salary_min = (int)($_POST['salary_min'] ?? 0);
    $salary_max = (int)($_POST['salary_max'] ?? 0);
    $salary_visible = isset($_POST['salary_visible']) ? 1 : 0;
    $desc = trim($_POST['description'] ?? '');
    $reqs = trim($_POST['requirements'] ?? '');

    if (empty($title) || empty($desc) || empty($reqs)) {
        $error = 'Judul, deskripsi, dan persyaratan wajib diisi.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO jobs (company_id, title, description, requirements, category, TYPE, location_key, location_label, is_remote, salary_min, salary_max, salary_visible, STATUS) 
            VALUES (:cid, :title, :desc, :reqs, :cat, :type, :lokey, :lolab, :rem, :smin, :smax, :svis, 'active')
        ");
        $stmt->execute([
            ':cid' => $companyId,
            ':title' => $title,
            ':desc' => $desc,
            ':reqs' => $reqs,
            ':cat' => $category,
            ':type' => $type,
            ':lokey' => $location_key,
            ':lolab' => $location_label,
            ':rem' => $is_remote,
            ':smin' => $salary_min ?: null,
            ':smax' => $salary_max ?: null,
            ':svis' => $salary_visible
        ]);
        $success = 'Lowongan berhasil diterbitkan!';
    }
}

require_once '../includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Buat Lowongan Baru</h1>
                <p class="text-slate-500 mt-1">Publikasikan posisi baru untuk menjangkau kandidat terbaik.</p>
            </div>
            <a href="index.php" class="inline-flex items-center text-brand-600 font-bold hover:text-brand-800 transition-colors">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold flex items-center border border-red-200">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 text-sm font-semibold flex items-center border border-emerald-200">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 sm:p-10 space-y-8">
            
            <!-- Posisi -->
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Judul Pekerjaan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-brand-500 outline-none" placeholder="Contoh: Senior Frontend Developer">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                        <select name="category" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-brand-500 outline-none">
                            <option value="IT & Software">IT & Software</option>
                            <option value="Data & Analytics">Data & Analytics</option>
                            <option value="Human Resources">Human Resources</option>
                            <option value="Sales & Marketing">Sales & Marketing</option>
                            <option value="Finance">Finance</option>
                            <option value="Design">Design</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Education">Education</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Other">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Pekerjaan</label>
                        <select name="type" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-brand-500 outline-none">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Lokasi -->
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Lokasi & Gaji</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi (Kota/Wilayah) <span class="text-red-500">*</span></label>
                        <input type="text" name="location_label" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-brand-500 outline-none" placeholder="Contoh: Jakarta Selatan">
                    </div>
                    <div class="flex items-center mt-8">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_remote" value="1" class="w-5 h-5 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="ml-2 font-bold text-slate-700 text-sm">Bisa Kerja Remote (WFA)</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Gaji Minimum (Rp)</label>
                        <input type="number" name="salary_min" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-brand-500 outline-none" placeholder="Contoh: 8000000">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Gaji Maksimum (Rp)</label>
                        <input type="number" name="salary_max" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-brand-500 outline-none" placeholder="Contoh: 15000000">
                    </div>
                    <div class="md:col-span-2 flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="salary_visible" value="1" checked class="w-5 h-5 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="ml-2 font-bold text-slate-700 text-sm">Tampilkan info gaji ke publik</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Detail -->
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Detail Lowongan</h3>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="5" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-brand-500 outline-none resize-y" placeholder="Ceritakan tanggung jawab utama untuk posisi ini..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Persyaratan / Kualifikasi <span class="text-red-500">*</span></label>
                        <textarea name="requirements" rows="5" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:bg-white focus:border-brand-500 outline-none resize-y" placeholder="- Minimal S1&#10;- Pengalaman 2 tahun&#10;- Dst..."></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-xl shadow-md btn-animate">
                    Terbitkan Lowongan
                </button>
            </div>
        </form>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
