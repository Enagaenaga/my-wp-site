/**
 * トップへ戻るボタン（Back to Top Button）
 * 
 * このスクリプトは以下の機能を提供します：
 * - ページを一定量スクロールするとボタンが表示される
 * - ボタンをクリックするとページ先頭にスムーズスクロール
 * - フェードイン・フェードアウトアニメーション
 * 
 * カスタマイズポイント：
 * - SCROLL_THRESHOLD: ボタンが表示されるスクロール量（ピクセル）
 * - aria-label: スクリーンリーダー用のラベルテキスト
 */

(function () {
    'use strict';

    // ======================================
    // 設定値（カスタマイズ箇所）
    // ======================================

    /**
     * ボタンが表示されるスクロール量（ピクセル）
     * 400より大きい値にすると、より深くスクロールしないと表示されない
     * 400より小さい値にすると、少しスクロールしただけで表示される
     */
    const SCROLL_THRESHOLD = 400;

    // ======================================
    // ボタン要素の生成
    // ======================================

    /**
     * ボタンのHTML要素を生成してbody末尾に追加
     * テンプレートファイルを編集せずにボタンを追加できる
     */
    function createBackToTopButton() {
        // ボタン要素を作成
        const button = document.createElement('button');

        // IDを設定（CSSでスタイリングするため）
        button.id = 'cocoon-back-to-top-btn';

        // クラスを設定（表示/非表示の制御用）
        button.className = 'cocoon-back-to-top';

        // タイプを設定（フォーム送信を防ぐ）
        button.type = 'button';

        // アクセシビリティ: スクリーンリーダー用のラベル
        button.setAttribute('aria-label', 'ページの先頭に戻る');

        // ボタンの中身（矢印アイコン）
        // SVGを使用することで、カラーやサイズをCSSで簡単に変更可能
        button.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="cocoon-back-to-top-icon" aria-hidden="true">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
            <span class="visually-hidden">トップへ戻る</span>
        `;

        // body末尾に追加
        document.body.appendChild(button);

        return button;
    }

    // ======================================
    // スクロール監視
    // ======================================

    /**
     * スクロール位置を監視して、ボタンの表示/非表示を切り替え
     * is-show クラスの付け外しでCSSアニメーションを実行
     */
    function handleScroll(button) {
        // 現在のスクロール位置を取得
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // しきい値を超えたかどうかで表示/非表示を切り替え
        if (scrollTop > SCROLL_THRESHOLD) {
            // しきい値を超えた：ボタンを表示
            if (!button.classList.contains('is-show')) {
                button.classList.add('is-show');
            }
        } else {
            // しきい値以下：ボタンを非表示
            if (button.classList.contains('is-show')) {
                button.classList.remove('is-show');
            }
        }
    }

    /**
     * スクロールイベントの最適化（スロットリング）
     * パフォーマンス向上のため、スクロールイベントの発火頻度を制限
     */
    function throttle(func, limit) {
        let inThrottle;
        return function () {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function () {
                    inThrottle = false;
                }, limit);
            }
        };
    }

    // ======================================
    // クリックイベント（スムーズスクロール）
    // ======================================

    /**
     * スムーズスクロールのアニメーション時間（ミリ秒）
     * 値を大きくするとゆっくり、小さくすると速くなる
     */
    const SCROLL_DURATION = 600;

    /**
     * ボタンクリック時にページ先頭へスムーズスクロール
     * requestAnimationFrameを使用して確実にスムーズにスクロール
     */
    function scrollToTop(event) {
        event.preventDefault();

        // 現在のスクロール位置
        const startPosition = window.pageYOffset || document.documentElement.scrollTop;

        // すでにトップにいる場合は何もしない
        if (startPosition === 0) return;

        const startTime = performance.now();

        /**
         * イージング関数（ease-in-out）
         * より自然で滑らかなアニメーションを実現
         */
        function easeInOutCubic(t) {
            return t < 0.5
                ? 4 * t * t * t
                : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        /**
         * アニメーションフレームごとにスクロール位置を更新
         */
        function animateScroll(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / SCROLL_DURATION, 1);

            // イージング関数を適用
            const easedProgress = easeInOutCubic(progress);

            // 新しいスクロール位置を計算
            const newPosition = startPosition * (1 - easedProgress);

            // スクロール位置を更新
            window.scrollTo(0, newPosition);

            // アニメーション継続
            if (progress < 1) {
                requestAnimationFrame(animateScroll);
            }
        }

        // アニメーション開始
        requestAnimationFrame(animateScroll);
    }

    // ======================================
    // 初期化
    // ======================================

    /**
     * DOM読み込み完了後に初期化を実行
     */
    function init() {
        // 1. ボタン要素を生成
        const button = createBackToTopButton();

        // 2. スクロールイベントを監視（スロットリング付き）
        //    16msは約60fpsに相当
        const throttledScroll = throttle(function () {
            handleScroll(button);
        }, 16);

        window.addEventListener('scroll', throttledScroll, { passive: true });

        // 3. 初期状態をチェック（リロード時などにスクロール位置が保持されている場合）
        handleScroll(button);

        // 4. クリックイベントを設定
        button.addEventListener('click', scrollToTop);

        // 5. キーボード操作対応（Enterキー）
        button.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                scrollToTop(event);
            }
        });
    }

    // DOMContentLoadedイベントで初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // すでにDOMが読み込まれている場合（遅延読み込み時など）
        init();
    }

})();
