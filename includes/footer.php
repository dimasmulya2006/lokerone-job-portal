    <!-- Footer -->
    <footer class="bg-slate-900 pt-16 pb-8 border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center mr-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="font-bold text-2xl tracking-tight text-white">LOKER<span class="text-brand-600">ONE</span></span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed font-medium">
                        Platform pencarian kerja modern. Misi kami adalah menjembatani talenta terbaik Indonesia dengan peluang karier impian mereka secara mudah, cepat, dan transparan.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-white font-bold mb-5">Pencari Kerja</h3>
                    <ul class="space-y-3 text-slate-400 text-sm font-medium">
                        <li><a href="#" class="hover:text-white transition-colors">Cari Lowongan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Jelajahi Perusahaan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Panduan Karier</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Tips Wawancara</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-white font-bold mb-5">Perusahaan</h3>
                    <ul class="space-y-3 text-slate-400 text-sm font-medium">
                        <li><a href="#" class="hover:text-white transition-colors">Pasang Lowongan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Cari Kandidat</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Produk Premium</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pusat Bantuan HR</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold mb-5">Tentang LOKERONE</h3>
                    <ul class="space-y-3 text-slate-400 text-sm font-medium">
                        <li><a href="#" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-slate-500 text-sm mb-4 md:mb-0 font-medium">
                    &copy; 2026 LOKERONE. Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Navbar Scroll Effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('shadow-md');
                navbar.classList.replace('py-0', 'py-1');
            } else {
                navbar.classList.remove('shadow-md');
                navbar.classList.replace('py-1', 'py-0');
            }
        });
    </script>
</body>
</html>
