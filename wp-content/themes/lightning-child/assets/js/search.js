// Lightning Theme Search Autocomplete
document.addEventListener('DOMContentLoaded', function () {
    // Lightning テーマの検索フィールドを探す（複数のセレクタを試行）
    const searchField = document.querySelector('input[type="search"]') ||
        document.querySelector('.search-field') ||
        document.querySelector('input[name="s"]');

    if (!searchField) {
        console.log('Search field not found');
        return;
    }

    // 検索候補を表示するコンテナを作成
    const searchForm = searchField.closest('form') || searchField.parentElement;
    let searchSuggestions = document.getElementById('searchSuggestions');

    if (!searchSuggestions) {
        searchSuggestions = document.createElement('div');
        searchSuggestions.id = 'searchSuggestions';
        searchSuggestions.className = 'search-suggestions';
        searchForm.style.position = 'relative';
        searchForm.appendChild(searchSuggestions);
    }

    let searchTimeout;

    searchField.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchSuggestions.classList.remove('active');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetchSearchSuggestions(query);
        }, 300);
    });

    // 外側クリックで候補を閉じる
    document.addEventListener('click', function (e) {
        if (!e.target.closest('form')) {
            searchSuggestions.classList.remove('active');
        }
    });

    function fetchSearchSuggestions(query) {
        // WordPress REST APIを使用
        fetch('/wp-json/wp/v2/posts?search=' + encodeURIComponent(query) + '&per_page=5')
            .then(response => response.json())
            .then(posts => {
                displaySuggestions(posts);
            })
            .catch(error => {
                console.error('Search error:', error);
            });
    }

    function displaySuggestions(posts) {
        if (posts.length === 0) {
            searchSuggestions.innerHTML = '<div class="search-suggestion-item">結果が見つかりませんでした</div>';
            searchSuggestions.classList.add('active');
            return;
        }

        const html = posts.map(post => `
      <a href="${post.link}" class="search-suggestion-item">
        <div class="suggestion-title">${post.title.rendered}</div>
        <div class="suggestion-category">記事</div>
      </a>
    `).join('');

        searchSuggestions.innerHTML = html;
        searchSuggestions.classList.add('active');
    }
});
