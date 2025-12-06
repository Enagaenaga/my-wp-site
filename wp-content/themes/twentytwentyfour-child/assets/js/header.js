// wp-content/themes/cocoon-child-master/js/header.js
// Cocoon親テーマ対応版（サブメニュー対応含む）

document.addEventListener('DOMContentLoaded', function() {
  // Cocoonのヘッダー要素を取得
  const header = document.querySelector('.header, #header');
  const navi = document.querySelector('.navi, #navi');
  const naviIn = document.querySelector('.navi-in, #navi-in');
  
  // スクロール時のヘッダー変化（Cocoon対応）
  let lastScroll = 0;
  window.addEventListener('scroll', function() {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
      if (header) header.classList.add('scrolled');
      if (navi) navi.classList.add('scrolled');
    } else {
      if (header) header.classList.remove('scrolled');
      if (navi) navi.classList.remove('scrolled');
    }
    
    lastScroll = currentScroll;
  });
  
  // モバイルでのサブメニュー対応（768px以下）
  if (window.innerWidth <= 768) {
    const menuItemsWithChildren = document.querySelectorAll('.navi-in > ul li.menu-item-has-children, #navi-in > ul li.menu-item-has-children');
    
    menuItemsWithChildren.forEach(function(item) {
      // メニュー項目をタップしたときの処理
      const link = item.querySelector('a');
      if (link) {
        link.addEventListener('click', function(e) {
          const subMenu = item.querySelector('.sub-menu');
          if (subMenu) {
            e.preventDefault(); // 最初のタップでリンクに飛ばない
            
            // 既に開いている場合は閉じる
            if (subMenu.style.display === 'block') {
              subMenu.style.display = 'none';
            } else {
              // 他のサブメニューを閉じる
              document.querySelectorAll('.sub-menu').forEach(function(menu) {
                menu.style.display = 'none';
              });
              // このサブメニューを開く
              subMenu.style.display = 'block';
              subMenu.style.opacity = '1';
              subMenu.style.transform = 'translateY(0)';
            }
          }
        });
      }
    });
  }
  
  console.log('Enaga Blog Header JS - Cocoon対応版（サブメニュー対応）が読み込まれました');
  console.log('Header:', header);
  console.log('Navi:', navi);
  console.log('NaviIn:', naviIn);
});
