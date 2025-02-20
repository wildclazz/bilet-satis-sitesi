<?php
session_start();
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_destroy();
  header('Location: index.php'); // Çıkış yaptıktan sonra ana sayfaya yönlendirme
  exit;
}

// Database connection
$servername = "127.0.0.1:3307"; // Port farklıysa ekleyin

$username = "root";
$password = "";
$dbname = "ssbilet"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get event id from URL
$event_id = $_GET['id'];

// Query to get event details
$sql = "SELECT * FROM spor WHERE id = $event_id";
$result = $conn->query($sql);

// Check if event exists
if ($result->num_rows > 0) {
  $event = $result->fetch_assoc();
} else {
  echo "Etkinlik bulunamadı.";
  exit();
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
      color: #00bbd44b;
      /* Hover efekti için renk */
    }
    /* Resim ve Butonun Yan Yana Olmasını Sağlama */
.event-image-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.event-image-container img {
  max-width: 100%; /* Resmin boyutunu sayfa genişliğine göre sınırlama */
  height: auto; /* Yüksekliği oranla ayarlama */
  max-height: 400px; /* Yüksekliği sınırlama */
}

.event-image-container a {
  margin-top: 20px; /* Buton ile resim arasına boşluk ekleme */
}

  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div style="color:#F5F5F5 ; font-weight: bold;" class="container">
      <a style="color:#F5F5F5 ;" class="navbar-brand" href="index.php">SSBilet</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <div class="d-flex align-items-center">
          <div class="nav-auth-group px-4 py-2">
            <?php if (isset($_SESSION['user_name'])): ?>
              <!-- Kullanıcı giriş yaptıysa kullanıcı adı, profil bağlantısı ve çıkış yap butonu göster -->
              <a class="nav-auth-link" href="profile.php">
                <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
              </a>
              <a class="nav-auth-link" href="?logout=true">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
              </a>
            <?php else: ?>
              <!-- Kullanıcı giriş yapmadıysa giriş yap bağlantısı göster -->
              <a href="giris.php" class="nav-auth-link">
                <i class="fas fa-user"></i> Giriş Yap
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <!-- Etkinlik Detay Başlık -->
  <div class="container my-5">
  <h1><?php echo $event['etkinlik_adi']; ?></h1>
<p><strong>Fiyat:</strong> <?php echo $event['fiyat']; ?> TL</p>
<p><strong>Şehir:</strong> <?php echo $event['sehir']; ?></p>
<p>
  <strong>Tarih:</strong> <?php echo $event['tarih']; ?> | 
  <strong>Saat:</strong> <?php echo $event['saat'] . ':' . str_pad($event['dakika'], 2, '0', STR_PAD_LEFT); ?>
</p>
<p><strong>Açıklama:</strong> <?php echo $event['aciklama']; ?></p>
  
  <!-- Resim ve Buton Kapsayıcı -->
  <div class="event-image-container">
    <img src="<?php echo $event['resim']; ?>" class="img-fluid" alt="Etkinlik Görseli">
    <!-- Satın Al Butonu -->
    <a href="odeme_sayfasi_spor.php?id=<?php echo $event['id']; ?>" class="btn btn-success mt-4">Satın Al</a>
  </div>
</div>
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