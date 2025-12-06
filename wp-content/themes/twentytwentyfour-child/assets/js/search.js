// wp-content/themes/cocoon-child-master/js/search.js

document.addEventListener('DOMContentLoaded', function () {
    const searchField = document.querySelector('.wp-block-search__input');
    const searchSuggestions = document.getElementById('searchSuggestions');
    let searchTimeout;

    if (searchField && searchSuggestions) {
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
            if (!e.target.closest('.search-wrapper')) {
                searchSuggestions.classList.remove('active');
            }
        });
    }

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
