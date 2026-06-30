-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 30, 2026 at 12:48 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lokerone`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int NOT NULL,
  `job_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `cover_letter` text NOT NULL,
  `STATUS` enum('pending','review','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `user_id`, `full_name`, `email`, `phone`, `cover_letter`, `STATUS`, `created_at`) VALUES
(1, 14, 2, 'zayyan', 'admin@gmail.com', '12345678', 'tes', 'pending', '2026-06-29 19:36:31'),
(2, 17, 2, 'zayyan', 'admin@gmail.com', '12345678', 'hai aku coba daftar lo', 'review', '2026-06-29 19:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `job_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `logo_initial` char(2) NOT NULL,
  `color` varchar(20) DEFAULT 'blue',
  `description` text,
  `website` varchar(200) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `NAME`, `logo_initial`, `color`, `description`, `website`, `location`, `created_at`, `user_id`, `logo_path`) VALUES
(1, 'TechCorp Indonesia', 'TC', 'blue', 'Perusahaan teknologi terkemuka di Indonesia yang berfokus pada pengembangan solusi digital inovatif.', 'https://techcorp.id', 'Jakarta', '2026-06-29 10:45:42', NULL, NULL),
(2, 'Finance.id', 'FI', 'purple', 'Platform keuangan digital terpercaya yang melayani jutaan pengguna di seluruh Indonesia.', 'https://finance.id', 'Bandung', '2026-06-29 10:45:42', NULL, NULL),
(3, 'EduStart Inc', 'ES', 'orange', 'Startup edukasi berbasis teknologi yang merevolusi cara belajar di Indonesia.', 'https://edustart.id', 'Yogyakarta', '2026-06-29 10:45:42', NULL, NULL),
(4, 'DataTech Solutions', 'DT', 'emerald', 'Perusahaan analitik data yang membantu bisnis membuat keputusan berdasarkan data.', 'https://datatech.id', 'Jakarta', '2026-06-29 10:45:42', NULL, NULL),
(5, 'MarketHub', 'MH', 'red', 'Agensi digital marketing dengan pengalaman lebih dari 10 tahun melayani brand-brand besar.', 'https://markethub.id', 'Surabaya', '2026-06-29 10:45:42', NULL, NULL),
(6, 'GlobalNet Corp', 'GN', 'indigo', 'Perusahaan teknologi global dengan kantor di lebih dari 20 negara.', 'https://globalnet.com', 'Jakarta', '2026-06-29 10:45:42', NULL, NULL),
(7, 'HealthPlus', 'HP', 'teal', 'Platform kesehatan digital yang menghubungkan pasien dengan tenaga medis profesional.', 'https://healthplus.id', 'Bali', '2026-06-29 10:45:42', NULL, NULL),
(8, 'CreativeStudio', 'CS', 'pink', 'Studio kreatif yang mengerjakan proyek desain dan branding untuk klien lokal dan internasional.', 'https://creativestudio.id', 'Bandung', '2026-06-29 10:45:42', NULL, NULL),
(9, 'PT. Zayyan Makmur', 'ZM', 'indigo', 'PT. Zayyan Makmur adalah perusahaan teknologi dan jasa profesional yang berpusat di Surabaya, Jawa Timur. Didirikan dengan visi membangun ekosistem bisnis digital yang inklusif dan inovatif, kami melayani berbagai klien dari sektor korporasi hingga UMKM. Dengan tim yang berpengalaman dan berdedikasi, kami berkomitmen untuk menghadirkan solusi terbaik yang mendorong pertumbuhan bisnis Anda secara berkelanjutan.', 'https://zayyanmakmur.id', 'Surabaya, Jawa Timur', '2026-06-29 19:48:21', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `requirements` text NOT NULL,
  `category` enum('IT & Software','Data & Analytics','Human Resources','Sales & Marketing','Finance','Design','Engineering','Education','Healthcare','Other') NOT NULL DEFAULT 'Other',
  `TYPE` enum('Full-time','Part-time','Contract','Freelance','Internship') NOT NULL DEFAULT 'Full-time',
  `location_key` varchar(50) NOT NULL,
  `location_label` varchar(100) NOT NULL,
  `is_remote` tinyint(1) DEFAULT '0',
  `salary_min` int DEFAULT NULL,
  `salary_max` int DEFAULT NULL,
  `salary_visible` tinyint(1) DEFAULT '1',
  `STATUS` enum('active','closed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `company_id`, `title`, `description`, `requirements`, `category`, `TYPE`, `location_key`, `location_label`, `is_remote`, `salary_min`, `salary_max`, `salary_visible`, `STATUS`, `created_at`, `updated_at`) VALUES
(1, 1, 'Senior UI/UX Designer', 'Kami mencari Senior UI/UX Designer yang berpengalaman untuk bergabung dengan tim produk kami. Kamu akan bertanggung jawab untuk merancang pengalaman pengguna yang intuitif dan menarik untuk produk-produk digital kami yang digunakan oleh jutaan pengguna.', '- Minimal 4 tahun pengalaman sebagai UI/UX Designer\n- Mahir menggunakan Figma, Adobe XD, atau Sketch\n- Portfolio yang kuat menunjukkan kemampuan desain\n- Pemahaman mendalam tentang prinsip UX dan user research\n- Kemampuan komunikasi yang baik dalam Bahasa Indonesia dan Inggris', 'Design', 'Full-time', 'jakarta', 'Jakarta Selatan', 1, 12000000, 20000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(2, 2, 'Financial Analyst', 'Finance.id membuka kesempatan bagi Anda yang memiliki passion di bidang keuangan untuk bergabung sebagai Financial Analyst. Anda akan menganalisis data keuangan dan memberikan rekomendasi strategis kepada manajemen.', '- Sarjana Akuntansi, Manajemen Keuangan, atau bidang terkait\n- Minimal 2 tahun pengalaman sebagai Financial Analyst\n- Kemampuan analisis data yang kuat\n- Menguasai Excel dan alat analisis keuangan lainnya\n- Sertifikasi CFA atau CPA merupakan nilai tambah', 'Finance', 'Full-time', 'bandung', 'Bandung', 0, 8000000, 15000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(3, 3, 'Frontend React Developer', 'EduStart mencari Frontend Developer yang berpengalaman dengan React.js untuk membangun antarmuka pembelajaran yang interaktif dan responsif. Kamu akan bekerja dengan tim yang dinamis dan passionate tentang pendidikan.', '- Minimal 3 tahun pengalaman dengan React.js\n- Familiar dengan TypeScript, Redux, dan React Query\n- Pemahaman mendalam tentang HTML5, CSS3, dan JavaScript ES6+\n- Pengalaman dengan testing (Jest, React Testing Library)\n- Kemampuan bekerja secara mandiri dalam lingkungan remote', 'IT & Software', 'Contract', 'remote', 'Fully Remote', 1, NULL, NULL, 0, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(4, 4, 'Data Scientist', 'DataTech Solutions mencari Data Scientist berbakat untuk bergabung dalam tim riset kami. Kamu akan membangun model machine learning dan analitik prediktif untuk membantu klien kami mengambil keputusan bisnis yang lebih baik.', '- Gelar S1/S2 di bidang Statistika, Matematika, Ilmu Komputer, atau bidang terkait\n- Pengalaman minimal 3 tahun sebagai Data Scientist\n- Mahir Python, R, dan SQL\n- Pengalaman dengan framework ML: TensorFlow, PyTorch, scikit-learn\n- Kemampuan mengkomunikasikan hasil analisis kepada non-technical stakeholders', 'Data & Analytics', 'Full-time', 'jakarta', 'Jakarta Pusat', 0, 15000000, 25000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(5, 5, 'Digital Marketing Specialist', 'MarketHub mencari Digital Marketing Specialist yang kreatif dan data-driven untuk mengelola kampanye digital klien-klien premium kami. Kamu akan merencanakan, melaksanakan, dan menganalisis strategi pemasaran digital.', '- Minimal 2 tahun pengalaman di Digital Marketing\n- Kemampuan mengelola Google Ads, Meta Ads, dan platform iklan lainnya\n- Pemahaman SEO/SEM yang kuat\n- Familiar dengan Google Analytics 4 dan tools analitik lainnya\n- Kreatif dan mampu membuat konten yang menarik', 'Sales & Marketing', 'Full-time', 'surabaya', 'Surabaya', 0, 6000000, 10000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(6, 6, 'Backend Node.js Developer', 'GlobalNet Corp membuka posisi Backend Developer untuk memperkuat tim engineering global kami. Kamu akan membangun dan memelihara API yang scalable dan performant yang digunakan oleh jutaan pengguna di seluruh dunia.', '- Minimal 4 tahun pengalaman Backend Development\n- Mahir Node.js, Express.js, dan NestJS\n- Pengalaman dengan database PostgreSQL dan MongoDB\n- Pemahaman tentang microservices, Docker, dan Kubernetes\n- Pengalaman dengan AWS atau GCP', 'IT & Software', 'Full-time', 'remote', 'Fully Remote', 1, 10000000, 18000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(7, 7, 'Dokter Umum / General Practitioner', 'HealthPlus membuka kesempatan bagi Dokter Umum untuk bergabung dalam platform telemedicine kami yang berkembang pesat. Kamu akan memberikan layanan konsultasi kesehatan kepada pasien secara online maupun offline.', '- Memiliki gelar dokter (dr.) yang sah\n- Memiliki STR dan SIP yang masih berlaku\n- Pengalaman minimal 1 tahun sebagai dokter umum\n- Kemampuan komunikasi yang baik\n- Tertarik dengan teknologi kesehatan digital', 'Healthcare', 'Full-time', 'bali', 'Bali', 1, 10000000, 20000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(8, 8, 'Graphic Designer (Illustrator)', 'CreativeStudio mencari Graphic Designer berbakat yang memiliki keahlian ilustrasi untuk mengerjakan proyek-proyek kreatif berskala nasional dan internasional. Kamu akan bekerja langsung dengan Art Director dan klien.', '- Portfolio ilustrasi yang kuat (wajib disertakan)\n- Mahir Adobe Illustrator, Photoshop, dan After Effects\n- Minimal 2 tahun pengalaman sebagai Graphic Designer\n- Pemahaman tentang branding dan tipografi\n- Kemampuan bekerja dalam deadline yang ketat', 'Design', 'Full-time', 'bandung', 'Bandung', 0, 7000000, 12000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(9, 1, 'DevOps Engineer', 'TechCorp Indonesia membutuhkan DevOps Engineer untuk membangun dan mengelola infrastruktur cloud kami yang kompleks. Kamu akan bertanggung jawab atas reliabilitas, skalabilitas, dan keamanan sistem kami.', '- Minimal 3 tahun pengalaman sebagai DevOps/SRE\n- Mahir Kubernetes, Docker, dan Terraform\n- Pengalaman mendalam dengan AWS atau GCP\n- Pemahaman tentang CI/CD pipeline (Jenkins, GitLab CI)\n- Kemampuan scripting Bash dan Python', 'IT & Software', 'Full-time', 'jakarta', 'Jakarta Utara', 0, 15000000, 28000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(10, 4, 'Business Intelligence Analyst', 'DataTech Solutions mencari BI Analyst untuk membantu klien kami mengubah data mentah menjadi wawasan bisnis yang actionable. Kamu akan membangun dashboard dan laporan yang mempengaruhi keputusan strategis perusahaan.', '- Minimal 2 tahun pengalaman sebagai BI Analyst\n- Mahir SQL, Power BI, atau Tableau\n- Pemahaman tentang data warehousing dan ETL\n- Kemampuan analisis bisnis yang kuat\n- Pengalaman dengan Python untuk analisis data adalah nilai tambah', 'Data & Analytics', 'Full-time', 'jakarta', 'Jakarta Selatan', 1, 9000000, 15000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(11, 2, 'HR Business Partner', 'Finance.id mencari HR Business Partner yang berpengalaman untuk mendukung pertumbuhan tim kami yang pesat. Kamu akan menjadi mitra strategis bagi para pemimpin bisnis dalam hal manajemen sumber daya manusia.', '- Gelar S1 Psikologi, Manajemen SDM, atau bidang terkait\n- Minimal 4 tahun pengalaman di bidang HR, khususnya sebagai HRBP\n- Pemahaman mendalam tentang employment law Indonesia\n- Kemampuan interpersonal dan komunikasi yang sangat baik\n- Pengalaman dengan HRIS system', 'Human Resources', 'Full-time', 'bandung', 'Bandung', 0, 10000000, 17000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(12, 5, 'Sales Account Executive', 'MarketHub membuka posisi Sales Account Executive untuk memperluas jangkauan klien kami di seluruh Indonesia. Kamu akan membangun hubungan dengan klien potensial dan mendorong pertumbuhan revenue perusahaan.', '- Minimal 2 tahun pengalaman di bidang Sales B2B\n- Track record yang terbukti dalam mencapai target penjualan\n- Kemampuan negosiasi dan presentasi yang kuat\n- Jaringan bisnis yang luas adalah nilai tambah\n- Bersedia melakukan perjalanan dinas', 'Sales & Marketing', 'Full-time', 'surabaya', 'Surabaya', 0, 7000000, 15000000, 1, 'active', '2026-06-29 10:45:42', '2026-06-29 10:45:42'),
(13, 1, 'Junior Data Analyst', 'Menganalisis data.', 'Bisa SQL dan Excel.', 'Data & Analytics', 'Full-time', 'jakarta', 'Jakarta Selatan', 0, 7000000, 10000000, 1, 'active', '2026-06-29 12:08:41', '2026-06-29 12:08:41'),
(14, 2, 'HR Manager', 'Mengurus rekrutmen dan payroll.', 'Pengalaman 5 tahun di bidang HR.', 'Human Resources', 'Full-time', 'bandung', 'Bandung', 0, 12000000, 20000000, 1, 'active', '2026-06-29 12:08:41', '2026-06-29 12:08:41'),
(15, 3, 'Digital Marketing Executive', 'Mengelola campaign ads.', 'Paham FB Ads & Google Ads.', 'Sales & Marketing', 'Contract', 'remote', 'Remote', 1, 8000000, 12000000, 1, 'active', '2026-06-29 12:08:41', '2026-06-29 12:08:41'),
(16, 4, 'IT Support', 'Menjaga jaringan dan hardware.', 'Paham jaringan dan troubleshooting.', 'IT & Software', 'Full-time', 'surabaya', 'Surabaya', 0, 5000000, 8000000, 1, 'active', '2026-06-29 12:08:41', '2026-06-29 12:08:41'),
(17, 9, 'IT ZayyanSukses', '1. Mengembangkan dan memelihara aplikasi sesuai kebutuhan perusahaan.\r\n2. Melakukan perbaikan (bug fixing) dan pengujian sistem.\r\n3. Berkolaborasi dengan tim untuk mengembangkan fitur baru.\r\n4. Membuat dokumentasi sederhana terkait pengembangan aplikasi.\r\n5. Memberikan dukungan teknis jika diperlukan.', 'Pendidikan minimal SMK/D3/S1 jurusan Teknik Informatika, Sistem Informasi, atau bidang terkait.\r\nMemahami dasar-dasar pemrograman.\r\nMenguasai minimal satu bahasa pemrograman (misalnya PHP, JavaScript, Java, atau Python).\r\nMemahami penggunaan database seperti MySQL atau PostgreSQL.\r\nMampu bekerja secara individu maupun dalam tim.\r\nMemiliki kemampuan komunikasi yang baik.\r\nBersedia belajar dan mengikuti perkembangan teknologi.', 'IT & Software', 'Full-time', 'surabaya', 'surabaya', 1, 100000, 100000, 1, 'active', '2026-06-29 19:50:53', '2026-06-29 19:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `role` enum('admin','user','company') DEFAULT 'user',
  `phone` varchar(20) DEFAULT NULL,
  `headline` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `company_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `NAME`, `email`, `PASSWORD`, `role`, `phone`, `headline`, `created_at`, `company_id`) VALUES
(1, 'Admin LOKERONE', 'admin@lokerone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, '2026-06-29 10:45:42', NULL),
(2, 'zayyan', 'admin@gmail.com', '$2y$10$J72lHecuPiTj7DAfl9pkHexL.EyBT2Dv/s4dExpIGaGFd1P4YDJLi', 'user', '', '', '2026-06-29 12:01:13', NULL),
(3, 'Josep', 'adminperusahaan@gmail.com', '$2y$10$EpwKJngQCyLQYSSERnd76.Fxiaa1eR3mZUmgV6O.Gmpofj533IH.G', 'company', NULL, NULL, '2026-06-29 19:48:21', 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bookmark` (`user_id`,`job_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
