
/**
 * Hero Section Animation - 通常スクロール方式
 * ヒーローは通常のドキュメントフローでスクロール
 * パララックス効果は無効化
 */
document.addEventListener('DOMContentLoaded', function () {

    const heroBg = document.querySelector('.hero-bg');
    const heroSection = document.querySelector('.hero-section');
    const heroOverlay = document.querySelector('.hero-overlay');

    if (heroSection) {
        const heroHeight = heroSection.offsetHeight;

        function updateHeroOnScroll() {
            const scrollY = window.pageYOffset || window.scrollY;
            const scrollRatio = Math.min(scrollY / heroHeight, 1);

            // フェードアウト効果（軽微）
            const opacity = Math.max(0.5, 1 - scrollRatio * 0.5);
            heroSection.style.opacity = opacity;

            // パララックス効果を無効化（コメントアウト）
            // if (heroBg) {
            //     const parallax = scrollY * 0.4;
            //     heroBg.style.transform = 'translateY(' + parallax + 'px) scale(1.1)';
            // }

            // オーバーレイを暗くする（軽微）
            if (heroOverlay) {
                const overlayOpacity = Math.min(0.6, 0.3 + scrollRatio * 0.3);
                heroOverlay.style.background = 'rgba(14, 14, 30, ' + overlayOpacity + ')';
            }
        }

        // スクロールイベント（パッシブで軽量）
        let ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    updateHeroOnScroll();
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        // 初期状態
        updateHeroOnScroll();
    }

    // ========================================
    // particles.js 初期化
    // ========================================
    if (typeof particlesJS !== 'undefined' && document.getElementById('particles-js')) {
        particlesJS('particles-js', {
            "particles": {
                "number": { "value": 50, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": ["#ffffff", "#00C6FF", "#8E2DE2", "#667eea"] },
                "shape": { "type": ["circle", "triangle"], "stroke": { "width": 0 } },
                "opacity": { "value": 0.6, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0.1 } },
                "size": { "value": 4, "random": true },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
                "move": { "enable": true, "speed": 2, "direction": "none", "random": true, "out_mode": "out" }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": true, "mode": "push" }, "resize": true },
                "modes": { "grab": { "distance": 140, "line_linked": { "opacity": 1 } }, "push": { "particles_nb": 4 } }
            },
            "retina_detect": true
        });
    }
});
