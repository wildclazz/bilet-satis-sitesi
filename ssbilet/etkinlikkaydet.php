<?php
session_start(); // Oturum başlatma
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_destroy();
  header('Location: index.php'); // Çıkış yaptıktan sonra ana sayfaya yönlendirme
  exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Formdan gelen veriler
  $etkinlikAdi = $_POST['etkinlik_adi'] ?? '';
  $aciklama = $_POST['aciklama'] ?? '';
  $sehir = $_POST['sehir'] ?? '';
  $fiyat = $_POST['fiyat'] ?? 0;
  $tarih = $_POST['tarih'] ?? '';
  $saat = $_POST['saat'] ?? '';
  $dakika = $_POST['dakika'] ?? 0; // Dakika varsayılan olarak 0
  $tur = $_POST['tur'] ?? '';

  try {
    $time = new DateTime($saat);
    $saat = $time->format('H:i'); // Saat formatını "HH:MM" olarak düzelt
} catch (Exception $e) {
    die("Geçersiz saat formatı!");
}

  // Veritabanı bağlantısı
  $servername = "127.0.0.1:3307"; // Port farklıysa ekleyin

  $username = "root"; // Veritabanı kullanıcı adınız
  $password_db = ""; // Veritabanı şifreniz
  $dbname = "ssbilet"; // Veritabanı adı

  $conn = new mysqli($servername, $username, $password_db, $dbname);

  // Bağlantı kontrolü
  if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
  }

  // Tablo adı etkinlik türüne göre belirlenir
  if ($tur == "sahne") {
    $tableName = "sahne";
  } elseif ($tur == "konser") {
    $tableName = "konser";
  } elseif ($tur == "spor") {
    $tableName = "spor";
  } else {
    die("Geçersiz etkinlik türü seçildi!");
  }

  // Resim dosyasını yükleme işlemi
  $target_dir = "uploads/"; // Yüklemelerin saklanacağı klasör
  $target_file = $target_dir . basename($_FILES["resim"]["name"]);
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  // Sadece belirli dosya türlerine izin ver
  if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
    // Resmi sunucuda hedef klasöre yükle
    if (move_uploaded_file($_FILES["resim"]["tmp_name"], $target_file)) {
      $minute = isset($_POST['minute']) && is_numeric($_POST['minute']) ? intval($_POST['minute']) : 0;
      // Resim başarıyla yüklendi, bilgileri veritabanına kaydet
      $stmt = $conn->prepare("INSERT INTO $tableName (etkinlik_adi, aciklama, sehir, fiyat, tarih, saat, dakika, resim) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssdsiss", $etkinlikAdi, $aciklama, $sehir, $fiyat, $tarih, $saat, $minute, $target_file);
      

      if ($stmt->execute()) {
        echo "<script>alert('Etkinlik başarıyla kaydedildi!');</script>";
      } else {
        echo "Hata: " . $stmt->error;
      }

      $stmt->close();
    } else {
      echo "Resim yüklenirken bir hata oluştu.";
    }
  } else {
    echo "Yalnızca JPG, JPEG, PNG ve GIF dosyaları yüklenebilir.";
  }

  $conn->close();
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
      color: #00BCD4;
      /* Hover efekti için renk */
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
              <a class="nav-auth-link" href="kisibilgi.php">
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

  <div class="container my-5">
    <form action="etkinlikkaydet.php" method="POST" class="p-4 bg-light shadow rounded" enctype="multipart/form-data">
        <h2 class="text-center text-primary mb-4">Etkinlik Kaydet</h2>

        <!-- Etkinlik Adı -->
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="etkinlik_adi" name="etkinlik_adi" placeholder="Etkinlik Adı" required>
            <label for="etkinlik_adi">Etkinlik Adı</label>
        </div>

        <!-- Açıklama -->
        <div class="form-floating mb-3">
            <textarea class="form-control" name="aciklama" id="aciklama" placeholder="Açıklama" required></textarea>
            <label for="aciklama">Açıklama</label>
        </div>

        <!-- Şehir -->
        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="sehir" id="sehir" placeholder="Şehir" required>
            <label for="sehir">Şehir</label>
        </div>

        <!-- Fiyat -->
        <div class="form-floating mb-3">
            <input type="number" class="form-control" name="fiyat" id="fiyat" placeholder="Fiyat" required>
            <label for="fiyat">Fiyat (TL)</label>
        </div>

        <!-- Tarih -->
        <div class="form-floating mb-3">
            <input type="date" class="form-control" name="tarih" id="tarih" placeholder="Tarih" required>
            <label for="tarih">Tarih</label>
        </div>

        <!-- Saat ve Dakika -->
        <div class="row mb-3">
            <!-- Saat -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="number" class="form-control" name="hour" id="hour" placeholder="Saat" min="0" max="23" required>
                    <label for="hour">Saat</label>
                </div>
            </div>
            <!-- Dakika -->
            <div class="col-md-6">
                <div class="form-floating">
                <input type="number" class="form-control" name="minute" id="minute" placeholder="Dakika" min="0" max="59" required>

                    <label for="dakika">Dakika</label>
                </div>
            </div>
        </div>

        <!-- Resim -->
        <div class="mb-3">
            <label for="resim" class="form-label">Etkinlik Resmi</label>
            <input type="file" class="form-control" id="resim" name="resim" accept="image/*" required>
        </div>

        <!-- Etkinlik Türü -->
        <div class="mb-3">
            <label for="tur" class="form-label">Etkinlik Türü</label>
            <select name="tur" id="tur" class="form-select" required>
                <option value="sahne">Sahne</option>
                <option value="konser">Konser</option>
                <option value="spor">Spor</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">Kaydet</button>
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