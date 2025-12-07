/**
 * エナガブログ - ヘッダー機能
 * スクロール時のヘッダー変化とモバイルメニュー
 */

document.addEventListener('DOMContentLoaded', function () {
    // ヘッダー要素を取得（Cocoonテーマ対応）
    const header = document.querySelector('#header') || document.querySelector('.header') || document.querySelector('.header-container');
    const menuToggle = document.querySelector('.menu-toggle') || document.querySelector('.mobile-menu-toggle') || document.querySelector('.navi-menu-toggle');
    const navMenu = document.querySelector('.nav-menu') || document.querySelector('#navi') || document.querySelector('.navi-in');

    // ヘッダーが見つからない場合は終了
    if (!header) {
        console.log('Header element not found');
        return;
    }

    // ==============================================
    // スクロール時のヘッダー変化 & ナビ固定
    // ==============================================
    let lastScroll = 0;
    let ticking = false;
    const scrollThreshold = 100;

    // ナビゲーション要素
    const navi = document.getElementById('navi');
    let naviOriginalTop = 0;
    let naviHeight = 0;
    let naviPlaceholder = null;

    // ナビの元の位置を記録
    if (navi) {
        // 少し遅延してから位置を取得（DOM移動完了後）
        setTimeout(function () {
            naviOriginalTop = navi.getBoundingClientRect().top + window.pageYOffset;
            naviHeight = navi.offsetHeight;

            // プレースホルダーを作成（固定時のスペース確保用）
            naviPlaceholder = document.createElement('div');
            naviPlaceholder.className = 'navi-placeholder';
            naviPlaceholder.style.height = naviHeight + 'px';
            naviPlaceholder.style.display = 'none';
            navi.parentNode.insertBefore(naviPlaceholder, navi.nextSibling);
        }, 100);
    }

    function updateHeader() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll > scrollThreshold) {
            header.classList.add('scrolled');
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('scrolled');
            header.classList.remove('header-scrolled');
        }

        // ナビの固定切り替え
        if (navi && naviOriginalTop > 0) {
            if (currentScroll >= naviOriginalTop) {
                // スクロールがナビの位置を超えた → fixedに
                if (!navi.classList.contains('navi-fixed')) {
                    navi.classList.add('navi-fixed');
                    if (naviPlaceholder) naviPlaceholder.style.display = 'block';
                }
            } else {
                // ナビの位置より上 → 通常に戻す
                if (navi.classList.contains('navi-fixed')) {
                    navi.classList.remove('navi-fixed');
                    if (naviPlaceholder) naviPlaceholder.style.display = 'none';
                }
            }
        }

        header.classList.remove('header-hidden');
        lastScroll = currentScroll <= 0 ? 0 : currentScroll;
        ticking = false;
    }

    // パフォーマンス最適化: requestAnimationFrameを使用
    function onScroll() {
        if (!ticking) {
            window.requestAnimationFrame(updateHeader);
            ticking = true;
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });

    // ページ読み込み時にも状態をチェック
    updateHeader();

    // ==============================================
    // モバイルメニュートグル
    // ==============================================
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');

            // アクセシビリティ: aria-expanded属性を更新
            const isExpanded = navMenu.classList.contains('active');
            menuToggle.setAttribute('aria-expanded', isExpanded);

            // ボディのスクロールを制御
            if (isExpanded) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });

        // メニュー外クリックで閉じる
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.main-navigation') &&
                !event.target.closest('#navi') &&
                !event.target.closest('.navi-in') &&
                !event.target.closest('.menu-toggle')) {
                navMenu.classList.remove('active');
                if (menuToggle) {
                    menuToggle.classList.remove('active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
                document.body.style.overflow = '';
            }
        });

        // ESCキーで閉じる
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
    }

    // ==============================================
    // スムーススクロール（アンカーリンク）
    // ==============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                const headerHeight = header.offsetHeight || 0;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    console.log('Header script initialized');

    // ==============================================
    // アフェリエイト広告通知の背景色を統一
    // ==============================================
    function updateAffiliateNoticeStyle() {
        // .text-mobile要素を取得
        const textMobileElements = document.querySelectorAll('.text-mobile');
        const widgetMobileTextElements = document.querySelectorAll('.widget_mobile_text');
        const allTargetElements = document.querySelectorAll('#mobile_text-2, .widget_mobile_text, aside.widget_mobile_text');

        const isDarkMode = document.documentElement.classList.contains('dark-mode') ||
            document.body.classList.contains('dark-mode');

        const bgColor = isDarkMode ? '#1a1a25' : '#f5f0e8';
        const textColor = isDarkMode ? '#a0a0b0' : '#5a5045';

        // .text-mobile要素にスタイルを適用
        textMobileElements.forEach(function (el) {
            el.style.setProperty('background-color', bgColor, 'important');
            el.style.setProperty('background', bgColor, 'important');
            el.style.setProperty('color', textColor, 'important');
            el.style.setProperty('border', 'none', 'important');
        });

        // .widget_mobile_text要素にスタイルを適用
        widgetMobileTextElements.forEach(function (el) {
            el.style.setProperty('background-color', bgColor, 'important');
            el.style.setProperty('background', bgColor, 'important');
            el.style.setProperty('color', textColor, 'important');
            el.style.setProperty('border', 'none', 'important');
        });

        // その他の要素にもスタイルを適用
        allTargetElements.forEach(function (el) {
            el.style.setProperty('background-color', bgColor, 'important');
            el.style.setProperty('background', bgColor, 'important');
            el.style.setProperty('color', textColor, 'important');
            el.style.setProperty('border', 'none', 'important');
        });

        console.log('Affiliate notice style updated:', textMobileElements.length, 'elements found');
    }

    // 初回実行
    updateAffiliateNoticeStyle();

    // 少し遅延してから再実行（動的読み込み対応）
    setTimeout(updateAffiliateNoticeStyle, 500);
    setTimeout(updateAffiliateNoticeStyle, 1000);
    setTimeout(updateAffiliateNoticeStyle, 2000);

    // ダークモード切り替え時に再実行
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.attributeName === 'class') {
                updateAffiliateNoticeStyle();
            }
        });
    });

    observer.observe(document.documentElement, { attributes: true });
    observer.observe(document.body, { attributes: true });
});
