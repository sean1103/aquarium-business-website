<?php
session_start();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>🤿</title>
  <style>

        header {
            background-color: #333;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .home-link {
            color: gray;
            text-decoration: none;
            font-size: 18px;
            margin-right: 15px;
        }

        .home-link:hover {
            color:rgb(255, 255, 255);
            transform: scale(1.1);
        }

    body {
      margin: 0;
      background-color: rgb(30, 30, 30);
      font-family: Arial, sans-serif;
      color: white;
      height: 100vh;
      overflow: hidden;
    }

    .container {
      display: flex;
      height: 100vh;
      align-items: center;
      justify-content: space-between;
      padding: 0 30px;
      box-sizing: border-box;
    }

    .side-column {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      width: 450px;
      justify-content: center;
    }

    .side-column img {
      width: 160px;
      height: 160px;
      object-fit: cover;
      border-radius: 12px;
      cursor: pointer;
      opacity: 0.4;
      transition: opacity 0.3s ease;
    }

    .side-column img:hover {
      opacity: 1;
      transform: scale(1.20);
    }

    .video-container {
      width: 600px;
      height: 350px;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(123, 123, 123, 0.3);
      flex-shrink: 0;
      background-color: black;
      margin-bottom: 20px;
    }

    .video-button {
      display: inline-block;
      margin-top: 10px;
      margin-bottom: 20px;
      padding: 10px 20px;
      background-color:rgb(45, 45, 45);
      color: white;
      border: none;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .video-button:hover {
      background-color:rgb(240, 240, 240);
      color:black;
    }

    .video-container video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    #carousel-image {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 15px;
      margin-bottom: 100px;
      box-shadow: 0 4px 8px rgba(123, 123, 123, 0.3);
    }

    .carousel-caption {
      font-size: 16px;
      text-align: center;
      padding: 10px;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
    }

    .emoji {
      position: absolute;
      font-size: 35px;
      z-index: 5;
      pointer-events: none;
      animation: moveLeft 10s forwards;
    }

    @keyframes moveLeft {
      0% {
        transform: translateX(0);
        opacity: 1;
      }
      100% {
        transform: translateX(-100vw);
        opacity: 0;
      }
    }

    .right-section {
      position: relative;
      width: 350px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .right-section img {
      width: 100%;
      height: 300px;
      border-radius: 12px;
      object-fit: cover;
      transition: 0.5s ease;
      transform: translateY(-10px);
    }

    .right-section button {
      position: absolute;
      top: 30%;
      transform: translateY(-50%);
      color: white;
      border: none;
      font-size: 14px;
      padding: 10px;
      border-radius: 50%;
      cursor: pointer;
      z-index: 10;
      transition: background-color 0.3s ease;
      opacity: 0%;
    }

    .right-section button:hover {
      background-color: rgba(0, 0, 0, 0.8);
      opacity: 100%;
    }

    .right-section button:first-of-type {
      left: 10px;
    }

    .right-section button:last-of-type {
      right: 10px;
    }

    #carousel-text {
      margin-top: 20px;
      text-align: left;
      color: white;
      font-weight: bold;
      font-family: '微軟正黑體';
      width: 100%;
      line-height: 1.5;
    }

    #lightbox {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 999;
    }

    #lightbox img {
      max-width: 90%;
      max-height: 90%;
      border: 4px solid white;
      border-radius: 10px;
    }

    #lightbox-close {
      position: absolute;
      top: 20px;
      right: 40px;
      color: white;
      font-size: 40px;
      font-weight: bold;
      cursor: pointer;
    }
  </style>
</head>

<body>

<header>
  <a href="home.php" class="home-link">回首頁</a>
  <a href="cart.php" class="home-link">🛒</a>
  <span class="user-name">
  <?php 
    if (isset($_SESSION["username"])) {
      echo '<a href="order.php" style="color: white; text-decoration: none;">' . htmlspecialchars($_SESSION["username"]) . ' 的頁面</a>';
    } else {
      echo '<a href="login.php" class="home-link" >登入</a>';
    }
  ?>
  </span>
</header>

<div class="container">
  <!-- 右邊圖片和文字區域 -->
  <div class="right-section">
  <button onclick="prevImage(); resetAutoSlide()">◀️</button>
  <img id="carousel-img" src="asses/tanks/12.jpg" alt="carousel image">
  <button onclick="nextImage(); resetAutoSlide()">▶️</button>

    <div id="carousel-text">
      飼養海水魚需要一些專門的設備來維持水質穩定和魚類健康。
      主要設備包括魚缸、濾水系統、蛋白質分離器、加熱器、水質測試工具、鹽度計、照明系統、水泵和氧氣泵。<br><br>
      為了製作人工海水，需要使用鹽混合劑；珊瑚和活石可作為過濾系統的一部分並增強景觀。
      自動餵食器幫助定時餵食，而冷卻器則在高溫環境中保持水溫穩定。
      這些設備共同作用，確保海水魚在適宜的環境中生長。
    </div>
  </div>

  <!-- 左邊影片和圖片區域 -->
  <div style="display: flex; flex-direction: column; align-items: center;">
    <a class="video-button" href="https://garychiu2001.pixnet.net/blog/post/27869083" target="_blank">
      🤿想知道怎麼養一個海水魚缸嗎?🐠
    </a>
    <div class="video-container">
      <a href="https://youtu.be/H82orWWNs0A?si=DfbJ1tQg053vvabi"><video src="asses/fish3.mp4" autoplay muted loop></video></a>
    </div>
    <a class="video-button" href="https://www.ph84.idv.tw/forum/" target="_blank">
      更多海水魚資訊
    </a>
  </div>

  <!-- 左邊圖片欄 -->
  <div class="side-column">
    <img src="asses/tanks/1.jpg" alt="1">
    <img src="asses/tanks/2.jpg" alt="2">
    <img src="asses/tanks/3.jpg" alt="3">
    <img src="asses/tanks/4.jpg" alt="4">
    <img src="asses/tanks/5.jpg" alt="5">
    <img src="asses/tanks/7.jpg" alt="6">
  </div>

</div>

<div id="lightbox" style="display:none;">
  <span id="lightbox-close">×</span>
  <img id="lightbox-img" src="" alt="preview">
</div>

<script>
  const emojis = ['🐠', '🐡', '🐟'];

  const isLoggedIn = <?php echo isset($_SESSION["username"]) ? 'true' : 'false'; ?>;

  if (isLoggedIn) {
    document.body.addEventListener('click', (event) => {
      if (!event.target.closest('.video-container') && !event.target.closest('.side-column')) {
        const emoji = document.createElement('span');
        emoji.className = 'emoji';
        emoji.innerText = emojis[Math.floor(Math.random() * emojis.length)];
        emoji.style.left = `${event.pageX}px`;
        emoji.style.top = `${event.pageY - 25}px`;

        document.body.appendChild(emoji);

        setTimeout(() => emoji.remove(), 10000);
      }
    });
  }

  const carouselImages = [
    { src: 'asses/tanks/12.jpg' },
    { src: 'asses/tanks/8.jpg' },
    { src: 'asses/tanks/9.jpg' },
    { src: 'asses/tanks/10.jpg' },
    { src: 'asses/tanks/11.jpg' },
    { src: 'asses/tanks/13.jpg' },
    { src: 'asses/tanks/14.jpg' },
  ];

  let currentIndex = 0;
  const carouselImg = document.getElementById('carousel-img');

  function showImage(index) {
    carouselImg.src = carouselImages[index].src;
  }

  function nextImage() {
    currentIndex = (currentIndex + 1) % carouselImages.length;
    showImage(currentIndex);
  }

  function prevImage() {
    currentIndex = (currentIndex - 1 + carouselImages.length) % carouselImages.length;
    showImage(currentIndex);
  }

  // 自動輪播
  let autoSlide = setInterval(nextImage, 3000);

  // 如果用戶點了按鈕，自動輪播暫停再繼續
  function resetAutoSlide() {
    clearInterval(autoSlide);
    autoSlide = setInterval(nextImage, 3000);
  }

  document.querySelectorAll('.side-column img').forEach(img => {
    img.addEventListener('click', () => {
      const lightbox = document.getElementById('lightbox');
      const lightboxImg = document.getElementById('lightbox-img');
      lightboxImg.src = img.src;
      lightbox.style.display = 'flex';
    });
  });

  document.getElementById('lightbox-close').addEventListener('click', () => {
    document.getElementById('lightbox').style.display = 'none';
  });
</script>

</body>
</html>
