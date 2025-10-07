<?php
session_start();
include "db.php";

// تحديد عنوان الصفحة
$page_title = "المنتجات - SR-Techno";

// بناء محتوى الصفحة
ob_start();
?>

<!-- Page Title -->
<h2 class="page-title">منتجاتنا</h2>

<!-- Filter Buttons -->
<div class="filter-btns">
    <button class="active" onclick="filterProducts('all')">الكل</button>
    <button onclick="filterProducts('electronics')">إلكترونيات</button>
    <button onclick="filterProducts('clothes')">ملابس</button>
    <button onclick="filterProducts('home')">أدوات منزلية</button>
</div>

<!-- Products Grid -->
<div class="product-grid">
    <?php
    // جلب المنتجات من قاعدة البيانات
    $products_result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
    if($products_result && $products_result->num_rows > 0){
        while($product = $products_result->fetch_assoc()){
            $category = 'electronics'; // يمكن تحديد الفئة بناءً على نوع المنتج
            echo "<div class='product-card' data-category='$category'>";
            
            // استخدام placeholder image
            if($product['image'] == 'placeholder' || empty($product['image'])) {
                echo "<div class='placeholder-image product-".$product['id']."' style='width:100%;height:200px;'>";
                echo "<div class='placeholder-text'>".$product['name']."</div>";
                echo "</div>";
            } else {
                echo "<img src='".$product['image']."' alt='".$product['name']."'>";
            }
            
            echo "<div class='info'>";
            echo "<h3>".$product['name']."</h3>";
            echo "<p>".($product['description'] ? $product['description'] : 'وصف قصير للمنتج')."</p>";
            echo "<div class='price-btn'>";
            echo "<span class='price'>$".$product['price']."</span>";
            
            if(isset($_SESSION['user_id'])){
                echo "<form action='cart.php' method='POST'>";
                echo "<input type='hidden' name='product_id' value='".$product['id']."'>";
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

<?php
$page_content = ob_get_clean();

// إضافة JavaScript للفلترة
$additional_js = '
<script>
// فلترة المنتجات
function filterProducts(category) {
    const cards = document.querySelectorAll(".product-card");
    cards.forEach(card => {
        if(category === "all" || card.dataset.category === category) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });

    // تفعيل الزر النشط
    const buttons = document.querySelectorAll(".filter-btns button");
    buttons.forEach(btn => btn.classList.remove("active"));
    event.target.classList.add("active");
}
</script>';

// تضمين ملف index.php
include 'index.php';
?>