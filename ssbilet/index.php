<?php
session_start(); // Oturum başlatma


if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_destroy();
  header('Location: index.php'); // Çıkış yaptıktan sonra ana sayfaya yönlendirme
  exit;
}


?>
<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SSBilet</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    .nav-auth-group {
      display: flex;
      align-items: center;
      background-color: #F5F5F5;
      /* Navbar rengi ile aynı */
      border-radius: 50px;
      /* Elips şekli */
      border: 1px solid #ffffff;
      /* Çizgi kenarı */
      padding: 5px 20px;
      /* İç boşluk */
    }

    .nav-auth-link {
      color: #212121;
      /* Yazı rengi */
      text-decoration: none;
      /* Alt çizgiyi kaldır */
      font-weight: bold;
      /* Kalın yazı */
      margin: 0 10px;
      /* Linkler arası boşluk */
      transition: color 0.3s ease;
      /* Renk geçiş efekti */
    }

    .nav-auth-link:hover {
      color: #00BCD4;
      /* Hover efekti için renk */
    }

    .separator {
      color: #212121;
      /* Çizgi rengi */
      font-size: 1rem;
      /* Boyut */
      font-weight: bold;
    }

    .carousel-item img {
      height: 500px;
      
    }

    .card img {
      transition: transform 0.3s ease;
    }

    .card img:hover {
      transform: scale(1.1);
    }

    .image-container {
      position: relative;
      width: 100%;
      overflow: hidden;
      border-radius: 8px;
    }

    .image-container img {
      width: 100%;
      display: block;
      border-radius: 8px;
      transition: transform 0.3s ease;
    }

    .image-container:hover img {
      transform: scale(1.1);
    }

    .image-container .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: #00bbd44b;
      /* Şeffaf mavi */
      opacity: 0;
      transition: opacity 0.3s ease;
      border-radius: 8px;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #F5F5F5;
      font-size: 1.5rem;
      font-weight: bold;
      text-align: center;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }

    .image-container:hover .overlay {
      opacity: 1;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div style="color:#F5F5F5; font-weight: bold;" class="container">
      <a style="color:#F5F5F5;" class="navbar-brand" href="index.php">SSBilet</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <div class="d-flex align-items-center">
          <div class="nav-auth-group px-4 py-2">
            <?php if (isset($_SESSION['user_name'])): ?>
              <!-- Kullanıcı giriş yaptıysa kullanıcı adı ve bağlantılar -->
              <a class="nav-auth-link" href="profile.php">
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
              </a>

              <a class="nav-auth-link" href="?logout=true">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
              </a>
            <?php else: ?>
              <!-- Giriş yapmamışsa -->
              <a href="giris.php" class="nav-auth-link">
                <i class="fas fa-user"></i> Giriş Yap
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- CSS (İç style olarak eklendi) -->

  <div style="width: 80%; margin-left: 10%; margin-top:50px ">
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="slider1.png" class="d-block w-100" alt="Slider 1">
          <div class="carousel-caption d-none d-md-block">
            <h1>En İyi Etkinlikler Burada</h1>
            <p>Hemen yerinizi ayırtın ve eğlenceye katılın!</p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="slider2.jpg" class="d-block w-100" alt="Slider 2">
          <div class="carousel-caption d-none d-md-block">
            <h1>Unutulmaz Anılar İçin</h1>
            <p>Sizi en özel etkinliklere davet ediyoruz.</p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="slider3.png" class="d-block w-100" alt="Slider 3">
          <div class="carousel-caption d-none d-md-block">
            <h1>Yerinizi Hemen Ayırtın</h1>
            <p>Eğlence ve heyecan dolu bir deneyim sizi bekliyor.</p>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Önceki</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sonraki</span>
      </button>
    </div>
  </div>
  <!-- Event Cards -->
  <section class="container my-5">
    <div class="row text-center">
      <h2>Etkinlik Türleri</h2>
      <div class="col-md-4 mb-4">
        <div class="card image-container">
          <a href="konser.php" class="image-link">
            <img src="kanyewest.jpg" class="card-img-top" alt="Müzik">
            <div class="overlay">
              <div class="overlay-text">Konser<br>"Ritmi yakalayın, eğlenceye katılın! "</div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card image-container">
          <a href="spor.php" class="image-link">
            <img src="saha.jpg" class="card-img-top" alt="Spor">
            <div class="overlay">
              <div class="overlay-text">Spor<br>"Taraftarın gücüyle zafere ortak ol!"</div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card image-container">
          <a href="sahne.php" class="image-link">
            <img src="sahne.png" class="card-img-top" alt="Sahne">
            <div class="overlay">
              <div class="overlay-text">Sahne<br>"Sahne'nin büyüsüne kapılın!"</div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>
  <?php if (isset($_SESSION['user_name'])): ?>
    <!-- Sol alt köşeye pop-up tarzı buton -->
    <style>
      #etkinlikKaydetBtn {
        position: fixed;
        bottom: 20px;
        left: 20px;
        background-color: #00BCD4;
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        z-index: 1000;
        transition: transform 0.3s ease;
      }

      #etkinlikKaydetBtn:hover {
        background-color: #008C9E;
        transform: scale(1.1);
      }
    </style>

    <a href="etkinlikkaydet.php">
      <button id="etkinlikKaydetBtn">
        Etkinlik Kaydet
      </button>
    </a>
  <?php endif; ?>

  <!-- Footer -->
  <!-- Footer -->
  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-4">
    <div class="container">
      <div class="row">
        <!-- Hakkımızda -->
        <div class="col-md-4 mb-3">
          <h5>Hakkımızda</h5>
          <p>
            SSBilet, üniversite öğrencilerinin organize ettiği spor, sahne ve konser etkinliklerinin bilet satış ve alım
            platformudur. Kampüs içi konserlerden öğrenci topluluklarının sahnelediği tiyatro oyunlarına, spor
            turnuvalarından özel atölyelere kadar birçok etkinlik için bilet satın alabilir ve unutulmaz anılar
            biriktirebilirsiniz. Güvenli ödeme sistemimiz ve kullanıcı dostu arayüzümüzle, hem etkinlik düzenleyicilere
            hem de katılımcılara en iyi deneyimi sunmayı hedefliyoruz.
          </p>
        </div>
        <!-- İletişim -->
        <div class="col-md-4 mb-3">
          <h5>İletişim</h5>
          <p>
            <i class="bi bi-geo-alt-fill"></i> Adres: İstanbul, Türkiye<br>
            <i class="bi bi-envelope-fill"></i> E-posta: <a href="mailto:info@etkinliksatis.com"
              class="text-white">info@etkinliksatis.com</a><br>
            <i class="bi bi-telephone-fill"></i> Telefon: <a href="tel:+900123456789" class="text-white">+90 123 456 78
              90</a>
          </p>
        </div>
        <!-- Sosyal Medya -->
        <div class="col-md-4 mb-3">
          <h5>Bizi Takip Edin</h5>
          <a href="https://facebook.com" class="text-white me-3">
            <i class="bi bi-facebook"></i> Facebook
          </a>
          <a href="https://twitter.com" class="text-white me-3">
            <i class="bi bi-twitter"></i> Twitter
          </a>
          <a href="https://instagram.com" class="text-white">
            <i class="bi bi-instagram"></i> Instagram
          </a>
        </div>
      </div>
      <hr class="my-3 bg-light">
      <p class="mb-0">© 2024 SSBilet. Tüm Hakları Saklıdır.</p>
    </div>
  </footer>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>