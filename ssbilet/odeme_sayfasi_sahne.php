<?php
session_start();
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_destroy();
  header('Location: index.php'); // Çıkış yaptıktan sonra ana sayfaya yönlendirme
  exit;
}

// Veritabanı bağlantısı
$servername = "127.0.0.1:3307"; // Port farklıysa ekleyin

$username = "root";
$password = "";
$dbname = "ssbilet"; // Database adı

// Veritabanı bağlantısı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
  die("Bağlantı hatası: " . $conn->connect_error);
}

// Kullanıcının giriş yapıp yapmadığını kontrol et
if (!isset($_SESSION['user_name'])) {
  echo "<div class='alert alert-danger'>Lütfen giriş yapın!</div>";
  exit();
}

// Etkinlik id'sini URL'den al
$event_id = $_GET['id'];

// Etkinlik bilgilerini al
$sql = "SELECT * FROM sahne WHERE id = $event_id";
$result = $conn->query($sql);

// Etkinlik var mı diye kontrol et
if ($result->num_rows > 0) {
  $event = $result->fetch_assoc();
} else {
  echo "Etkinlik bulunamadı.";
  exit();
}

// Kullanıcı bilgilerini session'dan al
$user_name = $_SESSION['user_name']; // Kullanıcı adı
$user_email = $_SESSION['user_email']; // Kullanıcı e-posta

// Form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Ödeme bilgilerini al
  $name = $_POST['name']; // Ad Soyad
  $email = $_POST['email']; // E-posta
  $card_number = $_POST['card_number']; // Kart numarası
  $expiry_date = $_POST['expiry_date']; // Son kullanma tarihi
  $cvv = $_POST['cvv']; // CVV

  // Burada ödeme işlemini gerçekleştirebilirsiniz (örneğin ödeme sağlayıcı API'si ile)
  // Şu anda ödeme işlemini simüle ediyoruz

  // Ödeme başarılı kabul edelim
  echo "<div class='alert alert-success'>Ödeme başarılı! Etkinlik için biletiniz onaylandı.</div>";

  // Ödeme sonrası bilet kaydını tickets tablosuna ekleyelim
  $ticket_sql = "INSERT INTO tickets (event_name, event_date, price, user_id) 
                   VALUES (?, ?, ?, ?)";

  // Etkinlik bilgilerini al
  $event_name = $event['etkinlik_adi']; // Etkinlik adı
  $event_date = $event['tarih'] . ' ' . $event['saat']; // Etkinlik tarihi
  $price = $event['fiyat']; // Etkinlik fiyatı
  $user_id = $_SESSION['user_id']; // Kullanıcı ID'si

  // SQL sorgusunu hazırla
  $stmt_ticket = $conn->prepare($ticket_sql);
  if (!$stmt_ticket) {
    die("Sorgu hazırlama başarısız: " . $conn->error);
  }

  // Parametreleri bağla
  $stmt_ticket->bind_param("ssdi", $event_name, $event_date, $price, $user_id);

  // Sorguyu çalıştır
  if ($stmt_ticket->execute()) {
    // Bilet kaydı başarılı
    // Ödeme başarılı olduğu için kullanıcıya bir alert mesajı gösterelim
    echo "<script>
              alert('Ödeme başarılı! Etkinlik için biletiniz onaylandı.');
              window.location.href = 'index.php'; // Yönlendirme yapılacak sayfa
            </script>";
    exit();
  } else {
    // Bilet kaydında bir hata oluşursa
    echo "<div class='alert alert-danger'>Bilet kaydında bir hata oluştu. Lütfen tekrar deneyin.</div>";
  }


  // Sorgu ve bağlantıyı kapat
  $stmt_ticket->close();
  $conn->close();
  exit();

}
?>


<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SSBilet</title>
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
  <div class="container my-5">
    <h1><?php echo $event['etkinlik_adi']; ?> - Ödeme Sayfası</h1>

    <!-- Etkinlik Bilgileri -->
    <h4>Etkinlik Detayları:</h4>
    <p><strong>Fiyat:</strong> <?php echo $event['fiyat']; ?> TL</p>
    <p><strong>Şehir:</strong> <?php echo $event['sehir']; ?></p>
    <p><strong>Tarih:</strong> <?php echo $event['tarih']; ?> | <strong>Saat:</strong> <?php echo $event['saat']; ?></p>
    <p><strong>Açıklama:</strong> <?php echo $event['aciklama']; ?></p>
    <img src="<?php echo $event['resim']; ?>" class="img-fluid" alt="Etkinlik Görseli">

    <h4 class="mt-4">Ödeme Bilgilerinizi Girin</h4>

    <!-- Ödeme Formu -->
    <form method="POST" action="odeme_sayfasi_sahne.php?id=<?php echo $event['id']; ?>" class="mt-3">
      <!-- Ad Soyad, kullanıcı oturumdan alınır -->
      <div class="mb-3">
        <label for="name" class="form-label">Ad Soyad</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $user_name; ?>" required>
      </div>
      <!-- E-posta, kullanıcı oturumdan alınır -->
      <div class="mb-3">
        <label for="email" class="form-label">E-posta</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user_email; ?>" required>
      </div>
      <div class="mb-3">
        <label for="card_number" class="form-label">Kart Numarası</label>
        <input type="text" class="form-control" id="card_number" name="card_number" required>
      </div>
      <div class="mb-3">
        <label for="expiry_date" class="form-label">Son Kullanma Tarihi</label>
        <input type="month" class="form-control" id="expiry_date" name="expiry_date" required>
      </div>
      <div class="mb-3">
        <label for="cvv" class="form-label">CVV</label>
        <input type="text" class="form-control" id="cvv" name="cvv" required>
      </div>
      <button type="submit" class="btn btn-success">Ödeme Yap</button>
    </form>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>