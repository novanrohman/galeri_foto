<?php
error_reporting(0);
include 'db.php';

// Check if the user is logged in
session_start();
$userLoggedIn = isset($_SESSION['a_global']);

$kontak = mysqli_query($conn, "SELECT admin_telp, admin_email, admin_address FROM tb_admin WHERE admin_id = 2");
$a = mysqli_fetch_object($kontak);

$produk = mysqli_query($conn, "SELECT * FROM tb_image WHERE image_id = '" . $_GET['id'] . "' ");
$p = mysqli_fetch_object($produk);

// Handle like and unlike
if ($userLoggedIn && isset($_POST['like'])) {
    $imageId = $_GET['id'];
    $admin_name = $_SESSION['a_global']->admin_name;

    // Check if the user already liked the image
    if ($userLoggedIn) {
        $checkLiked = mysqli_query($conn, "SELECT * FROM tb_like WHERE image_id = '" . $_GET['id'] . "' AND admin_name = '" . $_SESSION['a_global']->admin_name . "'");
        $isLiked = (mysqli_num_rows($checkLiked) > 0);
    } else {
        $isLiked = false;
    }

    if (mysqli_num_rows($checkLiked) > 0) {
        // Unlike if already liked
        mysqli_query($conn, "DELETE FROM tb_like WHERE image_id = '$imageId' AND admin_name = '$admin_name'");
    } else {
        // Like if not liked
        mysqli_query($conn, "INSERT INTO tb_like (image_id, admin_name) VALUES ('$imageId', '$admin_name')");
    }
}

// Get the total likes for the image
$qt = mysqli_query($conn, "SELECT COUNT(*) AS total_likes FROM tb_like WHERE image_id = '" . $_GET['id'] . "'");
$totalLikes = (mysqli_num_rows($qt) > 0) ? mysqli_fetch_array($qt)['total_likes'] : 0;

// Handle comments
if ($userLoggedIn && isset($_POST['submit_comment'])) {
    $image_id = $_GET['id'];
    $admin_id = $_SESSION['a_global']->admin_id;
    $admin_name = $_SESSION['a_global']->admin_name;
    $isi_komentar = mysqli_real_escape_string($conn, $_POST['komentar']);

    mysqli_query($conn, "INSERT INTO komentar_foto (image_id, admin_id, admin_name, isi_komentar) VALUES ('$image_id', '$admin_id','$admin_name', '$isi_komentar')");
}

// Get comments for the image
$commentQuery = mysqli_query($conn, "SELECT * FROM komentar_foto WHERE image_id = '" . $_GET['id'] . "'");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WEB Galeri Foto</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <!-- header -->
    <header>
        <div class="container">
        <h1><a href="index.php">WEB GALERI FOTO</a></h1>
        <ul>
            <li><a href="galeri.php">Galeri</a></li>
           <li><a href="registrasi.php">Registrasi</a></li>
           <li><a href="login.php">Login</a></li>
        </ul>
        </div>
    </header>

    <!-- search -->
    <div class="search">
        <div class="container">
            <form action="galeri.php">
                <input type="text" name="search" placeholder="Cari Foto" value="<?php echo $_GET['search'] ?>" />
                <input type="hidden" name="kat" value="<?php echo $_GET['kat'] ?>" />
                <input type="submit" name="cari" value="Cari Foto" />
            </form>
        </div>
    </div>

    <!-- product detail -->
    <div class="section">
        <div class="container">
            <h3>Detail Foto</h3>
            <div class="box">
                <div class="col-2">
                    <img src="foto/<?php echo $p->image ?>" width="100%" />
                </div>
                <div class="col-2">
                    <h3><?php echo $p->image_name ?><br />Kategori : <?php echo $p->category_name  ?></h3>
                    <h4>Nama User : <?php echo $p->admin_name ?><br />
                        Upload Pada Tanggal : <?php echo $p->date_created  ?></h4>
                    <p>Deskripsi :<br />
                        <?php echo $p->image_description ?>
                    </p>
                </div>
            </div>

            <div class="col-7">
                <div class="content">
                    <!-- Like Button -->
                    <?php if ($userLoggedIn) : ?>
                        <form action="" method="POST">
                            <button type="submit" name="like" class="like" id="like-btn">
                                <?php if ($isLiked == true) : ?>
                                    <i class="bi bi-suit-heart-fill"></i> 
                                <?php else : ?>
                                    <i class="bi bi-suit-heart"></i> 
                                <?php endif; ?>
                                <?php echo $totalLikes ?>
                            </button>
                        </form>
                    <?php else : ?>
                        <button class="like" onclick="showLoginAlert()">
                            <i class="bi bi-suit-heart"></i> Like
                        </button>
                    <?php endif; ?>

                    <!-- Comment Form -->
                    
                        <form action="" method="POST">
                            <input type="hidden" name="adminid" value="<?php echo $_SESSION['a_global']->admin_id ?>">
                            <textarea name="komentar" id="" type="text" cols="300" class="input-control" placeholder="Tulis Komentar..." required></textarea>
                            <input type="submit" onclick="showLoginAlert()" name="submit_comment" value="Kirim Komentar" class="btn">
                        </form>
                   

                    <!-- Display Comments -->
                    <h4>Komentar:</h4>
                    <?php while ($comment = mysqli_fetch_object($commentQuery)) : ?>
                        <p><strong><?php echo $comment->admin_name; ?>:</strong> <?php echo $comment->isi_komentar; ?></p>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2024 - Web Galeri Foto.</small>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Dynamically update the like button appearance
        // $(document).ready(function() {
        //     $("#like-btn").click(function() {
        //         $(this).find("i").toggleClass("bi-suit-heart-fill");
        //         $(this).text(function(i, text) {
        //             return text === "Like" ? "Unlike" : "Like";
        //         });
        //     });
        // });

        // Function to show login alert
        function showLoginAlert() {
            alert("Login Terlebih Dahulu");
            window.location="login.php";
        }
    </script>
</body>

</html>