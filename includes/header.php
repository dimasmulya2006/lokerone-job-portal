<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOKERONE - Temukan Karier Impianmu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc; /* slate-50 */
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .job-card {
            transition: all 0.3s ease;
        }
        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-color: #dbeafe;
        }
        
        /* Micro animations */
        .btn-animate {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-animate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2), 0 4px 6px -4px rgba(37, 99, 235, 0.1);
        }
        .btn-animate:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: 0 5px 10px -3px rgba(37, 99, 235, 0.2);
        }
        
        /* Floating Labels Input */
        .form-input-container {
            position: relative;
        }
        .form-input {
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="text-slate-800 antialiased overflow-x-hidden">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center cursor-pointer transition-transform hover:scale-105 active:scale-95" onclick="window.location.href='index.php'">
                    <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center mr-2 shadow-lg shadow-brand-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-slate-900">LOKER<span class="text-brand-600">ONE</span></span>
                </div>
                <?php
                $isEmployerPage = (strpos($_SERVER['PHP_SELF'], '/employer/') !== false);
                $base = $isEmployerPage ? '../' : '';
                $isCompany = (($_SESSION['user_role'] ?? 'user') === 'company');
                ?>
                <div class="hidden md:flex space-x-8 items-center">
                    <?php if ($isCompany): ?>
                        <a href="<?= $base ?>employer/index.php" class="text-slate-600 hover:text-brand-600 font-semibold transition-colors">Dashboard</a>
                        <a href="<?= $base ?>employer/create-job.php" class="text-slate-600 hover:text-brand-600 font-semibold transition-colors">Buat Lowongan</a>
                        <a href="<?= $base ?>employer/company-profile.php" class="text-slate-600 hover:text-brand-600 font-semibold transition-colors">Profil Perusahaan</a>
                    <?php else: ?>
                        <a href="<?= $base ?>index.php" class="text-slate-600 hover:text-brand-600 font-semibold transition-colors">Cari Lowongan</a>
                        <a href="<?= $base ?>companies.php" class="text-slate-600 hover:text-brand-600 font-semibold transition-colors">Perusahaan</a>
                        <a href="<?= $base ?>salary.php" class="text-slate-600 hover:text-brand-600 font-semibold transition-colors">Gaji &amp; Karier</a>
                    <?php endif; ?>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-3 relative group cursor-pointer">
                            <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold border border-brand-200">
                                <?= substr(htmlspecialchars($_SESSION['user_name']), 0, 1) ?>
                            </div>
                            <span class="font-semibold text-slate-700 hidden lg:block"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                            
                            <!-- Dropdown -->
                            <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right group-hover:translate-y-0 translate-y-2 z-50">
                                <div class="p-2 space-y-1">
                                    <?php if ($isCompany): ?>
                                        <a href="<?= $base ?>employer/index.php" class="block px-4 py-2 text-sm text-slate-600 font-medium hover:bg-slate-50 hover:text-brand-600 rounded-lg transition-colors">Dashboard</a>
                                        <a href="<?= $base ?>employer/create-job.php" class="block px-4 py-2 text-sm text-slate-600 font-medium hover:bg-slate-50 hover:text-brand-600 rounded-lg transition-colors">Buat Lowongan</a>
                                    <?php else: ?>
                                        <a href="<?= $base ?>profile.php" class="block px-4 py-2 text-sm text-slate-600 font-medium hover:bg-slate-50 hover:text-brand-600 rounded-lg transition-colors">Profil Saya</a>
                                        <a href="<?= $base ?>applications.php" class="block px-4 py-2 text-sm text-slate-600 font-medium hover:bg-slate-50 hover:text-brand-600 rounded-lg transition-colors">Lamaran Saya</a>
                                    <?php endif; ?>
                                    <div class="h-px bg-slate-100 my-1"></div>
                                    <a href="<?= $base ?>logout.php" class="block px-4 py-2 text-sm text-red-600 font-medium hover:bg-red-50 rounded-lg transition-colors">Keluar</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= $base ?>login.php" class="text-slate-600 hover:text-brand-600 font-semibold transition-colors">Masuk</a>
                        <a href="<?= $base ?>register.php" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl font-semibold btn-animate">Daftar Sekarang</a>
                    <?php endif; ?>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="text-slate-600 hover:text-slate-900 focus:outline-none transition-transform active:scale-95">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
