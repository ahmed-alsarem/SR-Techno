<?php
// التحقق من وجود الجلسة
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

// جلب اسم المستخدم
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "زائر";
?>

<!-- Global Styles -->
<link rel="stylesheet" href="styles.css">

<!-- Navigation -->
<nav>
    <div class="container">
        <h1><a href="home.php" style="text-decoration: none; color: inherit;">SR-Techno</a></h1>
        <ul>
            <li><a href="home.php">الرئيسية</a></li>
            <li><a href="product.php">المنتجات</a></li>
            <li><a href="cart.php">السلة</a></li>
            <li><a href="checkout.php">الدفع</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><span class="user-info">مرحباً، <?php echo htmlspecialchars($user_name); ?></span></li>
                <li><a href="logout.php" class="logout-btn">تسجيل الخروج</a></li>
            <?php else: ?>
                <li><a href="login.php">تسجيل الدخول</a></li>
                <li><a href="register.php">تسجيل جديد</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
