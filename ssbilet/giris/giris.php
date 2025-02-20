<?php
session_start(); // Oturum başlatma
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header('Location: index.php'); // Çıkış yaptıktan sonra ana sayfaya yönlendirme
    exit;
  }
  
// Veritabanı bağlantısı
$servername = "127.0.0.1:3307"; // Port farklıysa ekleyin

$username = "root";
$password = "";
$dbname = "ssbilet"; // Veritabanı adı

$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kullanıcıdan gelen verileri al
    $email = $_POST['email'];
    $password = $_POST['password'];
   

    // SQL sorgusu ile veritabanında kullanıcıyı ara
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Giriş başarılı, kullanıcı bilgilerini session'a kaydet
        $row = $result->fetch_assoc();
        $_SESSION['user_name'] = $row['firstname'] . ' ' . $row['lastname'];  // İsim ve soyismi oturumda sakla
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_id'] = $row['id'];
        // Giriş başarılı mesajı ve yönlendirme
        echo "<script>alert('Giriş başarılı!'); window.location.href='index.php';</script>";
    } else {
        // Giriş başarısız, hata mesajı göster
        echo "<script>alert('E-posta veya şifre hatalı!');</script>";
    }
}

// Bağlantıyı kapatma
$conn->close();
?>




<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSBilet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        .nav-auth-group {
            display: flex;
            align-items: center;
            background-color: #F5F5F5;
            border-radius: 50px;
            border: 1px solid #ffffff;
            padding: 5px 20px;
        }

        .nav-auth-link {
            color: #212121;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .nav-auth-link:hover {
            color: #00BCD4;
        }

        .switch-btns button {
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: bold;
            color: #007bff;
            border: 2px solid #007bff;
            background-color: transparent;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .switch-btns button:hover {
            background-color: #007bff;
            color: white;
        }

        .switch-btns button:focus {
            outline: none;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        /* Card genişliğini sınırla ve mobilde daha uyumlu hale getir */
.card {
    display: flex;
    flex-direction: row;
    border-radius: 15px;
    width: 100%;
    max-width: 800px; /* Maksimum genişlik */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
    margin-bottom: 20px;
    margin-left: auto;
    margin-right: auto;
    flex-wrap: wrap; /* İçeriğin taşmasını engelle */
}

.card-left {
    background-color: #007bff;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 40%; /* Kartın soldaki kısmı max %40 genişlikte olacak */
    border-top-left-radius: 15px;
    border-bottom-left-radius: 15px;
}

.card-left img {
    max-width: 80%;
    border-radius: 15px;
}

.card-body {
    width: 100%;
    max-width: 60%; /* Kartın sağdaki kısmı max %60 genişlikte olacak */
    padding: 30px;
}

/* Mobil cihazlar için uyumlu form */
@media (max-width: 768px) {
    .card {
        flex-direction: column; /* Kartın içeriğini dikey hizala */
        width: 90%; /* Kartı daha dar yap */
    }

    .card-left {
        width: 100%; /* Görselin genişliğini %100 yap */
        max-width: 100%; /* Genişliği %100 yap */
        border-radius: 15px 15px 0 0; /* Alt köşeleri yuvarlat */
    }

    .card-body {
        width: 100%; /* Sağ kısmı tamamen genişlet */
        padding: 15px; /* Daha küçük paddingle uyumlu hale getir */
    }

    .form-control {
        width: 100%; /* Giriş elemanlarının genişliğini %100 yap */
    }

    .btn-ellipse {
        width: 100%; /* Butonun genişliğini %100 yap */
    }
}

/* Form alanlarının hizalanması */
.mb-3 {
    margin-bottom: 15px; /* Form elemanları arasında yeterli boşluk bırak */
}

.form-label {
    font-weight: bold;
}

/* Düğme stilleri */
.btn-primary {
    width: 100%; /* Butonu genişlet */
    padding: 12px; /* Butonun yüksekliğini artır */
    border-radius: 50px;
    font-weight: bold;
}


        footer {
            background-color: #333;
            color: white;
            padding: 20px 0;
            margin-top: auto;
            /* Footer'ı en alt kısma çeker */
        }

        footer .container {
            max-width: 1200px;
        }
    </style>
</head>

<body>
    <!-- Header -->
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
                            <a class="nav-auth-link" href="profile.php">
                                <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                        <?php else: ?>
                            <a href="giris.php" class="nav-auth-link">
                                <i class="fas fa-user"></i> Giriş Yap
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>


    <!-- Card with Login Form -->
    <div class="card">
        <div class="card-left">
            <img src="logo.png" alt="Logo">
        </div>

        <div class="card-body">
            <!-- Giriş Yap Formu -->
            <h3 class="mb-4">Giriş Yap</h3>
            <form action="giris.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta Adresi</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-ellipse">Giriş Yap</button>
                <p>Üye değil misiniz? <a href="uyeol.php">Üye Olun</a></p>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <div class="row">
                <!-- Hakkımızda -->
                <div class="col-md-4 mb-3">
                    <h5>Hakkımızda</h5>
                    <p>
                        SSBilet, üniversite öğrencilerinin organize ettiği spor, sahne ve konser etkinliklerinin bilet
                        satış ve alım platformudur. Kampüs içi konserlerden öğrenci topluluklarının sahnelediği tiyatro
                        oyunlarına, spor turnuvalarından özel atölyelere kadar birçok etkinlik için bilet satın alabilir
                        ve unutulmaz anılar biriktirebilirsiniz. Güvenli ödeme sistemimiz ve kullanıcı dostu
                        arayüzümüzle, hem etkinlik düzenleyicilere hem de katılımcılara en iyi deneyimi sunmayı
                        hedefliyoruz.
                    </p>
                </div>
                <!-- İletişim -->
                <div class="col-md-4 mb-3">
                    <h5>İletişim</h5>
                    <p>
                        <i class="bi bi-geo-alt-fill"></i> Adres: İstanbul, Türkiye<br>
                        <i class="bi bi-envelope-fill"></i> E-posta: <a href="mailto:info@etkinliksatis.com"
                            class="text-white">info@etkinliksatis.com</a><br>
                        <i class="bi bi-telephone-fill"></i> Telefon: <a href="tel:+900123456789" class="text-white">+90
                            123 456 78
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>