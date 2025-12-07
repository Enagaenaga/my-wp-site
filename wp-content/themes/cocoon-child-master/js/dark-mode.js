/**
 * エナガブログ - ダークモード
 * CSS変数を使用したテーマ切り替え機能
 */

document.addEventListener('DOMContentLoaded', function () {

    const DARK_MODE_KEY = 'enaga-dark-mode';
    const DARK_CLASS = 'dark-mode';

    // トグルボタンのHTMLを生成
    function getToggleHTML() {
        return `
            <span class="toggle-icon light-icon"><i class="fas fa-sun"></i></span>
            <span class="toggle-icon dark-icon"><i class="fas fa-moon"></i></span>
        `;
    }

    // フローティングダークモードトグルボタンを作成
    function createFloatingToggle() {
        const toggle = document.createElement('button');
        toggle.id = 'dark-mode-toggle';
        toggle.className = 'dark-mode-toggle dark-mode-toggle-floating';
        toggle.setAttribute('aria-label', 'ダークモード切り替え');
        toggle.innerHTML = getToggleHTML();
        document.body.appendChild(toggle);
        return toggle;
    }

    // ヒーローセクション内のトグルボタンを作成
    function createHeroToggle() {
        const heroSection = document.querySelector('.hero-section');
        if (!heroSection) return null;

        const toggle = document.createElement('button');
        toggle.id = 'dark-mode-toggle-hero';
        toggle.className = 'dark-mode-toggle dark-mode-toggle-hero';
        toggle.setAttribute('aria-label', 'ダークモード切り替え');
        toggle.innerHTML = getToggleHTML();
        heroSection.appendChild(toggle);
        return toggle;
    }

    // ダークモードの状態を取得
    function getDarkModePreference() {
        const saved = localStorage.getItem(DARK_MODE_KEY);
        if (saved !== null) {
            return saved === 'true';
        }
        // システム設定をチェック
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    // ダークモードを適用
    function applyDarkMode(isDark) {
        if (isDark) {
            document.documentElement.classList.add(DARK_CLASS);
            document.body.classList.add(DARK_CLASS);
        } else {
            document.documentElement.classList.remove(DARK_CLASS);
            document.body.classList.remove(DARK_CLASS);
        }
        localStorage.setItem(DARK_MODE_KEY, isDark);
    }

    // ダークモードをトグル
    function toggleDarkMode() {
        const isDark = document.documentElement.classList.contains(DARK_CLASS);
        applyDarkMode(!isDark);
    }

    // 初期化
    const floatingToggle = createFloatingToggle();
    const heroToggle = createHeroToggle();
    const initialDarkMode = getDarkModePreference();
    applyDarkMode(initialDarkMode);

    // フローティングトグルのクリックイベント
    floatingToggle.addEventListener('click', toggleDarkMode);

    // ヒーローセクション内トグルのクリックイベント
    if (heroToggle) {
        heroToggle.addEventListener('click', toggleDarkMode);
    }

    // システム設定の変更を監視
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        // ユーザーが明示的に設定していない場合のみ自動切り替え
        if (localStorage.getItem(DARK_MODE_KEY) === null) {
            applyDarkMode(e.matches);
        }
    });

    console.log('Dark mode initialized with hero toggle');
});
