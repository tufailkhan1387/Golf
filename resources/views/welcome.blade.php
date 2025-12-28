<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Auto Golf - Your ultimate golf companion app for tracking your game, improving your skills, and connecting with fellow golfers.">
    <title>Auto Golf - Elevate Your Golf Game</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        :root {
            --primary-green: #2d5016;
            --secondary-green: #4a7c2a;
            --light-green: #6b9f4a;
            --accent-gold: #d4af37;
            --dark-bg: #1a1a1a;
            --light-bg: #f8f9fa;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 50%, var(--light-green) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 100px 0;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(212, 175, 55, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(212, 175, 55, 0.1) 0%, transparent 50%),
                url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.6;
            animation: backgroundMove 20s ease-in-out infinite;
        }

        @keyframes backgroundMove {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-20px, -20px); }
        }

        .hero-section::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
        }

        .hero-title {
            font-size: 5rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: linear-gradient(135deg, #ffffff 0%, var(--accent-gold) 50%, #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            animation: titleGlow 3s ease-in-out infinite;
            position: relative;
        }

        .hero-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
            border-radius: 2px;
        }

        @keyframes titleGlow {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.2); }
        }

        .hero-subtitle {
            font-size: 1.8rem;
            font-weight: 400;
            margin-bottom: 2rem;
            opacity: 0.95;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--accent-gold);
            font-weight: 600;
        }

        .hero-description {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            max-width: 700px;
            line-height: 1.8;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .hero-image {
            position: relative;
            z-index: 2;
        }

        .hero-image-wrapper {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 
                0 30px 80px rgba(0,0,0,0.4),
                0 0 0 4px rgba(212, 175, 55, 0.3),
                inset 0 0 50px rgba(0,0,0,0.1);
            transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
            transition: all 0.5s ease;
        }

        .hero-image-wrapper:hover {
            transform: perspective(1000px) rotateY(0deg) rotateX(0deg) scale(1.05);
            box-shadow: 
                0 40px 100px rgba(0,0,0,0.5),
                0 0 0 6px rgba(212, 175, 55, 0.5),
                inset 0 0 50px rgba(0,0,0,0.1);
        }

        .hero-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.5s ease;
        }

        .hero-image-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, transparent 50%);
            z-index: 1;
            pointer-events: none;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 18px 45px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-hero::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-hero:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-hero span {
            position: relative;
            z-index: 1;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--accent-gold) 0%, #f5c842 100%);
            color: var(--dark-bg);
            border: none;
            box-shadow: 
                0 10px 30px rgba(212, 175, 55, 0.4),
                0 0 0 0 rgba(212, 175, 55, 0.7);
            animation: buttonPulse 2s ease-in-out infinite;
        }

        @keyframes buttonPulse {
            0%, 100% {
                box-shadow: 
                    0 10px 30px rgba(212, 175, 55, 0.4),
                    0 0 0 0 rgba(212, 175, 55, 0.7);
            }
            50% {
                box-shadow: 
                    0 10px 30px rgba(212, 175, 55, 0.6),
                    0 0 0 10px rgba(212, 175, 55, 0);
            }
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #f5c842 0%, var(--accent-gold) 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 
                0 15px 40px rgba(212, 175, 55, 0.6),
                0 0 0 0 rgba(212, 175, 55, 0.7);
        }

        .btn-outline-custom {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
        }

        .btn-outline-custom:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: white;
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 255, 255, 0.2);
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            color: white;
            text-align: center;
            animation: bounce 2s infinite;
        }

        .scroll-indicator i {
            font-size: 2rem;
            opacity: 0.7;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: var(--light-bg);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #666;
            text-align: center;
            margin-bottom: 4rem;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 100%;
            margin-bottom: 30px;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .feature-image-wrapper {
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 15px;
            margin-bottom: 25px;
            position: relative;
        }

        .feature-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-image {
            transform: scale(1.1);
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 15px;
        }

        .feature-description {
            color: #666;
            line-height: 1.8;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            padding: 80px 0;
            color: white;
            position: relative;
            background-attachment: fixed;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(45, 80, 22, 0.85);
        }

        .stats-section .container {
            position: relative;
            z-index: 2;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--accent-gold);
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: white;
        }

        .cta-content {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
        }

        .cta-description {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2.5rem;
        }

        /* Pricing Section */
        .pricing-section {
            padding: 100px 0;
            background: white;
        }

        .pricing-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: 100%;
            position: relative;
            border: 2px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .pricing-card.featured {
            border: 3px solid var(--accent-gold);
            background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
            transform: scale(1.05);
        }

        .pricing-card.featured:hover {
            transform: scale(1.08) translateY(-10px);
        }

        .pricing-badge {
            position: absolute;
            top: -15px;
            right: 20px;
            background: var(--accent-gold);
            color: var(--dark-bg);
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .pricing-image-wrapper {
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 15px;
            margin-bottom: 20px;
            position: relative;
        }

        .pricing-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .pricing-card:hover .pricing-image {
            transform: scale(1.1);
        }

        .pricing-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 10px;
        }

        .pricing-subtitle {
            font-size: 1rem;
            color: #666;
            margin-bottom: 25px;
        }

        .pricing-price {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-green);
            margin-bottom: 5px;
        }

        .pricing-period {
            font-size: 1rem;
            color: #666;
            margin-bottom: 10px;
        }

        .pricing-equivalent {
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 15px;
        }

        .pricing-savings {
            display: inline-block;
            background: var(--light-green);
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .pricing-description {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
            line-height: 1.6;
        }

        .pricing-features {
            list-style: none;
            padding: 0;
            margin: 30px 0;
            text-align: left;
        }

        .pricing-features li {
            padding: 10px 0;
            color: #555;
            font-size: 0.95rem;
        }

        .pricing-features li i {
            color: var(--secondary-green);
            margin-right: 10px;
        }

        .pricing-footer {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
            font-size: 0.9rem;
            color: #666;
        }

        .pricing-footer i {
            color: var(--secondary-green);
            margin-right: 5px;
        }

        /* Footer */
        .footer {
            background: var(--dark-bg);
            color: white;
            padding: 50px 0 30px;
        }

        .footer-content {
            text-align: center;
        }

        .footer-logo {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent-gold);
            margin-bottom: 20px;
        }

        .footer-links {
            margin: 30px 0;
        }

        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            margin: 0 20px;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--accent-gold);
        }

        .footer-copyright {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 3rem;
            }

            .hero-subtitle {
                font-size: 1.3rem;
            }

            .hero-description {
                font-size: 1rem;
            }

            .hero-image-wrapper {
                transform: perspective(1000px) rotateY(0deg) rotateX(0deg);
            }

            .section-title {
                font-size: 2rem;
            }

            .cta-title {
                font-size: 2rem;
            }

            .pricing-card.featured {
                transform: scale(1);
            }

            .pricing-card.featured:hover {
                transform: scale(1.02) translateY(-10px);
            }

            .pricing-price {
                font-size: 2.5rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Golf Ball Animation */
        .golf-ball {
            position: absolute;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0.3) 100%);
            border-radius: 50%;
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
            box-shadow: 
                0 0 20px rgba(255,255,255,0.3),
                inset -10px -10px 20px rgba(0,0,0,0.2);
        }

        .golf-ball::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(0,0,0,0.1) 0%, transparent 50%);
        }

        .golf-ball::after {
            content: '';
            position: absolute;
            top: 20%;
            left: 20%;
            width: 30%;
            height: 30%;
            border-radius: 50%;
            background: rgba(0,0,0,0.1);
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg) scale(1);
            }
            25% {
                transform: translateY(-30px) rotate(90deg) scale(1.1);
            }
            50% {
                transform: translateY(-60px) rotate(180deg) scale(1);
            }
            75% {
                transform: translateY(-30px) rotate(270deg) scale(1.1);
            }
        }

        .golf-ball-1 {
            top: 15%;
            right: 8%;
            animation-delay: 0s;
            width: 100px;
            height: 100px;
        }

        .golf-ball-2 {
            bottom: 15%;
            right: 15%;
            animation-delay: 2.5s;
            width: 70px;
            height: 70px;
        }

        .golf-ball-3 {
            top: 45%;
            left: 3%;
            animation-delay: 5s;
            width: 90px;
            height: 90px;
        }

        /* Decorative Shapes */
        .hero-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.1);
            animation: shapeFloat 15s ease-in-out infinite;
        }

        .shape-1 {
            width: 200px;
            height: 200px;
            top: 10%;
            left: -5%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 150px;
            height: 150px;
            bottom: 10%;
            right: -3%;
            animation-delay: 3s;
        }

        @keyframes shapeFloat {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.3;
            }
            50% {
                transform: translate(30px, -30px) scale(1.2);
                opacity: 0.5;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-shape shape-1"></div>
        <div class="hero-shape shape-2"></div>
        <div class="golf-ball golf-ball-1"></div>
        <div class="golf-ball golf-ball-2"></div>
        <div class="golf-ball golf-ball-3"></div>
        
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8 col-xl-7 hero-content fade-in-up text-center">
                    <div class="mb-3">
                        <span style="display: inline-block; background: rgba(212, 175, 55, 0.2); padding: 8px 20px; border-radius: 30px; font-size: 0.9rem; font-weight: 600; color: var(--accent-gold); border: 1px solid rgba(212, 175, 55, 0.3);">
                            <i class="fas fa-star me-2"></i>Premium Golf Platform
                        </span>
                    </div>
                    <h1 class="hero-title">Auto Golf</h1>
                    <p class="hero-subtitle">Elevate Your Golf Game</p>
                    <p class="hero-description mx-auto">
                        Track your progress, improve your skills, and connect with a community of passionate golfers. 
                        Your ultimate companion for mastering the game.
                    </p>
                    <div class="hero-buttons justify-content-center">
                        <a href="{{ route('admin.login') }}" class="btn btn-hero btn-primary-custom">
                            <span>
                                <i class="fas fa-rocket me-2"></i>Get Started
                            </span>
                        </a>
                        <a href="#pricing" class="btn btn-hero btn-outline-custom">
                            <span>
                                <i class="fas fa-tag me-2"></i>View Plans
                            </span>
                        </a>
                    </div>
                    <div class="mt-4 d-flex align-items-center justify-content-center gap-4 flex-wrap" style="opacity: 0.8;">
                        <div>
                            <i class="fas fa-check-circle me-2" style="color: var(--accent-gold);"></i>
                            <span>Free 7-Day Trial</span>
                        </div>
                        <div>
                            <i class="fas fa-check-circle me-2" style="color: var(--accent-gold);"></i>
                            <span>Cancel Anytime</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose Auto Golf?</h2>
            <p class="section-subtitle">Everything you need to improve your golf game in one place</p>
            


            
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-image-wrapper">
                            <img src="{{ asset('images/pexels-freestockpro-1175015.jpg') }}" 
                                 alt="Track Progress" 
                                 class="feature-image">
                        </div>
                        <h3 class="feature-title">Track Your Progress</h3>
                        <p class="feature-description">
                            Monitor your scores, analyze your performance, and track improvements over time with detailed statistics and insights.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-image-wrapper">
                            <img src="{{ asset('images/pexels-tom-jackson-1238161-2891910.jpg') }}" 
                                 alt="Learn & Improve" 
                                 class="feature-image">
                        </div>
                        <h3 class="feature-title">Learn & Improve</h3>
                        <p class="feature-description">
                            Access personalized training programs, tips from professionals, and techniques to enhance your skills on the course.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-image-wrapper">
                            <img src="{{ asset('images/pexels-nathan-nedley-20160-92858.jpg') }}" 
                                 alt="Connect & Compete" 
                                 class="feature-image">
                        </div>
                        <h3 class="feature-title">Connect & Compete</h3>
                        <p class="feature-description">
                            Join a community of golfers, participate in challenges, and compete with friends to make your game more exciting.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-image-wrapper">
                            <img src="{{ asset('images/pexels-kindelmedia-6572955.jpg') }}" 
                                 alt="Mobile Friendly" 
                                 class="feature-image">
                        </div>
                        <h3 class="feature-title">Mobile Friendly</h3>
                        <p class="feature-description">
                            Access your golf data anywhere, anytime. Our responsive design works perfectly on all your devices.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-image-wrapper">
                            <img src="{{ asset('images/pexels-centre-for-ageing-better-55954677-7858232.jpg') }}" 
                                 alt="Stay Updated" 
                                 class="feature-image">
                        </div>
                        <h3 class="feature-title">Stay Updated</h3>
                        <p class="feature-description">
                            Get instant notifications about your achievements, upcoming events, and personalized recommendations.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-image-wrapper">
                            <img src="{{ asset('images/pexels-tyler-hendy-9620-54123.jpg') }}" 
                                 alt="Secure & Reliable" 
                                 class="feature-image">
                        </div>
                        <h3 class="feature-title">Secure & Reliable</h3>
                        <p class="feature-description">
                            Your data is safe with us. We use industry-leading security measures to protect your information.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section" style="background: linear-gradient(rgba(45, 80, 22, 0.9), rgba(74, 124, 42, 0.9)), url('{{ asset('images/pexels-tom-jackson-1238161-2891910.jpg') }}') center/cover;">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Games Tracked</div>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <div class="stat-number">4.8★</div>
                    <div class="stat-label">User Rating</div>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing-section">
        <div class="container">
            <h2 class="section-title">Choose Your Plan</h2>
            <p class="section-subtitle">Select the perfect plan for your golf journey</p>
            
            <div class="row justify-content-center">
                <!-- Monthly Coach -->
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-image-wrapper">
                            <img src="{{ asset('images/pexels-freestockpro-1175015.jpg') }}" 
                                 alt="Monthly Coach" 
                                 class="pricing-image">
                        </div>
                        <h3 class="pricing-title">🏃 MONTHLY COACH</h3>
                        <p class="pricing-subtitle">$29 per month</p>
                        <div class="pricing-price">$29</div>
                        <div class="pricing-period">per month</div>
                        <p class="pricing-description">Cancel anytime</p>
                        <a href="{{ route('admin.login') }}" class="btn btn-hero btn-outline-custom" style="color: var(--primary-green); border-color: var(--primary-green);">
                            Get Started
                        </a>
                    </div>
                </div>
                
                <!-- Season Pass (Best Value) -->
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card featured">
                        <div class="pricing-badge">Best Value</div>
                        <div class="pricing-image-wrapper">
                            <img src="{{ asset('images/pexels-tom-jackson-1238161-2891910.jpg') }}" 
                                 alt="Season Pass" 
                                 class="pricing-image">
                        </div>
                        <h3 class="pricing-title">⭐ SEASON PASS</h3>
                        <p class="pricing-subtitle">$64 for 3 months</p>
                        <div class="pricing-price">$64</div>
                        <div class="pricing-period">for 3 months</div>
                        <div class="pricing-equivalent">$21.33/month</div>
                        <div class="pricing-savings">Save 26% vs monthly</div>
                        <p class="pricing-description">Perfect for golf season</p>
                        <a href="{{ route('admin.login') }}" class="btn btn-hero btn-primary-custom">
                            Get Started
                        </a>
                    </div>
                </div>
                
                <!-- Annual Coach (Best Overall) -->
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-badge" style="background: var(--primary-green); color: white;">Best Overall</div>
                        <div class="pricing-image-wrapper">
                            <img src="{{ asset('images/pexels-nathan-nedley-20160-92858.jpg') }}" 
                                 alt="Annual Coach" 
                                 class="pricing-image">
                        </div>
                        <h3 class="pricing-title">👑 ANNUAL COACH</h3>
                        <p class="pricing-subtitle">$249 per year</p>
                        <div class="pricing-price">$249</div>
                        <div class="pricing-period">per year</div>
                        <div class="pricing-equivalent">$20.75/month</div>
                        <div class="pricing-savings">Save 28% vs monthly</div>
                        <p class="pricing-description">Get daily coaching 365 days a year</p>
                        <a href="{{ route('admin.login') }}" class="btn btn-hero btn-outline-custom" style="color: var(--primary-green); border-color: var(--primary-green);">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Pricing Footer -->
            <div class="pricing-footer text-center mt-5">
                <p>
                    <i class="fas fa-check-circle"></i> 7-day free trial included
                </p>
                <p>
                    <i class="fas fa-check-circle"></i> Cancel anytime (no refunds after billing)
                </p>
                <p>
                    <i class="fas fa-check-circle"></i> One free trial per user
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Improve Your Game?</h2>
                <p class="cta-description">
                    Join thousands of golfers who are already using Auto Golf to elevate their performance. 
                    Start your journey today and see the difference!
                </p>
                <a href="{{ route('admin.login') }}" class="btn btn-hero btn-primary-custom">
                    <i class="fas fa-rocket me-2"></i>Start Free Trial
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">Auto Golf</div>
                <p>Your ultimate golf companion</p>
                <div class="footer-links">
                    <a href="#features"><i class="fas fa-info-circle me-1"></i>Features</a>
                    <a href="#pricing"><i class="fas fa-tag me-1"></i>Pricing</a>
                    <a href="#"><i class="fas fa-question-circle me-1"></i>Support</a>
                    <a href="#"><i class="fas fa-file-alt me-1"></i>Terms</a>
                    <a href="#"><i class="fas fa-shield-alt me-1"></i>Privacy</a>
                </div>
                <div class="footer-copyright">
                    <p>&copy; {{ date('Y') }} Auto Golf. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>
