<?php
/**
 * ヒーローセクションテンプレート
 */
?>
<div id="hero-section" class="hero-section">
  <!-- 背景画像レイヤー -->
  <div class="hero-bg" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/images/hero-bg.jpg');"></div>
  
  <!-- オーバーレイ（暗くする用） -->
  <div class="hero-overlay"></div>
  
  <!-- particles.js コンテナ -->
  <div id="particles-js"></div>
  
  <!-- コンテンツ -->
  <div class="hero-content">
    <div class="hero-content-inner">
      <h2 class="hero-title">
        <span class="fade-in-text delay-1">That choice</span><br>
        <span class="fade-in-text delay-2">creates the</span><br>
        <span class="fade-in-text delay-3 text-gradient">future</span>
      </h2>
      
      <p class="hero-subtitle fade-in-text delay-4">
        Kickstarterで見つけた革新的プロダクトを<br>
        アウトドア好きエナガがレビュー＆紹介
      </p>
      
      <div class="hero-cta fade-in-text delay-5">
        <a href="#new-posts" class="btn-shine">最新記事を見る</a>
      </div>
    </div>
  </div>
  
  <!-- スクロールダウンインジケーター -->
  <div class="scroll-down">
    <span></span>
    <span></span>
    <span></span>
  </div>
</div>
