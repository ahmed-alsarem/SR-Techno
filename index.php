<?php
// التحقق من وجود الجلسة
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

// جلب اسم المستخدم
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "زائر";
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'SR-Techno'; ?></title>
    
    <!-- Global Styles -->
    <link rel="stylesheet" href="styles.css">
    
    <?php if(isset($additional_css)): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>
    
    <style>
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<?php include 'navigation.php'; ?>

<!-- Page Content -->
<main>
    <?php
    // عرض محتوى الصفحة
    if(isset($page_content)) {
        echo $page_content;
    } else {
        // إذا لم يتم تحديد محتوى، عرض رسالة خطأ 404
        echo '<div class="container" style="text-align: center; padding: 4rem 0;">';
        echo '<h1 style="color: #dc3545; font-size: 4rem; margin-bottom: 1rem;">404</h1>';
        echo '<h2 style="color: #1E3A8A; margin-bottom: 1rem;">الصفحة غير موجودة</h2>';
        echo '<p style="color: #6c757d; margin-bottom: 2rem;">عذراً، الصفحة التي تبحث عنها غير موجودة.</p>';
        echo '<a href="home.php" style="background: #1E3A8A; color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: bold;">العودة للرئيسية</a>';
        echo '</div>';
    }
    ?>
</main>

<?php include 'footer.php'; ?>

<?php if(isset($additional_js)): ?>
    <?php echo $additional_js; ?>
<?php endif; ?>

</body>
</html>
