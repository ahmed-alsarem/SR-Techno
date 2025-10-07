<?php
session_start();
include "db.php";

// تحديد عنوان الصفحة
$page_title = "متجر SR-Techno";

// جلب بعض المنتجات المميزة
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");

// بناء محتوى الصفحة
ob_start();
?>

<!-- Hero Banner -->
<section class="hero">
    <div>
        <h2>أفضل العروض في متجرنا!</h2>
        <p>استمتع بأحدث المنتجات بأفضل الأسعار</p>
        <a href="product.php" class="btn">تسوق الآن</a>
    </div>
</section>

<!-- Featured Products -->
<section class="products">
    <h2>منتجات مميزة</h2>
    <div class="product-grid">
        <?php
        if($result && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "<div class='product-card'>";
                // استخدام placeholder image
                if($row['image'] == 'placeholder' || empty($row['image'])) {
                    echo "<div class='placeholder-image product-".$row['id']."' style='width:100%;height:200px;'>";
                    echo "<div class='placeholder-text'>".$row['name']."</div>";
                    echo "</div>";
                } else {
                    echo "<img src='".$row['image']."' alt='".$row['name']."'>";
                }
                echo "<div class='info'>";
                echo "<h3>".$row['name']."</h3>";
                echo "<p>".($row['description'] ? $row['description'] : 'وصف قصير للمنتج')."</p>";
                echo "<div class='price-btn'>";
                echo "<span class='price'>$".$row['price']."</span>";
                if(isset($_SESSION['user_id'])){
                    echo "<form action='cart.php' method='POST' style='display:inline;'>";
                    echo "<input type='hidden' name='product_id' value='".$row['id']."'>";
                    echo "<button type='submit'>أضف للسلة</button>";
                    echo "</form>";
                } else {
                    echo "<a href='login.php' style='background:#1E3A8A;color:white;padding:0.5rem 1rem;border-radius:5px;text-decoration:none;'>تسجيل الدخول</a>";
                }
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<div style='grid-column:1/-1;text-align:center;padding:2rem;'>لا توجد منتجات متاحة حالياً</div>";
        }
        ?>
    </div>
</section>

<!-- Daily Deals -->
<section class="daily-deals">
    <h2>عروض اليوم</h2>
    <p>اغتنم الفرصة الآن مع خصومات مذهلة!</p>
    <a href="product.php">اكتشف العروض</a>
</section>

<?php
$page_content = ob_get_clean();

// تضمين ملف index.php
include 'index.php';
?>