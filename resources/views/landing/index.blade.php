<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PATANI - Platform Pertanian Digital Indonesia</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #050f05; overflow-x: hidden; }

        /* ═══════════════════════════════════════════════
           LOADING SCREEN
        ═══════════════════════════════════════════════ */

        #patani-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            overflow: hidden;
            background: #030e05 url("{{ asset('images/agri-land-wallpapers.jpg') }}") center center / cover no-repeat;
            transition: opacity 0.9s ease, visibility 0.9s ease;
        }
        #patani-loader.hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Overlay gelap agar konten terbaca */
        #ldr-overlay {
            position: absolute;
            inset: 0;
            background: rgba(1, 10, 2, 0.52);
            z-index: 1;
            pointer-events: none;
        }

        /* Vignette pinggir */
        #ldr-vignette {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 65% 65% at 50% 45%, transparent 20%, rgba(0,0,0,0.68) 100%);
            z-index: 2;
            pointer-events: none;
        }

        /* Canvas partikel */
        #ldr-bg-canvas {
            position: absolute;
            inset: 0;
            width: 100%; height: 100%;
            z-index: 3;
            pointer-events: none;
        }

        /* Wrapper tengah */
        #loader-center {
            position: absolute;
            inset: 0;
            z-index: 4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* ── Ring-ring energi ── */
        .ldr-rings {
            position: relative;
            width: 280px; height: 280px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 36px;
        }
        .ldr-ring {
            position: absolute;
            border-radius: 50%;
            top: 50%; left: 50%;
        }
        .ldr-ring-1 {
            width: 260px; height: 260px; margin: -130px 0 0 -130px;
            border: 1.5px solid transparent;
            border-top-color: rgba(34,197,94,0.9);
            border-right-color: rgba(34,197,94,0.3);
            animation: ringSpin 2.4s linear infinite;
        }
        .ldr-ring-2 {
            width: 220px; height: 220px; margin: -110px 0 0 -110px;
            border: 1px dashed rgba(74,222,128,0.25);
            animation: ringSpin 6s linear infinite reverse;
        }
        .ldr-ring-3 {
            width: 178px; height: 178px; margin: -89px 0 0 -89px;
            border: 1.5px solid transparent;
            border-bottom-color: rgba(34,197,94,0.7);
            border-left-color: rgba(34,197,94,0.2);
            animation: ringSpin 3.8s linear infinite;
        }
        .ldr-ring-4 {
            width: 136px; height: 136px; margin: -68px 0 0 -68px;
            border: 1px solid transparent;
            border-top-color: rgba(250,204,21,0.5);
            animation: ringSpin 5s linear infinite reverse;
        }
        @keyframes ringSpin { to { transform: rotate(360deg); } }

        /* Dot di ujung ring */
        .ldr-ring-1::before, .ldr-ring-3::before {
            content: '';
            position: absolute;
            width: 7px; height: 7px;
            border-radius: 50%;
            top: -3.5px; left: 50%; margin-left: -3.5px;
        }
        .ldr-ring-1::before { background: #22c55e; box-shadow: 0 0 10px 3px rgba(34,197,94,1); }
        .ldr-ring-3::before { background: #fbbf24; box-shadow: 0 0 10px 3px rgba(251,191,36,1); }

        /* ── Kubus 3D tengah ── */
        .ldr-cube-wrap {
            position: absolute;
            width: 72px; height: 72px;
            perspective: 500px;
        }
        .ldr-cube {
            width: 72px; height: 72px;
            transform-style: preserve-3d;
            animation: cubeRotate 9s linear infinite;
        }
        @keyframes cubeRotate {
            0%   { transform: rotateX(15deg) rotateY(0deg); }
            100% { transform: rotateX(15deg) rotateY(360deg); }
        }
        .ldr-face {
            position: absolute; width: 72px; height: 72px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(34,197,94,0.55);
            background: rgba(2,18,5,0.75);
        }
        .ldr-face.front  { transform: translateZ(36px); }
        .ldr-face.back   { transform: rotateY(180deg) translateZ(36px); }
        .ldr-face.left   { transform: rotateY(-90deg) translateZ(36px); }
        .ldr-face.right  { transform: rotateY(90deg)  translateZ(36px); }
        .ldr-face.top    { transform: rotateX(90deg)  translateZ(36px); }
        .ldr-face.bottom { transform: rotateX(-90deg) translateZ(36px); }

        .ldr-face.front .logo-box {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, #15803d, #22c55e);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 22px rgba(34,197,94,0.9), 0 0 50px rgba(34,197,94,0.4);
            animation: logoPulse 1.8s ease-in-out infinite;
        }
        .ldr-face.front .logo-box i { color: #fff; font-size: 22px; }
        @keyframes logoPulse {
            0%,100% { box-shadow: 0 0 18px rgba(34,197,94,0.8), 0 0 36px rgba(34,197,94,0.3); }
            50%     { box-shadow: 0 0 32px rgba(34,197,94,1),   0 0 70px rgba(34,197,94,0.6); }
        }
        .ldr-face .mini-i { color: rgba(34,197,94,0.3); font-size: 16px; }

        /* Scan line pada kubus */
        .ldr-cube-scan {
            position: absolute;
            left: -4px; right: -4px; height: 1.5px;
            background: linear-gradient(90deg, transparent, rgba(34,197,94,0.9), rgba(134,239,172,1), rgba(34,197,94,0.9), transparent);
            box-shadow: 0 0 6px rgba(34,197,94,0.8);
            animation: cubeScan 1.8s ease-in-out infinite;
            z-index: 5;
        }
        @keyframes cubeScan {
            0%   { top: -2px; opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { top: calc(100% + 2px); opacity: 0; }
        }

        /* Cahaya bawah / proyektor */
        .ldr-platform {
            position: absolute;
            bottom: -18px; left: 50%;
            transform: translateX(-50%);
            width: 160px; height: 8px;
            background: linear-gradient(to right, transparent, rgba(34,197,94,0.5), rgba(74,222,128,0.8), rgba(34,197,94,0.5), transparent);
            border-radius: 50%;
            filter: blur(3px);
            animation: platformPulse 2s ease-in-out infinite;
        }
        @keyframes platformPulse {
            0%,100% { opacity: 0.6; transform: translateX(-50%) scaleX(0.9); }
            50%     { opacity: 1;   transform: translateX(-50%) scaleX(1.1); }
        }

        /* ── Brand ── */
        .ldr-brand { text-align: center; }
        .ldr-logo-row {
            display: inline-flex; align-items: center; gap: 12px; margin-bottom: 6px;
            animation: brandIn 1s ease both;
        }
        @keyframes brandIn {
            from { opacity: 0; transform: translateY(14px) scale(0.95); filter: blur(8px); }
            to   { opacity: 1; transform: translateY(0)    scale(1);    filter: blur(0); }
        }
        .ldr-logo-icon {
            width: 42px; height: 42px; background: #16a34a; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 16px rgba(34,197,94,0.6);
        }
        .ldr-logo-icon i { color: #fff; font-size: 20px; }
        .ldr-name {
            font-family: 'Outfit', sans-serif;
            font-size: 2.8rem; font-weight: 700; color: #fff;
            letter-spacing: 0.18em;
            text-shadow: 0 0 24px rgba(34,197,94,0.35);
            animation: brandIn 1s 0.1s ease both;
        }
        .ldr-tagline {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.65rem; font-weight: 300;
            color: rgba(74,222,128,0.5); letter-spacing: 0.38em; text-transform: uppercase;
            margin-top: 3px;
            animation: brandIn 1s 0.2s ease both;
        }
        .ldr-divider {
            width: 0; height: 1px; margin: 10px auto 0;
            background: linear-gradient(90deg, transparent, rgba(34,197,94,0.7), transparent);
            animation: divExpand 0.7s 0.8s ease both;
        }
        @keyframes divExpand { from { width: 0; } to { width: 220px; } }

        /* ── Progress ── */
        .ldr-progress-wrap {
            margin-top: 26px; width: 280px; text-align: center;
            animation: brandIn 0.8s 0.4s ease both;
        }
        .ldr-progress-head {
            display: flex; justify-content: space-between;
            margin-bottom: 6px;
        }
        .ldr-progress-label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.6rem; color: rgba(74,222,128,0.5);
            letter-spacing: 0.15em;
        }
        .ldr-pct {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.65rem; color: rgba(74,222,128,0.85);
        }
        .ldr-track {
            width: 100%; height: 2px;
            background: rgba(255,255,255,0.06); border-radius: 2px;
            overflow: visible; position: relative;
        }
        .ldr-bar {
            height: 100%; width: 0%;
            background: linear-gradient(90deg, #15803d, #22c55e, #86efac);
            border-radius: 2px;
            box-shadow: 0 0 14px rgba(34,197,94,0.9);
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .ldr-bar::after {
            content: ''; position: absolute; right: -5px; top: -4px;
            width: 10px; height: 10px; background: #22c55e; border-radius: 50%;
            box-shadow: 0 0 12px rgba(34,197,94,1);
        }
        .ldr-shine {
            position: absolute; top: 0; left: 0;
            width: 50px; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.55), transparent);
            animation: shine 1.6s ease-in-out infinite;
        }
        @keyframes shine { 0% { transform: translateX(-50px); } 100% { transform: translateX(330px); } }
        .ldr-msg {
            font-family: 'Share Tech Mono', monospace; font-size: 0.62rem;
            color: rgba(74,222,128,0.65); letter-spacing: 0.1em;
            margin-top: 10px; min-height: 1.1em;
            transition: opacity 0.2s ease;
        }
        .ldr-msg::before { content: '> '; color: rgba(34,197,94,0.4); }

        /* ═══════════════════════════════════════════════
           HALAMAN UTAMA — kode asli tidak diubah
        ═══════════════════════════════════════════════ */
        .sawah-full-bg {
            position: relative;
            background-image: url("{{ asset('images/petani.jpg') }}");
            background-size: cover;
            background-position: center top;
            background-attachment: fixed;
        }
        .sawah-full-bg::before {
            content: '';
            position: fixed; inset: 0;
            background: rgba(0, 8, 2, 0.62);
            pointer-events: none; z-index: 0;
        }
        .sawah-full-bg > * { position: relative; z-index: 1; }

        #particles-canvas {
            position: fixed; inset: 0;
            width: 100%; height: 100%;
            pointer-events: none; z-index: 2;
        }
        .light-sweep {
            position: fixed; inset: 0; pointer-events: none; z-index: 1;
            background: radial-gradient(ellipse 60% 40% at 50% 50%, rgba(80,200,80,0.07) 0%, transparent 70%);
            animation: sweepLight 12s ease-in-out infinite;
        }
        @keyframes sweepLight {
            0%   { background-position-x:-20%; opacity:.6; }
            25%  { background-position-x:30%;  opacity:1; }
            50%  { background-position-x:80%;  opacity:.7; }
            75%  { background-position-x:50%;  opacity:1; }
            100% { background-position-x:-20%; opacity:.6; }
        }

        nav {
            position: fixed; top:0; width:100%; z-index:50;
            background: linear-gradient(to bottom, rgba(0,0,0,0.55), transparent);
            backdrop-filter: blur(0px);
            transition: background 0.4s, backdrop-filter 0.4s;
        }
        nav.scrolled { background: rgba(3,18,5,0.85); backdrop-filter: blur(12px); }

        .hero-section {
            min-height: 100vh; display:flex;
            align-items:center; justify-content:center; position:relative;
        }
        .hero-title { animation: fadeUp 1s ease both; }
        .hero-sub   { animation: fadeUp 1s 0.2s ease both; }
        .hero-btns  { animation: fadeUp 1s 0.4s ease both; }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(28px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .features-section { position:relative; padding:7rem 0 6rem; }
        .features-shimmer { position:absolute; inset:0; pointer-events:none; overflow:hidden; }
        .features-shimmer::before, .features-shimmer::after {
            content:''; position:absolute; border-radius:50%; filter:blur(80px); opacity:.18;
        }
        .features-shimmer::before { width:600px;height:600px;background:radial-gradient(circle,#22c55e,transparent 70%);top:-100px;left:-150px;animation:driftA 18s ease-in-out infinite; }
        .features-shimmer::after  { width:500px;height:500px;background:radial-gradient(circle,#16a34a,transparent 70%);bottom:-80px;right:-100px;animation:driftB 22s ease-in-out infinite; }
        @keyframes driftA { 0%,100%{transform:translate(0,0)} 33%{transform:translate(120px,60px)} 66%{transform:translate(60px,-40px)} }
        @keyframes driftB { 0%,100%{transform:translate(0,0)} 33%{transform:translate(-80px,-50px)} 66%{transform:translate(-40px,80px)} }
        .features-content { position:relative; z-index:3; }

        .feature-card {
            background: rgba(10,30,12,0.45); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
            border:1px solid rgba(80,200,100,0.18);
            box-shadow:0 8px 32px rgba(0,0,0,0.40),inset 0 1px 0 rgba(255,255,255,0.06);
            color:#fff; position:relative; overflow:hidden;
            opacity:0; transform:translateY(30px);
            transition:opacity 0.6s ease,transform 0.6s ease,box-shadow 0.35s ease,border-color 0.35s ease,background 0.35s ease;
        }
        .feature-card::before { content:'';position:absolute;inset:0;background:radial-gradient(circle at 50% 0%,rgba(34,197,94,0.12),transparent 70%);opacity:0;transition:opacity 0.35s ease; }
        .feature-card:hover::before { opacity:1; }
        .feature-card:hover { background:rgba(15,45,18,0.60);border-color:rgba(80,200,100,0.40);transform:translateY(-8px) scale(1.02) !important;box-shadow:0 24px 60px rgba(0,0,0,0.50),0 0 30px rgba(34,197,94,0.12); }
        .feature-card.visible { opacity:1; transform:translateY(0); }
        .feature-card h3 { color:#fff; }
        .feature-card p  { color:rgba(190,230,195,0.85); }
        .feature-icon-box { background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.25); }
        .feature-icon-box i { color:#4ade80; }
        .section-tag { display:inline-block;background:rgba(34,197,94,0.15);backdrop-filter:blur(8px);border:1px solid rgba(34,197,94,0.30);border-radius:999px;padding:4px 18px;font-size:.82rem;font-weight:600;color:#4ade80;letter-spacing:.06em;margin-bottom:12px; }
        .features-heading    { color:#fff; text-shadow:0 0 40px rgba(34,197,94,0.3),0 2px 12px rgba(0,0,0,0.6); }
        .features-subheading { color:rgba(180,230,185,0.85); }
        #cta-section { position:relative; }
        footer { position:relative; }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════
     LOADING SCREEN
══════════════════════════════════════ -->
<div id="patani-loader">

    {{-- Overlay & vignette --}}
    <div id="ldr-overlay"></div>
    <div id="ldr-vignette"></div>

    {{-- Canvas partikel --}}
    <canvas id="ldr-bg-canvas"></canvas>

    {{-- Konten tengah --}}
    <div id="loader-center">

        <!-- Ring + Kubus -->
        <div class="ldr-rings">
            <div class="ldr-ring ldr-ring-1"></div>
            <div class="ldr-ring ldr-ring-2"></div>
            <div class="ldr-ring ldr-ring-3"></div>
            <div class="ldr-ring ldr-ring-4"></div>
            <div class="ldr-cube-wrap">
                <div class="ldr-cube-scan"></div>
                <div class="ldr-cube">
                    <div class="ldr-face front"><div class="logo-box"><i class="fas fa-seedling"></i></div></div>
                    <div class="ldr-face back"><i class="fas fa-seedling mini-i"></i></div>
                    <div class="ldr-face left"><i class="fas fa-leaf mini-i"></i></div>
                    <div class="ldr-face right"><i class="fas fa-spa mini-i"></i></div>
                    <div class="ldr-face top"><i class="fas fa-seedling mini-i"></i></div>
                    <div class="ldr-face bottom"></div>
                </div>
            </div>
            <div class="ldr-platform"></div>
        </div>

        <!-- Brand -->
        <div class="ldr-brand">
            <div class="ldr-logo-row">
                <div class="ldr-logo-icon"><i class="fas fa-seedling"></i></div>
                <div class="ldr-name">PATANI</div>
            </div>
            <div class="ldr-tagline">Platform Pertanian Digital Indonesia</div>
            <div class="ldr-divider"></div>
        </div>

        <!-- Progress -->
        <div class="ldr-progress-wrap">
            <div class="ldr-progress-head">
                <span class="ldr-progress-label">Memulai</span>
                <span class="ldr-pct" id="ldr-pct">0%</span>
            </div>
            <div class="ldr-track">
                <div class="ldr-bar" id="ldr-bar"><div class="ldr-shine"></div></div>
            </div>
            <div class="ldr-msg" id="ldr-msg">Menginisialisasi sistem...</div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════
     HALAMAN UTAMA — kode asli tidak diubah
══════════════════════════════════════ -->
<div class="light-sweep"></div>
<canvas id="particles-canvas"></canvas>

<div class="sawah-full-bg">

    <nav id="navbar">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-green-600 shadow-md">
                        <i class="fas fa-seedling text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-white">PATANI</span>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-white hover:text-green-400 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 shadow-md transition">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="max-w-4xl mx-auto text-center px-6 text-white">
            <h1 class="hero-title text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
                Platform Pertanian Digital
                <span class="text-green-400" style="text-shadow: 0 0 30px rgba(74,222,128,0.5);">Indonesia</span>
            </h1>
            <p class="hero-sub text-xl md:text-2xl text-gray-300 mb-10">
                Tingkatkan produktivitas pertanian Anda dengan teknologi digital.
                Monitoring lahan, prediksi panen, dan konsultasi dalam satu platform.
            </p>
            <div class="hero-btns flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}"
                   class="px-8 py-4 bg-green-600 rounded-xl text-lg font-semibold hover:bg-green-500 shadow-lg transition hover:scale-105"
                   style="box-shadow: 0 0 20px rgba(34,197,94,0.35);">Mulai Sekarang</a>
                <a href="#features"
                   class="px-8 py-4 border-2 border-white/40 rounded-xl text-lg font-semibold hover:bg-white/10 transition hover:scale-105 backdrop-blur-sm">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </section>

    <section id="features" class="features-section">
        <div class="features-shimmer"></div>
        <div class="features-content max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="section-tag">✦ Teknologi Terdepan</div>
                <h2 class="text-4xl font-bold features-heading mb-4">Fitur Unggulan</h2>
                <p class="text-lg features-subheading">Solusi lengkap untuk petani modern</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card p-8 rounded-2xl">
                    <div class="h-14 w-14 feature-icon-box rounded-xl flex items-center justify-center mb-6"><i class="fas fa-chart-line text-2xl"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Prediksi Panen</h3>
                    <p>Algoritma AI untuk memprediksi hasil panen berdasarkan kondisi lahan dan cuaca.</p>
                </div>
                <div class="feature-card p-8 rounded-2xl">
                    <div class="h-14 w-14 feature-icon-box rounded-xl flex items-center justify-center mb-6"><i class="fas fa-map text-2xl"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Monitoring Lahan</h3>
                    <p>Pantau kondisi sawah dan riwayat panen secara real-time.</p>
                </div>
                <div class="feature-card p-8 rounded-2xl">
                    <div class="h-14 w-14 feature-icon-box rounded-xl flex items-center justify-center mb-6"><i class="fas fa-cloud-sun text-2xl"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Info Cuaca</h3>
                    <p>Prakiraan cuaca akurat untuk perencanaan pertanian.</p>
                </div>
                <div class="feature-card p-8 rounded-2xl">
                    <div class="h-14 w-14 feature-icon-box rounded-xl flex items-center justify-center mb-6"><i class="fas fa-robot text-2xl"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Chatbot AI</h3>
                    <p>Konsultasi pertanian 24/7 dengan asisten AI yang cerdas.</p>
                </div>
                <div class="feature-card p-8 rounded-2xl">
                    <div class="h-14 w-14 feature-icon-box rounded-xl flex items-center justify-center mb-6"><i class="fas fa-comments text-2xl"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Forum Diskusi</h3>
                    <p>Berbagi pengalaman bersama komunitas petani Indonesia.</p>
                </div>
                <div class="feature-card p-8 rounded-2xl">
                    <div class="h-14 w-14 feature-icon-box rounded-xl flex items-center justify-center mb-6"><i class="fas fa-chart-bar text-2xl"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Analitik & Laporan</h3>
                    <p>Visualisasi data produksi dan analisis kinerja lahan.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-6 px-6">
        <div class="rounded-2xl py-16 px-8 text-center" style="background:rgba(10,30,12,0.45);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(80,200,100,0.18);box-shadow:0 8px 32px rgba(0,0,0,0.40),inset 0 1px 0 rgba(255,255,255,0.06);">
            <h2 class="text-4xl font-bold mb-5 text-white" style="text-shadow:0 0 30px rgba(74,222,128,0.2);">Siap Meningkatkan Produktivitas Pertanian Anda?</h2>
            <p class="text-lg mb-10" style="color:rgba(190,230,195,0.85);">Bergabunglah dengan ribuan petani yang sudah merasakan manfaatnya</p>
            <a href="{{ route('register') }}" class="inline-block px-10 py-4 bg-green-600 text-white rounded-xl text-lg font-semibold hover:bg-green-500 hover:scale-105 transition" style="box-shadow:0 0 24px rgba(34,197,94,0.35);">Daftar Gratis Sekarang</a>
        </div>
    </section>

    <footer class="py-6 px-6 pb-8">
        <div class="rounded-2xl py-8" style="background:rgba(10,30,12,0.45);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(80,200,100,0.18);box-shadow:0 8px 32px rgba(0,0,0,0.40),inset 0 1px 0 rgba(255,255,255,0.06);">
            <div class="text-center text-sm" style="color:rgba(170,220,175,0.60);">
                <p>PATANI DI BUAT OLEH KELOMPOK 4</p>
                <p class="mt-2">Platform Pertanian Digital Indonesia</p>
            </div>
        </div>
    </footer>

</div>

<script>
/* ─── LOADING BACKGROUND CANVAS — partikel kunang-kunang lembut ─── */
(function () {
    const canvas = document.getElementById('ldr-bg-canvas');
    const ctx = canvas.getContext('2d');
    let W, H;
    function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
    resize();
    window.addEventListener('resize', resize);

    const pts = Array.from({ length: 55 }, () => ({
        x: Math.random() * window.innerWidth,
        y: Math.random() * window.innerHeight,
        r: Math.random() * 1.4 + 0.3,
        sx: (Math.random() - 0.5) * 0.3,
        sy: -(Math.random() * 0.35 + 0.08),
        o: Math.random() * 0.5 + 0.15,
        p: Math.random() * Math.PI * 2,
        hue: 110 + Math.random() * 20,
    }));

    let f = 0;
    function draw() {
        ctx.clearRect(0, 0, W, H);
        f++;
        pts.forEach(p => {
            const a = p.o * (0.5 + 0.5 * Math.sin(f * 0.025 + p.p));
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `hsla(${p.hue}, 75%, 60%, ${a})`;
            ctx.shadowBlur = 10;
            ctx.shadowColor = `hsla(${p.hue}, 75%, 60%, ${a * 0.5})`;
            ctx.fill();
            ctx.shadowBlur = 0;
            p.x += p.sx + Math.sin(f * 0.01 + p.p) * 0.18;
            p.y += p.sy;
            if (p.y < -5) p.y = H + 5;
            if (p.x < -5) p.x = W + 5;
            if (p.x > W + 5) p.x = -5;
        });
        requestAnimationFrame(draw);
    }
    draw();
})();

/* ─── LOADING ─── */
(function () {
    const bar    = document.getElementById('ldr-bar');
    const msg    = document.getElementById('ldr-msg');
    const pct    = document.getElementById('ldr-pct');
    const loader = document.getElementById('patani-loader');
    const steps  = [
        { pct: 18,  txt: 'Memuat data lahan sawah...' },
        { pct: 40,  txt: 'Menyiapkan prediksi panen...' },
        { pct: 62,  txt: 'Menghubungkan layanan cuaca...' },
        { pct: 84,  txt: 'Mengaktifkan dashboard petani...' },
        { pct: 100, txt: 'Selamat datang di PATANI! 🌱' },
    ];
    let i = 0;
    function tick() {
        if (i >= steps.length) { setTimeout(() => loader.classList.add('hide'), 700); return; }
        const s = steps[i++];
        bar.style.width  = s.pct + '%';
        pct.textContent  = s.pct + '%';
        msg.style.opacity = '0';
        setTimeout(() => {
            msg.textContent   = s.txt;
            msg.style.opacity = '1';
            setTimeout(tick, s.pct === 100 ? 800 : 450);
        }, 200);
    }
    setTimeout(tick, 350);
    setTimeout(() => loader.classList.add('hide'), 5000);
})();

/* ─── NAVBAR ─── */
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 60));

/* ─── FIREFLY ─── */
(function () {
    const canvas = document.getElementById('particles-canvas');
    const ctx    = canvas.getContext('2d');
    function resize() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
    resize();
    window.addEventListener('resize', resize);
    const COUNT = 90;
    const particles = Array.from({ length: COUNT }, () => ({
        x: Math.random() * window.innerWidth,
        y: Math.random() * window.innerHeight,
        r: Math.random() * 1.8 + 0.4,
        speedX: (Math.random() - 0.5) * 0.35,
        speedY: -(Math.random() * 0.4 + 0.1),
        opacity: Math.random() * 0.6 + 0.2,
        pulse: Math.random() * Math.PI * 2,
        hue: Math.random() > 0.5 ? '110' : '60',
    }));
    let frame = 0;
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        frame++;
        particles.forEach(p => {
            const alpha = p.opacity * (0.6 + 0.4 * Math.sin(frame * 0.03 + p.pulse));
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle  = `hsla(${p.hue}, 80%, 65%, ${alpha})`;
            ctx.shadowBlur = 8;
            ctx.shadowColor= `hsla(${p.hue}, 80%, 65%, ${alpha * 0.6})`;
            ctx.fill();
            p.x += p.speedX + Math.sin(frame * 0.012 + p.pulse) * 0.2;
            p.y += p.speedY;
            if (p.y < -5)               p.y = canvas.height + 5;
            if (p.x < -5)               p.x = canvas.width  + 5;
            if (p.x > canvas.width + 5) p.x = -5;
        });
        requestAnimationFrame(draw);
    }
    draw();
})();

/* ─── SCROLL REVEAL ─── */
(function () {
    const cards = document.querySelectorAll('.feature-card');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const idx = Array.from(cards).indexOf(entry.target);
                setTimeout(() => entry.target.classList.add('visible'), idx * 80);
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    cards.forEach(c => io.observe(c));
})();
</script>

</body>
</html>