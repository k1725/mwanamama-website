<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mwanamama Enterprises Limited | Hire Purchase, Bajaj Motorcycles & Event Services in Kenya</title>
    <meta name="description" content="Mwanamama Enterprises Limited - a trusted company in Kenya offering flexible hire purchase, Bajaj motorcycles & spare parts, and professional event services (tents, chairs, PA systems). Based in Hola, Tana River County.">
    <meta name="keywords" content="hire purchase Kenya, Bajaj motorcycles Kenya, event organizers Hola, MWANAMAMA Enterprises Limited">
    
    <link rel="icon" href="images/mwanamama-logo.png" type="image/png">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        /* Logo Skeleton Loader */
        #logo-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.5s ease;
        }
        
        #logo-loader.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        
        .logo-skeleton {
            text-align: center;
            animation: logoFadeIn 0.6s ease-out;
            background: transparent;
        }
        
        .logo-skeleton img {
            width: 150px;
            height: 150px;
            object-fit: contain;
            animation: logoPulse 1.5s ease-in-out infinite;
            background: transparent;
            display: block;
        }
        
        @keyframes logoFadeIn {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes logoPulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            #logo-loader {
                background: #000000;
            }
        }
        
        /* Prevent content flash */
        body.loading {
            overflow: hidden;
        }
        
        body.loading #nav-placeholder,
        body.loading #content-placeholder,
        body.loading #footer-placeholder {
            opacity: 0;
        }
    </style>
</head>
<body class="loading">
    
    <!-- Logo Skeleton Loader -->
    <div id="logo-loader">
        <div class="logo-skeleton">
            <img src="images/mwanamama-logo.png" alt="MWANAMAMA Enterprises Limited" onerror="this.style.display='none'">
        </div>
    </div>
    
    <div id="nav-placeholder"></div>

    <main id="content-placeholder">
    </main>

    <div id="footer-placeholder"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="js/api-handler.js"></script>
    
    <script src="js/script.js"></script>
    
    <script>
        // Logo Skeleton Loader Management
        const logoLoader = document.getElementById('logo-loader');
        let loaderStartTime = Date.now();
        
        // Hide loader after minimum 1 second
        window.addEventListener('load', function() {
            const elapsedTime = Date.now() - loaderStartTime;
            const remainingTime = Math.max(1000 - elapsedTime, 0);
            
            setTimeout(() => {
                logoLoader.classList.add('fade-out');
                document.body.classList.remove('loading');
                
                // Remove loader from DOM after transition
                setTimeout(() => {
                    logoLoader.style.display = 'none';
                }, 500);
            }, remainingTime);
        });
        
        // Show loader on page navigation
        window.addEventListener('beforeunload', function() {
            logoLoader.style.display = 'flex';
            logoLoader.classList.remove('fade-out');
            document.body.classList.add('loading');
            loaderStartTime = Date.now();
        });
        
        // Handle internal navigation
        document.addEventListener('DOMContentLoaded', function() {
            const internalLinks = document.querySelectorAll('a[href^="/"], a[href^="./"], a[href^="../"]');
            
            internalLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && !href.startsWith('#')) {
                        logoLoader.style.display = 'flex';
                        logoLoader.classList.remove('fade-out');
                        document.body.classList.add('loading');
                        loaderStartTime = Date.now();
                    }
                });
            });
        });
        
        // Functions to show/hide loader programmatically
        function showLogoLoader() {
            logoLoader.style.display = 'flex';
            logoLoader.classList.remove('fade-out');
            document.body.classList.add('loading');
            loaderStartTime = Date.now();
        }
        
        function hideLogoLoader(immediate = false) {
            if (immediate) {
                logoLoader.classList.add('fade-out');
                document.body.classList.remove('loading');
                setTimeout(() => {
                    logoLoader.style.display = 'none';
                }, 500);
            } else {
                const elapsedTime = Date.now() - loaderStartTime;
                const remainingTime = Math.max(1000 - elapsedTime, 0);
                
                setTimeout(() => {
                    logoLoader.classList.add('fade-out');
                    document.body.classList.remove('loading');
                    setTimeout(() => {
                        logoLoader.style.display = 'none';
                    }, 500);
                }, remainingTime);
            }
        }
        
        // Make functions globally available
        window.showLogoLoader = showLogoLoader;
        window.hideLogoLoader = hideLogoLoader;
    </script>
</body>
</html>