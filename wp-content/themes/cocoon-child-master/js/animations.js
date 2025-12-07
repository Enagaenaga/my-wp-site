/**
 * エナガブログ - スクロールアニメーション
 * Intersection Observer APIを使用したスムーズなアニメーション効果
 */

document.addEventListener('DOMContentLoaded', function () {

    // アニメーション対象要素のセレクタ
    const animationSelectors = [
        '.entry-card-wrap',
        '.entry-card',
        '.related-entry-card',
        '.sidebar-widget',
        '.enaga-sidebar-widget',
        '.widget',
        '.article h2',
        '.article h3',
        '.entry-content > p',
        '.entry-content > ul',
        '.entry-content > ol',
        '.entry-content > blockquote',
        '.enaga-footer-section .footer-column',
        '.breadcrumb'
    ];

    // アニメーションクラスを追加
    const addAnimationClasses = () => {
        animationSelectors.forEach(selector => {
            document.querySelectorAll(selector).forEach((el, index) => {
                if (!el.classList.contains('animate-on-scroll')) {
                    el.classList.add('animate-on-scroll');
                    // 遅延を設定（連続する要素に対して段階的なアニメーション）
                    el.style.setProperty('--animation-delay', `${index * 0.1}s`);
                }
            });
        });
    };

    // Intersection Observer の設定
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.1
    };

    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // 要素が画面内に入った時
                entry.target.classList.add('animate-visible');
                // 一度アニメーションしたら監視を停止
                animationObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // 初期設定
    addAnimationClasses();

    // アニメーション対象要素を監視開始
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        animationObserver.observe(el);
    });

    // 動的に追加される要素にも対応（MutationObserver）
    const mutationObserver = new MutationObserver((mutations) => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) { // Element node
                    animationSelectors.forEach(selector => {
                        if (node.matches && node.matches(selector)) {
                            node.classList.add('animate-on-scroll');
                            animationObserver.observe(node);
                        }
                        node.querySelectorAll && node.querySelectorAll(selector).forEach(el => {
                            if (!el.classList.contains('animate-on-scroll')) {
                                el.classList.add('animate-on-scroll');
                                animationObserver.observe(el);
                            }
                        });
                    });
                }
            });
        });
    });

    mutationObserver.observe(document.body, {
        childList: true,
        subtree: true
    });

    console.log('Scroll animations initialized');
});
