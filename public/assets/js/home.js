// HOME PAGE SPECIFIC JAVASCRIPT
(function() {
    'use strict';
    
    // Simple Counter Animation dengan speed 120
    function startCounterAnimation() {
        const counters = document.querySelectorAll('.counter');
        const speed = 120; // Speed sesuai permintaan Anda
        
        counters.forEach(counter => {
            // Reset ke 0 sebelum memulai
            if (counter.getAttribute('data-plus') === 'true') {
                counter.textContent = '0+';
            } else {
                counter.textContent = '0%';
            }
            
            const target = parseInt(counter.getAttribute('data-target'));
            const hasPlus = counter.getAttribute('data-plus') === 'true';
            const increment = target / speed;
            let current = 0;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    if (hasPlus) {
                        counter.textContent = Math.floor(current) + '+';
                    } else {
                        counter.textContent = Math.floor(current) + '%';
                    }
                    setTimeout(updateCounter, 25); // Timing optimal
                } else {
                    // Set nilai akhir
                    if (hasPlus) {
                        counter.textContent = target + '+';
                    } else {
                        counter.textContent = target + '%';
                    }
                }
            };
            
            updateCounter();
        });
    }
    
    // Testimonial autoplay dengan interval 5 detik
    function initTestimonialCarousel() {
        const testimonialCarousel = document.getElementById('singleTestimonialCarousel');
        if (testimonialCarousel && typeof bootstrap !== 'undefined') {
            const carousel = new bootstrap.Carousel(testimonialCarousel, {
                interval: 5000, // 5 detik
                wrap: true,
                pause: 'hover',
                touch: true
            });
            
            // Auto advance jika user tidak berinteraksi
            let userInteracted = false;
            
            testimonialCarousel.addEventListener('slid.bs.carousel', function() {
                userInteracted = false;
            });
            
            testimonialCarousel.addEventListener('mouseenter', function() {
                carousel.pause();
            });
            
            testimonialCarousel.addEventListener('mouseleave', function() {
                if (!userInteracted) {
                    carousel.cycle();
                }
            });
            
            // Tandai jika user berinteraksi
            document.querySelectorAll('[data-bs-slide]').forEach(button => {
                button.addEventListener('click', function() {
                    userInteracted = true;
                });
            });
            
            console.log('Testimonial carousel initialized with 5-second interval');
        }
    }
    
    // Video Background Handling - OPTIMIZED untuk load cepat
    function initVideoBackground() {
        const video = document.getElementById('heroVideo');
        if (video) {
            // Optimasi: Preload dan set atribut lebih awal
            video.preload = 'auto';
            video.setAttribute('preload', 'auto');
            video.setAttribute('playsinline', '');
            video.setAttribute('muted', '');
            video.setAttribute('autoplay', '');
            video.setAttribute('loop', '');
            
            // Set poster atau placeholder sementara
            video.style.backgroundColor = '#000';
            
            // Event untuk handle video readiness
            video.addEventListener('loadeddata', function() {
                console.log('Video loaded successfully');
                // Hapus placeholder setelah video siap
                video.style.backgroundColor = 'transparent';
                // Hapus loading indicator
                const loadingIndicator = document.querySelector('.video-loading');
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
            });
            
            // Coba play video dengan strategi lebih baik
            const playVideo = () => {
                const playPromise = video.play();
                
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.log('Autoplay prevented:', error.name);
                        // Fallback: Play on user interaction
                        const playOnInteraction = () => {
                            video.play();
                            document.removeEventListener('click', playOnInteraction);
                            document.removeEventListener('scroll', playOnInteraction);
                            document.removeEventListener('touchstart', playOnInteraction);
                        };
                        
                        document.addEventListener('click', playOnInteraction);
                        document.addEventListener('scroll', playOnInteraction);
                        document.addEventListener('touchstart', playOnInteraction);
                    });
                }
            };
            
            // Coba play saat metadata loaded
            video.addEventListener('loadedmetadata', playVideo);
            
            // Juga coba play saat sudah ada data yang cukup
            video.addEventListener('canplay', playVideo);
            
            // Play langsung jika sudah cukup data
            if (video.readyState >= 2) { // HAVE_CURRENT_DATA
                playVideo();
            }
            
            // Tambahkan event untuk progress loading
            video.addEventListener('progress', function() {
                if (video.buffered.length > 0) {
                    const bufferedEnd = video.buffered.end(video.buffered.length - 1);
                    const duration = video.duration;
                    if (duration > 0) {
                        const percent = (bufferedEnd / duration) * 100;
                        console.log(`Video buffered: ${percent.toFixed(1)}%`);
                        
                        // Jika sudah buffered cukup, coba play
                        if (percent > 10 && video.paused) {
                            playVideo();
                        }
                    }
                }
            });
            
            // Handle video errors
            video.addEventListener('error', function(e) {
                console.error('Video error:', e);
                // Hapus loading indicator
                const loadingIndicator = document.querySelector('.video-loading');
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
                
                // Fallback to image background immediately
                const heroSection = document.querySelector('.hero-section');
                if (heroSection) {
                    heroSection.style.background = "linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), url('https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1600&q=80')";
                    heroSection.style.backgroundSize = 'cover';
                    heroSection.style.backgroundPosition = 'center';
                    heroSection.style.backgroundAttachment = 'fixed';
                    heroSection.style.backgroundRepeat = 'no-repeat';
                }
            });
        }
    }
    
    // Initialize counter hanya ketika section stats terlihat
    function initCounterObserver() {
        const statsSection = document.querySelector('#services .stats-card');
        
        if (!statsSection) return;
        
        // Reset counter ke 0 saat page load
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            if (counter.getAttribute('data-plus') === 'true') {
                counter.textContent = '0+';
            } else {
                counter.textContent = '0%';
            }
        });
        
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        console.log('Stats section is visible, starting counter...');
                        // Tunggu sedikit sebelum memulai counter
                        setTimeout(() => {
                            startCounterAnimation();
                        }, 300);
                        observer.unobserve(entry.target); // Hanya jalankan sekali
                    }
                });
            }, {
                threshold: 0.5, // Trigger ketika 50% section terlihat
                rootMargin: '0px 0px -100px 0px' // Trigger sedikit sebelum masuk viewport
            });
            
            observer.observe(statsSection);
        } else {
            // Fallback untuk browser lama
            setTimeout(startCounterAnimation, 1000);
        }
    }
    
    // Animate service cards on scroll
    function initServiceCardsAnimation() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                        observer.unobserve(entry.target); // Hanya animasi sekali
                    }
                });
            }, { 
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px' // Trigger sedikit lebih awal
            });
            
            document.querySelectorAll('.service-card').forEach(card => {
                observer.observe(card);
            });
        } else {
            // Fallback: langsung tambahkan class
            document.querySelectorAll('.service-card').forEach(card => {
                card.classList.add('animate__animated', 'animate__fadeInUp');
            });
        }
    }
    
    // Logo error handler
    function initLogoErrorHandler() {
        window.handleLogoError = function(img, initials) {
            const wrapper = img.parentElement;
            const fallbackHTML = `
                <div class="fallback-logo d-flex align-items-center justify-content-center bg-gray-light rounded"
                     style="width: 120px; height: 80px; border: 2px dashed #ddd;">
                    <span class="fw-bold fs-4 text-orange">${initials}</span>
                </div>
            `;
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = fallbackHTML;
            wrapper.replaceChild(tempDiv.firstChild, img);
        };
        
        // Preload logo images untuk performance
        const logoImages = document.querySelectorAll('.partner-logo');
        logoImages.forEach(img => {
            const tempImage = new Image();
            tempImage.src = img.src;
            tempImage.onload = function() {
                // Logo berhasil di-load
            };
            tempImage.onerror = function() {
                // Handle error jika logo gagal load
                const initials = img.getAttribute('alt')?.substring(0, 2) || 'CD';
                handleLogoError(img, initials);
            };
        });
    }
    
    // Initialize semua fungsi
    function initHomePage() {
        console.log('Initializing home page components...');
        
        // Initialize video background ASAP (prioritas tinggi)
        initVideoBackground();
        
        // Initialize counter observer
        initCounterObserver();
        
        // Initialize logo error handler
        initLogoErrorHandler();
        
        // Initialize service cards animation
        initServiceCardsAnimation();
        
        // Cek apakah Bootstrap tersedia
        if (typeof bootstrap !== 'undefined') {
            console.log('Bootstrap loaded successfully');
        } else {
            console.warn('Bootstrap not loaded');
        }
    }
    
    // Initialize saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHomePage);
    } else {
        initHomePage();
    }
    
    // Hanya testimonial carousel yang perlu tunggu full load
    window.addEventListener('load', function() {
        console.log('Page fully loaded, initializing testimonial carousel...');
        
        // Initialize testimonial carousel
        initTestimonialCarousel();
        
        // Preload additional resources jika diperlukan
        const video = document.getElementById('heroVideo');
        if (video && video.readyState < 3) {
            console.log('Video still loading, triggering additional buffering...');
        }
    });
    
    // Performance monitoring (opsional, untuk debugging)
    if (typeof PerformanceObserver !== 'undefined') {
        const perfObserver = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                console.log(`${entry.name}: ${entry.duration}ms`);
            }
        });
        
        perfObserver.observe({ entryTypes: ["measure", "resource"] });
    }
    
    // Log video ready state untuk debugging
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('heroVideo');
        if (video) {
            console.log(`Video readyState on DOMContentLoaded: ${video.readyState}`);
            
            // Log ready state changes
            const readyStateMap = [
                'HAVE_NOTHING',
                'HAVE_METADATA',
                'HAVE_CURRENT_DATA',
                'HAVE_FUTURE_DATA',
                'HAVE_ENOUGH_DATA'
            ];
            
            video.addEventListener('readystatechange', function() {
                console.log(`Video readyState changed to: ${readyStateMap[this.readyState] || this.readyState}`);
            });
        }
    });
    
    // Export fungsi untuk penggunaan eksternal jika diperlukan
    window.HomePage = {
        startCounterAnimation,
        initTestimonialCarousel,
        initVideoBackground
    };
    
})();