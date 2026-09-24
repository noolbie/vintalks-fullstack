<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <!-- LINK WEB PAGE ICON -->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet"/>
        <!-- LINK GOOGLE FONT -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet"/>
        <!-- LINK CSS EKSTERNAL -->
        <link href="css/styles.css" rel="stylesheet"/>
        <link href="css/swiper-bundle.min.css" rel="stylesheet"/>
        <!-- TITLE LINK WEBSITE LANDING PAGE VINTALKS -->
        <title>VinTalks | Konsultasi Karier Global & Internasional</title>
        <!-- ICON VINTALKS -->
        <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon"/>
    </head>
    <body>
        <!-- SECTION NAVIGATION BAR -->
        <header class="site-header" id="navbar">
            <div class="navbar-container">
                <div class="nav-logo">
                    <a href="#home">
                        <img src="assets/logovintalksbackground.png" alt="nav-logo"/>
                    </a>
                </div>
                <div class="nav-menu">
                    <ul class="nav-links">
                        <li class="nav-link">
                            <a href="#home">Home</a>
                        </li>
                        <li class="nav-link-dropdown">
                            <a href="#">About Us<span class="dropdown-icon"><i class="ri-arrow-drop-down-line"></i></span></a>
                            <ul class="nav-dropdown">
                                <li>
                                    <a href="#get-to-know">Get to Know VinTalks</a>
                                </li>
                                <li><a href="#our-team">Our Team</a></li>
                                <li><a href="#why-choose">Why Choose VinTalks</a></li>
                                <li><a href="#how-works">How VinTalks Works</a></li>
                            </ul>
                        </li>
                        <li class="nav-link-dropdown">
                            <a href="#">Career Development<span class="dropdown-icon"><i class="ri-arrow-drop-down-line"></i></span></a>
                            <ul class="nav-dropdown">
                                <li>
                                    <a href="#mentors">Mentors</a>
                                </li>
                                <li>
                                    <a href="#programs">Programs</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-link">
                            <a href="#testimonial">Testimonial</a>
                        </li>
                        <li class="nav-link">
                            <a href="#faq">FAQ</a>
                        </li>
                        <li class="nav-link">
                            <a href="#contact-us">Contact Us</a>
                        </li>
                    </ul>
                </div>
                <div class="nav-btn">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a class="nav-whatsapp-btn" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        @elseif (auth()->user()->isMentor())
                            <a class="nav-whatsapp-btn" href="{{ route('mentor.dashboard') }}">Dashboard</a>
                        @else
                            <a class="nav-whatsapp-btn" href="{{ route('participant.dashboard') }}">Dashboard</a>
                        @endif
                    @else
                        <a class="nav-whatsapp-btn" href="{{ route('register') }}">Daftar Sekarang</a>
                    @endauth
                </div>
                <div class="nav-burger">
                    <span><i class="ri-menu-line"></i></span>
                </div>
            </div>
        </header>
        <!-- SECTION HOME -->
        <section class="home-section" id="home">
            <div class="home-container">
                <h1>Kickstart Your International Career Journey Today with VinTalks</h1>
                <p>
                    Dapatkan bimbingan 1-on-1 bersama mentor berpengalaman dari VINIX7 untuk
                    mempersiapkan karier global — mulai dari WFA, WFH, hingga remote job internasional.
                    Mulai sekarang dan wujudkan karier global impianmu!
                </p>
                <div class="home-btn">
                    <a class="book-btn" href="{{ route('register') }}">Book Consultation</a>
                    <a class="programs-btn" href="#programs">Our Programs</a>
                </div>
                <div class="home-hero">
                    <img src="assets/hero-home.png" alt="home-hero"/>
                </div>
            </div>
        </section>
        <!-- SECTION COMPANY ROLLING -->
        <section class="scroll-section">
            <div class="scroll-header">
                <h1>We're Celebrating Our Learners' Growth from VinTalks to Amazing Global Companies</h1>
            <div class="scroll-container">
                <div class="scroll-content" id="scroll-content">
                    <div class="company-item"><img src="assets/comp-glossier.png" alt="comp-glossier"></div>
                    <div class="company-item"><img src="assets/comp-apple.png" alt="comp-apple"></div>
                    <div class="company-item"><img src="assets/comp-adidas.png" alt="comp-adidas"></div>
                    <div class="company-item"><img src="assets/comp-amazon.png" alt="comp-amazon"></div>
                    <div class="company-item"><img src="assets/comp-fedex.png" alt="comp-fedex"></div>
                    <div class="company-item"><img src="assets/comp-ford.png" alt="comp-ford"></div>
                    <div class="company-item"><img src="assets/comp-laneige.png" alt="comp-laneige"></div>
                    <div class="company-item"><img src="assets/comp-nike.png" alt="comp-nike"></div>
                    <div class="company-item"><img src="assets/comp-toblerone.png" alt="comp-toblerone"></div>
                    <div class="company-item"><img src="assets/comp-glossier.png" alt="comp-glossier"></div>
                    <div class="company-item"><img src="assets/comp-apple.png" alt="comp-apple"></div>
                    <div class="company-item"><img src="assets/comp-adidas.png" alt="comp-adidas"></div>
                    <div class="company-item"><img src="assets/comp-amazon.png" alt="comp-amazon"></div>
                    <div class="company-item"><img src="assets/comp-fedex.png" alt="comp-fedex"></div>
                    <div class="company-item"><img src="assets/comp-ford.png" alt="comp-ford"></div>
                    <div class="company-item"><img src="assets/comp-laneige.png" alt="comp-laneige"></div>
                    <div class="company-item"><img src="assets/comp-nike.png" alt="comp-nike"></div>
                    <div class="company-item"><img src="assets/comp-toblerone.png" alt="comp-toblerone"></div>
                </div>
            </div>
        </section>
        <!-- SECTION GET TO KNOW VINTALKS -->
        <section class="what-section" id="get-to-know">
            <div class="container-what-section">
                <div class="container-kiri">
                    <h1>
                        <span class="highlight">Professional Mentoring</span><br>Towards an International Career
                    </h1>
                    <p>
                        VinTalks adalah platform mentoring yang disediakan oleh VINIX7 untuk membantu peserta memperoleh
                        bimbingan langsung dari mentor berpengalaman. Layanan ini berfokus pada persiapan karier luar negeri
                        dan pekerjaan remote yang membutuhkan pemahaman industri global.
                    </p>
                    <a class="btn-what" href="#why-choose">Discover Our Value</a>
                </div>
                <div class="container-kanan">
                    <div class="container-box">
                        <div class="tujuan-box">
                            <h2>Helping You Step Confidently Into Your Global Career Journey</h2>
                            <p>
                                Melalui VinTalks, peserta bisa mendapatkan arahan personal, feedback, dan strategi karier
                                yang lebih terarah. Pendekatan ini membantu peserta mempercepat proses belajar dan meningkatkan
                                peluang sukses dalam dunia kerja profesional.
                            </p>
                        </div>
                        <div class="keunggulan-box">
                            <h2>Where Your Potential Meets the Mentors You Deserve</h2>
                            <p>
                                Dengan akses mentor yang relevan di setiap bidang, VinTalks memastikan setiap peserta
                                mendapatkan pengalaman mentoring yang praktis, fleksibel, dan sesuai tujuan karier mereka.
                            </p>
                        </div>
                    </div>
                    <div class="container-gambar">
                        <div class="gambar-box">
                            <img src="assets/hero-gettoknow.png"/>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- SECTION OUR TEAM -->
        <section class="our-team-section" id="our-team">
            <div class="team-header">
                <h2>
                    The Team Who Brings VinTalks to Life
                </h2>
                <p>
                    Di balik setiap inovasi pada layanan VinTalks, terdapat tim
                    muda yang berdedikasi di bidang teknologi, desain, dan manajemen bisnis layanan.
                </p>
            </div>
            <div class="our-team-container">
                <div class="card-container">
                    <div class="img-container">
                        <img src="assets/team-aisya.png">
                    </div>
                    <div class="content">
                        <div class="card-details">
                            <h3>Aisyaaliy Prianto</h3>
                            <h4>Web Development & UI/UX<br>Kelompok 38 Proyek Akhir</h4>
                            <ul class="social-media-links-container">
                                <li><a href="https://www.instagram.com/aisyaaliyy_"><i class="ri-instagram-fill"></i></a></li>
                                <li><a href="http://www.linkedin.com/in/aisyaaliyprianto"><i class="ri-linkedin-box-fill"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-container">
                    <div class="img-container">
                        <img src="assets/team-hamsa.png">
                    </div>
                    <div class="content">
                        <div class="card-details">
                            <h3>Akhmad Fadillah Hamsa</h3>
                            <h4>Web Development & UI/UX<br>Kelompok 38 Proyek Akhir</h4>
                            <ul class="social-media-links-container">
                                <li><a href="https://www.instagram.com/fhakhmd"><i class="ri-instagram-fill"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-container">
                    <div class="img-container">
                        <img src="assets/team-salsa.png">
                    </div>
                    <div class="content">
                        <div class="card-details">
                            <h3>Salsa Nabila Putri</h3>
                            <h4>Web Development & UI/UX<br>Kelompok 38 Proyek Akhir</h4>
                            <ul class="social-media-links-container">
                                <li><a href="https://www.instagram.com/salsa.nabilla.05"><i class="ri-instagram-fill"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-container">
                    <div class="img-container">
                        <img src="assets/team-nicke.png">
                    </div>
                    <div class="content">
                        <div class="card-details">
                            <h3>Nicke Ramadhona</h3>
                            <h4>Web Development & UI/UX<br>Kelompok 38 Proyek Akhir</h4>
                            <ul class="social-media-links-container">
                                <li><a href="https://www.instagram.com/nikeeyra"><i class="ri-instagram-fill"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- SECTION WHY CHOOSE VINTALKS -->
        <section class="why" id="why-choose">
            <div class="container-why">
                <div class="container-atas">
                    <div class="h1-container-atas">
                        <h1>
                            Why Choose <br class="desktop-br"> Vintalks?
                        </h1>
                    </div>
                    <div class="p-container-atas">
                        <p>
                            VinTalks dirancang untuk membantu peserta VINIX7 berkembang dan mempersiapkan diri
                            memasuki dunia kerja global. Setiap mentor dipilih berdasarkan pengalaman nyata di
                            industri, sehingga peserta mendapatkan bimbingan yang relevan, praktis, dan dapat langsung
                            diterapkan.
                        </p>
                    </div>
                </div>
                <div class="container-bawah">
                    <div class="box-keunggulan1">
                        <div class="box-icon">
                            <img src="assets/icon-experiencementor.png">
                        </div>
                        <h2>
                            Experienced Mentors
                        </h2>
                        <p>
                            Mentor dipilih berdasarkan pengalaman nyata di industri, sehingga peserta mendapatkan arahan
                            yang relevan dengan standar kerja global.
                        </p>
                    </div>
                    <div class="box-keunggulan2">
                        <div class="box-icon">
                            <img src="assets/icon-learningpractice.png">
                        </div>
                        <h2>
                            Learning Through Real Practice
                        </h2>
                        <p>
                            Materi dirancang untuk langsung diterapkan, mulai dari pengembangan skill, portofolio, hingga
                            persiapan kerja nyata.
                        </p>
                    </div>
                    <div class="box-keunggulan3">
                        <div class="box-icon">
                            <img src="assets/icon-flexible.png">
                        </div>
                        <h2>
                            Flexible, Personalized for You
                        </h2>
                        <p>
                            Peserta bebas memilih mentor dan menjadwalkan sesi. Sistem 1-on-1 membuat mentoring lebih
                            efektif dan sesuai kebutuhan setiap peserta.
                        </p>
                    </div>
                    <div class="box-keunggulan4">
                        <div class="box-icon">
                            <img src="assets/icon-dedicated.png">
                        </div>
                        <h2>
                            Dedicated Career Support
                        </h2>
                        <p>
                            Mulai dari review CV, portofolio, hingga simulasi interview, mentor senantiasa mendampingi
                            sampai peserta siap melamar kerja internasional atau global.
                        </p>
                    </div>
                </div>
                <div class="btn-why-box">
                    <a class="btn-why" href="#mentors">Find Your Mentor</a>
                </div>
            </div>
        </section>
        <!-- SECTION HOW VINTALKS WORKS -->
        <section class="how" id="how-works">
            <div class="container-how">
                <div class="container-atas-how">
                    <h1>
                        How VinTalks Works
                    </h1>
                    <p>
                        Prosesnya mudah dan cepat, dari memilih mentor, memilih paket, hingga konsultasi.
                    </p>
                </div>
                <div class="container-bawah-how">
                    <div class="box-tutorial1">
                        <div class="nomor1">
                            01
                            <div class="kotak1"></div>
                        </div>
                        <div class="konten">
                            <h2>
                                Pilih Mentor & Lihat profil
                            </h2>
                            <p>
                                Mulailah dengan memilih mentor yang sesuai kebutuhanmu. Baca profil, pengalaman, dan fokus
                                keahliannya untuk memastikan mentor tersebut cocok dengan tujuan kariermu.
                            </p>
                        </div>
                    </div>
                    <div class="box-tutorial2">
                        <div class="nomor2">
                            02
                            <div class="kotak2"></div>
                        </div>
                        <div class="konten">
                            <h2>
                                Pilih Paket Mentoring
                            </h2>
                            <p>
                                Pilih paket layanan yang tersedia mulai dari basic hingga premium. Setiap paket memiliki
                                fasilitas berbeda sehingga kamu bisa menyesuaikan dengan budget dan kebutuhan pendampingan.
                            </p>
                        </div>
                    </div>
                    <div class="box-tutorial3">
                        <div class="nomor3">
                            03
                            <div class="kotak3"></div>
                        </div>
                        <div class="konten">
                            <h2>
                                Hubungi Admin
                            </h2>
                            <p>
                                Setelah menemukan mentor dan paket yang tepat, klik tombol book consultation untuk terhubung
                                dengan admin. Admin akan memandu proses pembayaran agar lebih mudah dan aman.
                            </p>
                        </div>
                    </div>
                    <div class="box-tutorial4">
                        <div class="nomor4">
                            04
                            <div class="kotak4"></div>
                        </div>
                        <div class="konten">
                            <h2>
                                Mulai Mentoring
                            </h2>
                            <p>
                                Setelah konfirmasi pembayaran, admin akan menghubungkanmu langsung dengan mentor pilihanmu.
                                Kamu bisa memulai sesi mentoring sesuai jadwal yang telah disepakati.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- SECTION MENTORS -->
        <section class="mentors-section" id="mentors">
            <h2 class="mentor-heading">Meet the Mentor</h2>
            <p class="mentor-subtitle">
                Kami menghadirkan mentor pilihan dengan pengalaman nyata, kredibel,
                dan siap membimbingmu sesuai kebutuhan.
            </p>
            <div class="mentor-slider-wrapper">
                <button class="mentor-btn prev">&#10094;</button>
                <div class="mentor-slider">
                    <div class="mentor-track">
                        @forelse ($mentors as $mentor)
                            <div class="mentor-card">
                                <img src="{{ $mentor->photo_url ?? asset('assets/logovintalksround.png') }}" alt="{{ $mentor->display_name }}">
                                <h3>{{ $mentor->display_name }}</h3>
                                <p>{{ $mentor->expertise }}</p>
                                <a href="{{ route('register') }}" class="linkedin-btn">Book Sesi<i class="ri-arrow-right-line"></i></a>
                            </div>
                        @empty
                            <p class="mentor-subtitle">Mentor akan segera hadir. Pantau terus ya!</p>
                        @endforelse
                    </div>
                </div>
                <button class="mentor-btn next">&#10095;</button>
            </div>
        </section>
        <!-- SECTION PROGRAMS -->
        <section class="programs-section" id="programs">
            <div class="programs-header">
                <h2>Find the Right Career Consultation Package for Your Global Journey</h2>
                <p>
                    Siapkan karier global impianmu dengan memilih layanan konsultasi yang paling sesuai -
                    mulai dari sesi 1-on-1 wawancara hingga pendampingan portofolio secara profesional.
                </p>
            </div>
            <div class="pricing-cards">
                @forelse ($packages as $package)
                    <div class="card {{ $package->is_popular ? 'popular' : '' }}">
                        @if ($package->is_popular)
                            <div class="label">⭐ Most Popular ⭐</div>
                        @endif
                        <img src="{{ asset($package->image ?: 'assets/programs-starter.png') }}" class="card-image" alt="{{ $package->name }}">
                        <h3>{{ $package->name }}</h3>
                        <p class="description">{{ $package->description }}</p>
                        <p class="old-price">{{ $package->old_price_formatted }}</p>
                        <p class="price">{{ $package->price_formatted }}</p>
                        <div class="benefits">
                            <h4>Yang Kamu Dapatkan:</h4>
                            <ul>
                                @foreach ($package->benefits ?? [] as $benefit)
                                    <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <a class="btn" href="{{ route('packages.select', $package) }}">Book Consultation</a>
                    </div>
                @empty
                    <p class="text-center text-slate-500">Paket belum tersedia.</p>
                @endforelse
            </div>
        </section>
        <!-- SECTION TESTIMONIAL -->
        <section class="testimonial-section" id="testimonial">
            <h2 class="testi-title">See How Others Succeeded with Vintalks. Now It’s Your Turn to Start Your Journey</h2>
            <p class="testi-subtitle">Intip pengalaman mereka sebelum kamu mulai perjalananmu sendiri.</p>
            <div class="testimonial-container">
                <button id="testi-left" class="testi-btn left-btn">‹</button>
                <div class="testimonial-wrapper">
                    <div id="slider" class="testimonial-slider">
                        <div class="testimonial-card">
                            <div class="top-row">
                                <div class="left-info">
                                    <img src="assets/testi-clara.png" alt="foto" class="profile">
                                    <h3 class="name">Clara Adinda</h3>
                                    <p class="role">Glossier, Inc. | AS</p>
                                </div>
                                <img src="assets/glossier-clara.png" alt="thumb" class="thumb">
                            </div>
                            <p class="text">
                                VinTalks benar-benar ngebantu aku yang awalnya bingung harus mulai dari mana untuk apply kerja ke luar negeri.
                                Di sesi mentoring, CV aku di revisi total jadi versi english yang ATS-Friendly banget.
                                Mentornya juga ngarahin cara bikin profile LinkedIn yang profesional.
                                Terus, aku juga latihan buat interview. Benar-benar worth it!
                            </p>
                        </div>
                        <div class="testimonial-card">
                            <div class="top-row">
                                <div class="left-info">
                                    <img src="assets/testi-bagas.png" alt="foto" class="profile">
                                    <h3 class="name">Bagas Wibowo</h3>
                                    <p class="role">Al Jazeera Media | Qatar</p>
                                </div>
                                <img src="assets/aljazeera-bagas.png" alt="thumb" class="thumb">
                            </div>
                            <p class="text">
                                VinTalks benar-benar ngebantu aku yang awalnya bingung harus mulai dari mana untuk apply kerja ke luar negeri.
                                Di sesi mentoring, CV aku di revisi total jadi versi english yang ATS-Friendly banget.
                                Mentornya juga ngarahin cara bikin profile LinkedIn yang profesional.
                                Terus, aku juga latihan buat interview. Benar-benar worth it!
                            </p>
                        </div>
                        <div class="testimonial-card">
                            <div class="top-row">
                                <div class="left-info">
                                    <img src="assets/testi-salma.png" alt="foto" class="profile">
                                    <h3 class="name">Salsa Nadila</h3>
                                    <p class="role">Dr. Dennis Skincare | AS</p>
                                </div>
                                <img src="assets/ford-bambang.png" alt="thumb" class="thumb">
                            </div>
                            <p class="text">
                                VinTalks benar-benar ngebantu aku yang awalnya bingung harus mulai dari mana untuk apply kerja ke luar negeri.
                                Di sesi mentoring, CV aku di revisi total jadi versi english yang ATS-Friendly banget.
                                Mentornya juga ngarahin cara bikin profile LinkedIn yang profesional.
                                Terus, aku juga latihan buat interview. Benar-benar worth it!
                            </p>
                        </div>
                        <div class="testimonial-card">
                            <div class="top-row">
                                <div class="left-info">
                                    <img src="assets/testi-bambang.png" alt="foto" class="profile">
                                    <h3 class="name">Bang Yudira</h3>
                                    <p class="role">Ford | Amerika Serikat</p>
                                </div>
                                <img src="assets/ford-bambang.png" alt="thumb" class="thumb">
                            </div>
                            <p class="text">
                                VinTalks benar-benar ngebantu aku yang awalnya bingung harus mulai dari mana untuk apply kerja ke luar negeri.
                                Di sesi mentoring, CV aku di revisi total jadi versi english yang ATS-Friendly banget.
                                Mentornya juga ngarahin cara bikin profile LinkedIn yang profesional.
                                Terus, aku juga latihan buat interview. Benar-benar worth it!
                            </p>
                        </div>
                        <div class="testimonial-card">
                            <div class="top-row">
                                <div class="left-info">
                                    <img src="assets/testi-liliana.png" alt="foto" class="profile">
                                    <h3 class="name">Liliana Wulan</h3>
                                    <p class="role">Amorepacific | Korea</p>
                                </div>
                                <img src="assets/amore-liliana.png" alt="thumb" class="thumb">
                            </div>
                            <p class="text">
                            VinTalks benar-benar ngebantu aku yang awalnya bingung harus mulai dari mana untuk apply kerja ke luar negeri.
                            Di sesi mentoring, CV aku di revisi total jadi versi english yang ATS-Friendly banget.
                            Mentornya juga ngarahin cara bikin profile LinkedIn yang profesional.
                            Terus, aku juga latihan buat interview. Benar-benar worth it!
                            </p>
                        </div>
                    <div class="testimonial-card">
                            <div class="top-row">
                                <div class="left-info">
                                    <img src="assets/testi-putri.png" alt="foto" class="profile">
                                    <h3 class="name">Putri Wardani</h3>
                                    <p class="role">Dr. Dennis Skincare | AS</p>
                                </div>
                                <img src="assets/dennis-salma.png" alt="thumb" class="thumb">
                            </div>
                            <p class="text">
                                VinTalks benar-benar ngebantu aku yang awalnya bingung harus mulai dari mana untuk apply kerja ke luar negeri.
                                Di sesi mentoring, CV aku di revisi total jadi versi english yang ATS-Friendly banget.
                                Mentornya juga ngarahin cara bikin profile LinkedIn yang profesional.
                                Terus, aku juga latihan buat interview. Benar-benar worth it!
                            </p>
                        </div>
                    </div>
                </div>
                <button id="testi-right" class="testi-btn right-btn">›</button>
            </div>
        </section>
        <!-- SECTION FAQ -->
        <section class="faq-section" id="faq">
            <h2>Still Have Questions? Find the Answers Here</h2>
            <p class="faq-subtitle">
                Temukan rangkuman pertanyaan dan jawaban yang telah kami sediakan sebagai panduan cepat seputar layanan Vintalks.
            </p>
            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-header">
                        <span>Layanan apa saja yang tersedia di Vintalks?</span><i class="arrow"></i>
                    </button>
                    <div class="faq-content">
                        <p>Vintalks menyediakan berbagai program mentoring seperti:</p>
                        <ul>
                            <li>Review & optimasi CV/Portfolio/LinkedIn</li>
                            <li>Persiapan interview kerja</li>
                            <li>Konsultasi karier</li>
                            <li>Persiapan beasiswa dan kuliah</li>
                            <li>Mentoring kerja internasional</li>
                        </ul>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-header">
                        <span>Apakah Vintalks cocok untuk pemula atau fresh graduate?</span><i class="arrow"></i>
                    </button>
                    <div class="faq-content">
                        <p>
                            Sangat cocok! Banyak peserta Vintalks berasal dari pemula dan fresh graduate
                            yang butuh arahan mulai dari CV, interview, hingga strategi awal membangun karier.
                        </p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-header">
                        <span>Bagaimana cara memilih mentor?</span><i class="arrow"></i>
                    </button>
                    <div class="faq-content">
                        <p>
                            Kamu tinggal melihat profil mentor, keahlian, pengalaman, dan review peserta sebelumnya.
                            Setelah itu pilih mentor yang paling cocok dengan kebutuhanmu, lalu langsung booking sesi.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- SECTION CONTACT US -->
        <section class="career-banner" id="contact-us">
            <div class="career-banner-inner">
                <div class="career-text">
                    <h2>Start Your Global<br>Career Journey Today!</h2>
                    <p>
                        Punya pertanyaan atau butuh panduan? Tim Vintalks siap membantu kamu memulai perjalanan karier globalmu.
                    </p>
                    <a href="{{ route('register') }}" class="career-btn">Book Consultation Now</a>
                </div>
                <div class="career-image">
                    <img src="assets/hero-contactus.png" alt="Career Image">
                </div>
            </div>
        </section>
        <!-- SECTION FOOTER -->
        <footer>
            <div class="footer-container">
                <div class="footer-column">
                    <div class="footer-logo">
                        <a href="#home">
                            <img src="assets/logovintalksbackground.png" alt="footer-logo"/>
                        </a>
                    </div>
                    <p class="section-description">
                        VinTalks membantu talenta Indonesia dalam menghadapi peluang kerja
                        Internasional dengan menyediakan layanan konsultasi karier.
                    </p>
                    <ul class="footer-socials">
                        <li><a href="https://www.instagram.com/vinix7idn"><i class="ri-instagram-line"></i></a></li>
                        <li><a href="https://x.com/Vinix7idn"><i class="ri-twitter-x-line"></i></a></li>
                        <li><a href="https://web.facebook.com/"><i class="ri-facebook-fill"></i></a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Main Navigation</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#get-to-know">About Us</a></li>
                        <li><a href="#testimonial">Testimonial</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Career Development</h3>
                    <ul class="footer-links">
                        <li><a href="#mentors">Mentors</a></li>
                        <li><a href="#programs">Programs</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Help & Support</h3>
                    <ul class="footer-links">
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#contact-us">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bar">
                Copyright © 2026 Logika Tech Repair | Cyber Security Analyst. All Rights Reserved.
            </div>
        </footer>
        <!-- LINK JAVASCRIPT EKSTERNAL -->
        <script src="js/swiper-bundle.min.js"></script>
        <script src="js/script.js"></script>
    </body>
</html>
