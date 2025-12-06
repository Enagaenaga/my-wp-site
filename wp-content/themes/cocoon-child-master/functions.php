<?php //子テーマ用関数
if ( !defined( 'ABSPATH' ) ) exit;

//子テーマ用のビジュアルエディタースタイルを適用
add_editor_style();

//以下に子テーマ用の関数を書く

// ヒーローセクション用スクリプト読み込み
function enqueue_hero_scripts() {
  if ( is_front_page() || is_home() ) {
    // particles.js (CDN)
    wp_enqueue_script( 'particles-js', 'https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js', array(), '2.0.0', true );
    
    // 自作アニメーションスクリプト
    wp_enqueue_script( 'hero-js', get_stylesheet_directory_uri() . '/js/hero.js', array('particles-js'), '1.0.3', true );
  }
}
add_action( 'wp_enqueue_scripts', 'enqueue_hero_scripts' );

// ヒーローセクション表示（安全な直接出力版）
function add_hero_section() {
    // ひとまず全ページでテスト表示
    if ( is_front_page() || is_home() ) {
        ?>
        <div id="hero-section" class="hero-section" style="margin-top:20px; z-index:100; position:relative;">
          <!-- 画像パスを修正 -->
          <div class="hero-bg" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/images/hero-bg.jpg');"></div>
          <div class="hero-overlay"></div>
          <div id="particles-js"></div>
              </div>
            </div>
          </div>
        </div>
        <script>
        // 強制移動スクリプト：フッターに生成された要素をヘッダーの直下に移動させる
        document.addEventListener('DOMContentLoaded', function() {
            var hero = document.getElementById('hero-section');
            // Cocoonのヘッダー要素を探す (ID: header または class: header-container)
            var target = document.getElementById('header') || document.querySelector('.header-container');
            
            if (hero && target) {
                // ヘッダーの後ろ（直下）に移動
                target.parentNode.insertBefore(hero, target.nextSibling);
                // 移動後に表示フェードインアニメーションなどを発火させるためのクラス付与（必要なら）
                hero.classList.add('moved-to-top');
            }
        });

        // 既存のアニメーションスクリプト
        document.addEventListener('DOMContentLoaded', function() {
          // パララックス
          const heroBg = document.querySelector('.hero-bg');
          if (heroBg) {
            window.addEventListener('scroll', function() {
              const scrollPosition = window.pageYOffset;
              heroBg.style.transform = 'translate3d(0, ' + (scrollPosition * 0.4) + 'px, 0)';
            });
          }
          
          // particles.js
          if (typeof particlesJS !== 'undefined' && document.getElementById('particles-js')) {
            particlesJS('particles-js', {
              "particles": {
                "number": { "value": 30, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": ["#ffffff", "#00C6FF", "#8E2DE2"] },
                "shape": { "type": ["circle", "triangle"], "stroke": { "width": 0, "color": "#000000" } },
                "opacity": { "value": 0.5, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0.1, "sync": false } },
                "size": { "value": 4, "random": true, "anim": { "enable": false, "speed": 40, "size_min": 0.1, "sync": false } },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.3, "width": 1 },
                "move": { "enable": true, "speed": 2, "direction": "none", "random": true, "straight": false, "out_mode": "out", "bounce": false, "attract": { "enable": false, "rotateX": 600, "rotateY": 1200 } }
              },
              "interactivity": {
                "detect_on": "canvas",
                "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": true, "mode": "push" }, "resize": true },
                "modes": { "grab": { "distance": 140, "line_linked": { "opacity": 1 } }, "bubble": { "distance": 400, "size": 40, "duration": 2, "opacity": 8, "speed": 3 }, "repulse": { "distance": 200, "duration": 0.4 }, "push": { "particles_nb": 4 }, "remove": { "particles_nb": 2 } }
              },
              "retina_detect": true
            });
          }
        });
        </script>
        <?php
    }
}
// 確実に出るフッターに出力
add_action( 'wp_footer', 'add_hero_section', 1 );