<?php
session_start(); // Oturum başlatma
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_destroy();
  header('Location: index.php'); // Çıkış yaptıktan sonra ana sayfaya yönlendirme
  exit;
}

// Database connection (use your actual database credentials)
$servername = "127.0.0.1:3307"; // Port farklıysa ekleyin

$username = "root";
$password = "";
$dbname = "ssbilet"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Query to get event data
$sql = "SELECT * FROM spor";
$result = $conn->query($sql);
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
    .card {
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .card-img-top {
      height: 200px;
      object-fit: cover;
      transition: opacity 0.3s ease;
    }

    /* Başlangıçta sadece resim görünür, diğer içerik gizli */
    .card-body {
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      padding: 10px;
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      transition: top 0.3s ease;
    }

    /* Kart üzerine gelince içerikler yukarı kayar */
    .card:hover .card-img-top {
      opacity: 0.7;
    }

    .card:hover .card-body {
      top: 0;
    }

    /* Butonu resmin hemen altına yerleştir */
    .btn-buy {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      padding: 20px;
      background-color: rgba(0, 0, 0, 1);
      color: white;
      text-align: center;
      display: none;
      /* Başlangıçta gizle */
    }

    .switch-btns button {
      border-radius: 50px;
      /* Elips şekli */
      padding: 10px 30px;
      /* İç boşluk */
      font-weight: bold;
      /* Kalın yazı */
      color: #007bff;
      /* Yazı rengi */
      border: 2px solid #007bff;
      /* Kenarlık */
      background-color: transparent;
      /* Arka plan transparan */
      text-decoration: none;
      /* Alt çizgiyi kaldırır */
      transition: background-color 0.3s ease, color 0.3s ease;
      /* Hover efekti */
    }

    .switch-btns button:hover {
      background-color: #007bff;
      /* Hoverda arka plan rengi */
      color: white;
      /* Yazı rengi hoverda beyaz */
    }

    .switch-btns button:focus {
      outline: none;
      /* Focus durumunda kenarlık olmaması için */
    }

    /* Hover ile butonu görünür yap */
    .card:hover .btn-buy {
      display: block;
    }

    .card-footer {
      padding: 10px;
      background-color: #00bbd4;
    }

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

  <!-- Navbar -->
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

  <!-- Etkinlikler Kartları -->
  <section class="container my-5">
    <h2 class="text-center mb-4">Spor Etkinlikleri</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php
      // Loop through the results and generate cards
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          // Dakikayı düzgün formatlamak için str_pad kullanılıyor
          $formatted_time = $row['saat'] . ':' . str_pad($row['dakika'], 2, '0', STR_PAD_LEFT);

          echo '<div class="col">
                    <div class="card">
                      <img src="' . $row['resim'] . '" class="card-img-top" alt="' . $row['etkinlik_adi'] . '">
                      <div class="card-body">
                        <h5 class="card-title">' . $row['etkinlik_adi'] . '</h5>
                        <p class="card-text">' . $row['aciklama'] . '</p>
                      </div>
                      <a href="etkinlik_detay_spor.php?id=' . $row['id'] . '" class="btn btn-primary btn-buy">Satın Al</a>
                      <div class="card-footer">
                        <p><strong>' . $row['sehir'] . '</strong> | <span>' . $row['fiyat'] . ' TL</span> | <span>' . $formatted_time . '</span> | <span>' . $row['tarih'] . '</span></p>
                      </div>
                    </div>
                  </div>';
        }
      } else {
        echo "<p>No events found.</p>";
      }

      // Close the database connection
      $conn->close();
      ?>
    </div>
</section>


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
            hem de katılımcılara en iyi deneyimi sunmayı hedefliyoruz..
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