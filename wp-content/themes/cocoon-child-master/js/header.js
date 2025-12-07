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
});
