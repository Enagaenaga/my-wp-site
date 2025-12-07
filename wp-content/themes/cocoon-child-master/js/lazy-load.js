/**
 * エナガブログ - 画像遅延読み込み (Lazy Loading)
 * パフォーマンス最適化のためのネイティブ+Intersection Observer実装
 */

document.addEventListener('DOMContentLoaded', function () {

    // ネイティブlazyloadをサポートしている場合はそれを使用
    if ('loading' in HTMLImageElement.prototype) {
        // ネイティブ遅延読み込み対応
        document.querySelectorAll('img:not([loading])').forEach(img => {
            if (!img.closest('.hero-section') && !img.closest('header')) {
                img.setAttribute('loading', 'lazy');
            }
        });
        console.log('Native lazy loading enabled');
    } else {
        // フォールバック: Intersection Observer
        initLazyLoadObserver();
    }

    // data-src属性を持つ画像のための遅延読み込み
    initDataSrcLazyLoad();

    /**
     * Intersection Observerを使用した遅延読み込み
     */
    function initLazyLoadObserver() {
        const lazyImages = document.querySelectorAll('img.lazy-load');

        if (lazyImages.length === 0) return;

        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;

                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                    }

                    img.classList.remove('lazy-load');
                    img.classList.add('lazy-loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        lazyImages.forEach(img => imageObserver.observe(img));
        console.log('Intersection Observer lazy loading initialized for', lazyImages.length, 'images');
    }

    /**
     * data-src属性を持つ画像の遅延読み込み
     */
    function initDataSrcLazyLoad() {
        const dataSrcImages = document.querySelectorAll('img[data-src]:not(.lazy-loaded)');

        if (dataSrcImages.length === 0) return;

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;

                    // プレースホルダーからアクチュアル画像に切り替え
                    img.src = img.dataset.src;
                    delete img.dataset.src;

                    img.classList.add('lazy-loaded');
                    obs.unobserve(img);
                }
            });
        }, {
            rootMargin: '100px 0px',
            threshold: 0.1
        });

        dataSrcImages.forEach(img => observer.observe(img));
    }

    /**
     * 動的に追加される画像にもlazyload属性を追加
     */
    const mutationObserver = new MutationObserver((mutations) => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    // 画像要素の場合
                    if (node.tagName === 'IMG' && !node.loading && !node.closest('.hero-section')) {
                        node.setAttribute('loading', 'lazy');
                    }
                    // 子孫の画像要素
                    node.querySelectorAll && node.querySelectorAll('img:not([loading])').forEach(img => {
                        if (!img.closest('.hero-section') && !img.closest('header')) {
                            img.setAttribute('loading', 'lazy');
                        }
                    });
                }
            });
        });
    });

    mutationObserver.observe(document.body, {
        childList: true,
        subtree: true
    });

    console.log('Lazy loading module initialized');
});
