<?php
// Oturum başlatma
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
$dbname = "ssbilet";

$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kullanıcı ID'sini oturumdan al
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("Lütfen giriş yapınız.");
}

// Kullanıcı bilgilerini sorgulama
$userQuery = "SELECT firstname, lastname, email FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);

if (!$stmt) {
    die("Sorgu hazırlama başarısız: " . $conn->error);  // Hata mesajını göster
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
    $fullName = $user['firstname'] . ' ' . $user['lastname'];
    $email = $user['email'];
} else {
    die("Kullanıcı bilgileri bulunamadı.");
}
// Kullanıcı bilgilerini sorgulama
$userQuery = "SELECT firstname, lastname, email FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);

if (!$stmt) {
    die("Sorgu hazırlama başarısız: " . $conn->error);
}

// Kullanıcı ID'sini bağla
$stmt->bind_param("i", $user_id); // "i" => integer türü parametre
$stmt->execute();
$userResult = $stmt->get_result();

// Kullanıcı bilgilerini al
if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
    $fullName = $user['firstname'] . ' ' . $user['lastname'];
} else {
    die("Kullanıcı bilgileri bulunamadı.");
}

// Bilet bilgilerini sorgulama
$ticketsQuery = "SELECT event_name, event_date, price FROM tickets WHERE user_id = ?";
$stmt = $conn->prepare($ticketsQuery);

if (!$stmt) {
    die("Sorgu hazırlama başarısız: " . $conn->error);
}

// Kullanıcı ID'sini bağla
$stmt->bind_param("i", $user_id); // "i" => integer türü parametre
$stmt->execute();
$ticketsResult = $stmt->get_result();

// Bilet bilgilerini al
$tickets = [];
while ($row = $ticketsResult->fetch_assoc()) {
    $tickets[] = $row;
}

// Bağlantıyı kapat
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSBilet - Profil</title>
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

    <div class="container mt-5">
        <h1 class="text-center">Profil</h1>

        <!-- Kullanıcı Bilgileri -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Kullanıcı Bilgileri</h4>
            </div>
            <div class="card-body">
                <p><strong>Adı Soyadı:</strong> <?= htmlspecialchars($fullName); ?></p>
                <p><strong>E-Posta:</strong> <?= htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <!-- Satın Alınan Biletler -->
        <div class="card">
            <div class="card-header">
                <h4>Satın Alınan Biletler</h4>
            </div>
            <div class="card-body">
                <?php if (count($tickets) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($tickets as $ticket): ?>
                            <li class="list-group-item">
                                <strong>Bilet Adı:</strong> <?= htmlspecialchars($ticket['event_name']); ?><br>
                                <strong>Tarih:</strong> <?= htmlspecialchars($ticket['event_date']); ?><br>
                                <strong>Fiyat:</strong> <?= htmlspecialchars($ticket['price']); ?> ₺
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Henüz bilet satın almadınız.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer style="margin-top: auto;" class="bg-dark text-white text-center py-4">
        <div class="container">
            <div class="row">
                <!-- Hakkımızda -->
                <div class="col-md-4 mb-3">
                    <h5>Hakkımızda</h5>
                    <p>
                        SSBilet, üniversite öğrencilerinin organize ettiği spor, sahne ve konser etkinliklerinin bilet
                        satış ve alım
                        platformudur. Kampüs içi konserlerden öğrenci topluluklarının sahnelediği tiyatro oyunlarına,
                        spor
                        turnuvalarından özel atölyelere kadar birçok etkinlik için bilet satın alabilir ve unutulmaz
                        anılar
                        biriktirebilirsiniz. Güvenli ödeme sistemimiz ve kullanıcı dostu arayüzümüzle, hem etkinlik
                        düzenleyicilere
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>