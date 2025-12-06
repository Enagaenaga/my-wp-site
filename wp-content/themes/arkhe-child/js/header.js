/**
 * Arkhe Child Theme - Header JavaScript
 * スティッキーヘッダーとスクロールエフェクト
 */

document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.l-header, .site-header, header');
    
    if (!header) return;
    
    let lastScroll = 0;
    
    // スクロール時のヘッダー変化
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // モバイルメニュートグル（Arkheのデフォルト機能を補完）
    const menuToggle = document.querySelector('.c-menuBtn, .menu-toggle');
    const navMenu = document.querySelector('.c-gnav, .nav-menu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
        
        // メニュー外クリックで閉じる
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.l-header__nav, .main-navigation')) {
                navMenu.classList.remove('active');
                if (menuToggle) menuToggle.classList.remove('active');
            }
        });
    }
});
