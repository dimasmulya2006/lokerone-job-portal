<?php
require_once 'guard.php';

$error = '';
$success = '';

// Allowed colors
$colorOptions = [
    'blue'    => ['label' => 'Biru', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'ring' => 'ring-blue-400'],
    'indigo'  => ['label' => 'Indigo', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'ring' => 'ring-indigo-400'],
    'purple'  => ['label' => 'Ungu', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'ring' => 'ring-purple-400'],
    'emerald' => ['label' => 'Hijau', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'ring' => 'ring-emerald-400'],
    'orange'  => ['label' => 'Oranye', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'ring' => 'ring-amber-400'],
    'red'     => ['label' => 'Merah', 'bg' => 'bg-red-100', 'text' => 'text-red-600', 'ring' => 'ring-red-400'],
];

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cName     = trim($_POST['company_name'] ?? '');
    $cLocation = trim($_POST['location'] ?? '');
    $cWebsite  = trim($_POST['website'] ?? '');
    $cDesc     = trim($_POST['description'] ?? '');
    $cColor    = array_key_exists($_POST['color'] ?? '', $colorOptions) ? $_POST['color'] : 'blue';

    // Auto-generate logo initial from name
    $words = explode(' ', $cName);
    $initial = '';
    foreach (array_slice($words, 0, 2) as $w) {
        if (!empty($w)) $initial .= strtoupper($w[0]);
    }
    $initial = substr($initial, 0, 2) ?: 'CO';

    // Handle Logo Upload
    $logoPath = $company['logo_path'] ?? null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileSize = $_FILES['logo']['size'];
        $fileType = $_FILES['logo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../uploads/logos/';
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $logoPath = 'uploads/logos/' . $newFileName;
            } else {
                $error = 'Terjadi kesalahan saat mengunggah logo.';
            }
        } else {
            $error = 'Ekstensi file tidak diizinkan. Unggah file JPG, JPEG, PNG, atau GIF.';
        }
    }

    if (empty($cName)) {
        $error = 'Nama perusahaan wajib diisi.';
    } elseif (empty($error)) {
        $pdo->prepare("UPDATE companies SET NAME=:name, logo_initial=:init, color=:color, description=:desc, location=:loc, website=:web, logo_path=:logopath WHERE id=:id")
            ->execute([':name'=>$cName, ':init'=>$initial, ':color'=>$cColor, ':desc'=>$cDesc, ':loc'=>$cLocation, ':web'=>$cWebsite, ':logopath'=>$logoPath, ':id'=>$companyId]);

        $success = 'Profil perusahaan berhasil diperbarui!';

        // Refresh company data
        $stmt = $pdo->prepare("SELECT c.*, c.NAME as name FROM companies c WHERE c.id = :id");
        $stmt->execute([':id' => $companyId]);
        $company = $stmt->fetch();
    }
}

$currentColor   = $company['color'] ?? 'blue';
$cBg            = $colorOptions[$currentColor]['bg'] ?? 'bg-blue-100';
$cText          = $colorOptions[$currentColor]['text'] ?? 'text-blue-600';

require_once '../includes/header.php';
?>

<div class="min-h-screen pt-32 pb-20 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Profil Perusahaan</h1>
                <p class="text-slate-500 mt-1">Kelola informasi perusahaan Anda yang tampil ke publik.</p>
            </div>
            <a href="index.php" class="inline-flex items-center text-brand-600 font-bold hover:text-brand-800 transition-colors">
                &larr; Dashboard
            </a>
        </div>

        <?php if ($success): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl mb-6 font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Preview Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6 flex items-center gap-5 shadow-sm">
            <div id="previewAvatar" class="w-16 h-16 rounded-2xl <?= $cBg ?> <?= $cText ?> flex items-center justify-center font-bold text-2xl shadow-sm flex-shrink-0 overflow-hidden relative">
                <?php if (!empty($company['logo_path'])): ?>
                    <img src="../<?= htmlspecialchars($company['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= htmlspecialchars($company['logo_initial'] ?? 'CO') ?>
                <?php endif; ?>
            </div>
            <div>
                <h2 id="previewName" class="text-xl font-extrabold text-slate-900"><?= htmlspecialchars($company['name'] ?? '') ?></h2>
                <p id="previewLocation" class="text-slate-500 text-sm font-medium mt-0.5"><?= htmlspecialchars($company['location'] ?? '') ?></p>
                <p class="text-xs text-slate-400 mt-1">Tampilan di halaman publik</p>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <form method="POST" action="company-profile.php" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8">

                <!-- Identitas -->
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-5 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </span>
                        Identitas Perusahaan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Unggah Logo Perusahaan (Opsional)</label>
                            <input type="file" name="logo" accept="image/*"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 font-medium text-slate-900 focus:bg-white">
                            <p class="text-xs text-slate-400 mt-1">Format yang didukung: JPG, PNG, GIF. Maksimal ukuran file: 2MB.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Perusahaan <span class="text-red-500">*</span></label>
                            <input type="text" name="company_name" id="inputName" required
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 font-medium text-slate-900 focus:bg-white"
                                placeholder="Contoh: PT. Nama Perusahaan"
                                value="<?= htmlspecialchars($company['name'] ?? '') ?>">
                            <p class="text-xs text-slate-400 mt-1">Logo inisial akan dibuat otomatis jika logo perusahaan tidak diunggah.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kota / Lokasi</label>
                            <input type="text" name="location" id="inputLocation"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 font-medium text-slate-900 focus:bg-white"
                                placeholder="Contoh: Surabaya, Jawa Timur"
                                value="<?= htmlspecialchars($company['location'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Website</label>
                            <input type="url" name="website"
                                class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 font-medium text-slate-900 focus:bg-white"
                                placeholder="https://perusahaan.com"
                                value="<?= htmlspecialchars($company['website'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Warna Avatar -->
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                        </span>
                        Warna Logo Avatar
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach ($colorOptions as $colorKey => $colorVal): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="<?= $colorKey ?>" class="sr-only color-radio"
                                    <?= $currentColor === $colorKey ? 'checked' : '' ?>>
                                <div class="w-12 h-12 rounded-xl <?= $colorVal['bg'] ?> <?= $colorVal['text'] ?> flex items-center justify-center font-bold text-sm border-2 border-transparent transition-all duration-200 color-swatch <?= $currentColor === $colorKey ? 'ring-2 ring-offset-2 '.$colorVal['ring'].' border-white scale-110' : '' ?>">
                                    <?= strtoupper(substr($colorKey, 0, 1)) ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                        </span>
                        Tentang Perusahaan
                    </h3>
                    <textarea name="description" rows="6"
                        class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 font-medium text-slate-900 focus:bg-white resize-y"
                        placeholder="Ceritakan visi, misi, dan keunggulan perusahaan Anda..."><?= htmlspecialchars($company['description'] ?? '') ?></textarea>
                    <p class="text-xs text-slate-400 mt-1">Deskripsi ini akan tampil di halaman Perusahaan dan detail lowongan Anda.</p>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-10 rounded-xl shadow-md shadow-brand-600/20 btn-animate">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
// Live preview name & location
document.getElementById('inputName').addEventListener('input', function() {
    document.getElementById('previewName').textContent = this.value || 'Nama Perusahaan';
    // Auto-generate initials
    const words = this.value.trim().split(/\s+/);
    let init = '';
    for (let i = 0; i < Math.min(2, words.length); i++) {
        if (words[i]) init += words[i][0].toUpperCase();
    }
    document.getElementById('previewAvatar').textContent = init || 'CO';
});
document.getElementById('inputLocation').addEventListener('input', function() {
    document.getElementById('previewLocation').textContent = this.value || '';
});

// Color swatch selection
const avatarColorMap = {
    'blue':    ['bg-blue-100','text-blue-600'],
    'indigo':  ['bg-indigo-100','text-indigo-600'],
    'purple':  ['bg-purple-100','text-purple-600'],
    'emerald': ['bg-emerald-100','text-emerald-600'],
    'orange':  ['bg-amber-100','text-amber-600'],
    'red':     ['bg-red-100','text-red-600'],
};
const ringMap = {
    'blue':'ring-blue-400','indigo':'ring-indigo-400','purple':'ring-purple-400',
    'emerald':'ring-emerald-400','orange':'ring-amber-400','red':'ring-red-400',
};

document.querySelectorAll('.color-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        // Update swatches
        document.querySelectorAll('.color-swatch').forEach(sw => {
            sw.classList.remove('ring-2','ring-offset-2','border-white','scale-110',
                'ring-blue-400','ring-indigo-400','ring-purple-400','ring-emerald-400','ring-amber-400','ring-red-400');
        });
        const swatch = this.closest('label').querySelector('.color-swatch');
        const ring = ringMap[this.value] || 'ring-blue-400';
        swatch.classList.add('ring-2','ring-offset-2','border-white','scale-110', ring);

        // Update avatar preview
        const avatar = document.getElementById('previewAvatar');
        const [removeB, removeT] = avatar.className.match(/(bg-\S+-100)\s+(text-\S+-600)/)?.slice(1) || [];
        if (removeB) avatar.classList.remove(removeB);
        if (removeT) avatar.classList.remove(removeT);
        const [newBg, newText] = avatarColorMap[this.value] || ['bg-blue-100','text-blue-600'];
        avatar.classList.add(newBg, newText);
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
