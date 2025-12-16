<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBERIKAN - Sistem Informasi Distribusi Perikanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --danger: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem 0;
        }
        
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .navbar-brand i {
            font-size: 2rem;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            color: white;
            padding: 8rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transform: skewX(-20deg);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        .btn-hero {
            padding: 0.75rem 2.5rem;
            font-size: 1.1rem;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .btn-hero-primary {
            background: white;
            color: #0d6efd;
        }
        
        .btn-hero-primary:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        .btn-hero-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-hero-secondary:hover {
            background: white;
            color: #0d6efd;
            transform: translateY(-2px);
        }
        
        /* Features Section */
        .features {
            padding: 5rem 0;
            background: #f8f9fa;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .section-subtitle {
            text-align: center;
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
        }
        
        .feature-card h4 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .feature-card p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        /* User Roles Section */
        .roles-section {
            padding: 5rem 0;
        }
        
        .role-box {
            background: white;
            border-left: 5px solid #0d6efd;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .role-box:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            transform: translateX(5px);
        }
        
        .role-box h5 {
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .role-box .role-icon {
            font-size: 2rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        /* Footer */
        footer {
            background: #212529;
            color: white;
            padding: 3rem 0 1rem;
        }
        
        footer h5 {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        footer p, footer a {
            font-size: 0.95rem;
        }
        
        footer a {
            color: #0d6efd;
            text-decoration: none;
        }
        
        footer a:hover {
            text-decoration: underline;
        }
        
        .footer-divider {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 2rem;
            padding-top: 2rem;
        }
        
        .footer-bottom {
            text-align: center;
            color: rgba(255,255,255,0.7);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .btn-hero {
                padding: 0.6rem 2rem;
                font-size: 0.95rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-fish"></i>
                SIBERIKAN
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#peran">Peran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn btn-light text-primary px-3" href="{{ route('login') }}">Masuk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1>Sistem Distribusi Perikanan Terpadu</h1>
                    <p>Kelola seluruh rantai pasokan ikan dari nelayan hingga pembeli dengan sistem manajemen yang komprehensif dan terintegrasi.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-hero btn-hero-primary">
                            <i class="bi bi-arrow-right"></i> Masuk
                        </a>
                        <a href="#fitur" class="btn btn-hero btn-hero-secondary">
                            <i class="bi bi-info-circle"></i> Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center d-none d-lg-block">
                    <i class="bi bi-fish" style="font-size: 15rem; color: rgba(255,255,255,0.15);"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="fitur">
        <div class="container">
            <h2 class="section-title">Fitur Utama</h2>
            <p class="section-subtitle">Kelola distribusi perikanan dengan mudah dan efisien</p>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h4>Manajemen Tangkapan</h4>
                        <p>Catat hasil tangkapan ikan secara real-time dengan informasi detail jenis, jumlah, dan kualitas.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-bag-check"></i>
                        </div>
                        <h4>Sistem Order</h4>
                        <p>Pembeli dapat memesan ikan langsung dari nelayan atau tengkulak dengan mudah dan transparan.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h4>Tracking Pengiriman</h4>
                        <p>Pantau status pengiriman secara real-time dengan dokumentasi lengkap setiap tahap perjalanan.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h4>Bukti Serah Terima</h4>
                        <p>Otomatis generate dokumen bukti serah terima untuk setiap transaksi pengiriman.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- User Roles Section -->
    <section class="roles-section" id="peran">
        <div class="container">
            <h2 class="section-title">Peran dalam Sistem</h2>
            <p class="section-subtitle">Setiap peran memiliki fungsi khusus dalam ekosistem distribusi</p>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="role-box">
                        <div class="role-icon">
                            <i class="bi bi-person-arms-up"></i>
                        </div>
                        <h5>Nelayan</h5>
                        <p>Mencatat hasil tangkapan harian, melacak penjualan, dan mendapatkan pembayaran dari tengkulak atau pembeli langsung.</p>
                    </div>
                    
                    <div class="role-box">
                        <div class="role-icon">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                        <h5>Tengkulak</h5>
                        <p>Mengelola inventori, membeli dari nelayan, melayani pesanan pembeli, dan mengatur distribusi ikan.</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="role-box">
                        <div class="role-icon">
                            <i class="bi bi-bicycle"></i>
                        </div>
                        <h5>Sopir/Kurir</h5>
                        <p>Menerima pesanan pengiriman otomatis, melacak rute, dan mendokumentasikan pengiriman dengan bukti serah terima digital.</p>
                    </div>
                    
                    <div class="role-box">
                        <div class="role-icon">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <h5>Pembeli</h5>
                        <p>Menjelajahi katalog ikan, membuat pesanan, melacak pengiriman, dan memberikan konfirmasi penerimaan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="kontak">
        <div class="container">
            <h2>Siap Menggunakan SIBERIKAN?</h2>
            <p>Bergabunglah dengan sistem distribusi perikanan modern yang mengubah cara bisnis ikan dijalankan.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-hero btn-hero-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                </a>
                <a href="mailto:support@siberikan.local" class="btn btn-hero btn-hero-secondary">
                    <i class="bi bi-envelope"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5><i class="bi bi-fish"></i> SIBERIKAN</h5>
                    <p>Sistem Informasi Distribusi Perikanan yang modern dan terintegrasi untuk kemudahan bisnis Anda.</p>
                </div>
                
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="/">Beranda</a></li>
                        <li><a href="#fitur">Fitur</a></li>
                        <li><a href="#peran">Peran</a></li>
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope"></i> <a href="mailto:info@siberikan.local">info@siberikan.local</a></li>
                        <li><i class="bi bi-telephone"></i> <a href="tel:+62123456789">+62 (123) 456-789</a></li>
                        <li><i class="bi bi-geo-alt"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-divider">
                <div class="footer-bottom">
                    <p>&copy; 2025 SIBERIKAN. Sistem Informasi Distribusi Perikanan. Semua hak dilindungi.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
