/**
 * エナガブログ - 検索機能強化
 * オートコンプリート候補表示とクイック検索
 */

document.addEventListener('DOMContentLoaded', function () {
    // 検索フィールドとサジェストボックスを取得
    const searchFields = document.querySelectorAll('.search-field, .search-box input[type="search"], #s, input[name="s"]');

    searchFields.forEach(searchField => {
        if (!searchField) return;

        // サジェストボックスを作成
        let searchSuggestions = searchField.parentElement.querySelector('.search-suggestions');
        if (!searchSuggestions) {
            searchSuggestions = document.createElement('div');
            searchSuggestions.className = 'search-suggestions';
            searchSuggestions.id = 'searchSuggestions-' + Date.now();

            // 検索フォームにposition: relativeを追加
            const form = searchField.closest('form');
            if (form) {
                form.style.position = 'relative';
                form.appendChild(searchSuggestions);
            } else {
                searchField.parentElement.style.position = 'relative';
                searchField.parentElement.appendChild(searchSuggestions);
            }
        }

        let searchTimeout;

        // 入力イベント
        searchField.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchSuggestions.classList.remove('active');
                return;
            }

            // デバウンス処理
            searchTimeout = setTimeout(() => {
                fetchSearchSuggestions(query, searchSuggestions);
            }, 300);
        });

        // フォーカスイベント
        searchField.addEventListener('focus', function () {
            if (this.value.trim().length >= 2) {
                fetchSearchSuggestions(this.value.trim(), searchSuggestions);
            }
        });

        // 外側クリックで候補を閉じる
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-wrapper') &&
                !e.target.closest('.search-form') &&
                !e.target.closest('.search-box')) {
                searchSuggestions.classList.remove('active');
            }
        });
    });

    /**
     * 検索候補を取得
     */
    function fetchSearchSuggestions(query, container) {
        // WordPress REST APIを使用
        const apiUrl = window.location.origin + '/wp-json/wp/v2/posts?search=' + encodeURIComponent(query) + '&per_page=5&_fields=id,title,link,date';

        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(posts => {
                displaySuggestions(posts, container);
            })
            .catch(error => {
                console.error('Search error:', error);
                container.classList.remove('active');
            });
    }

    /**
     * 検索候補を表示
     */
    function displaySuggestions(posts, container) {
        if (posts.length === 0) {
            container.innerHTML = '<div class="search-suggestion-item search-no-result">該当する記事が見つかりませんでした</div>';
            container.classList.add('active');
            return;
        }

        const html = posts.map(post => {
            const date = new Date(post.date).toLocaleDateString('ja-JP');
            return `
                <a href="${post.link}" class="search-suggestion-item">
                    <div class="suggestion-title">${post.title.rendered}</div>
                    <div class="suggestion-meta">
                        <span class="suggestion-date"><i class="far fa-calendar-alt"></i> ${date}</span>
                    </div>
                </a>
            `;
        }).join('');

        container.innerHTML = html;
        container.classList.add('active');
    }

    // キーボードナビゲーション
    document.addEventListener('keydown', function (e) {
        const activeSuggestions = document.querySelector('.search-suggestions.active');
        if (!activeSuggestions) return;

        const items = activeSuggestions.querySelectorAll('.search-suggestion-item:not(.search-no-result)');
        if (items.length === 0) return;

        const focused = activeSuggestions.querySelector('.search-suggestion-item.focused');
        let index = Array.from(items).indexOf(focused);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (focused) focused.classList.remove('focused');
            index = (index + 1) % items.length;
            items[index].classList.add('focused');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (focused) focused.classList.remove('focused');
            index = (index - 1 + items.length) % items.length;
            items[index].classList.add('focused');
        } else if (e.key === 'Enter' && focused) {
            e.preventDefault();
            window.location.href = focused.href;
        } else if (e.key === 'Escape') {
            activeSuggestions.classList.remove('active');
        }
    });

    console.log('Search enhancement script initialized');
});
