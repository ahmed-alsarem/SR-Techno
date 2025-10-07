<?php
session_start();
include "db.php";

// التحقق إذا المستخدم مسجل
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// جلب محتويات السلة
$cart_result = $conn->query("
    SELECT c.id AS cart_id, p.name, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

if($cart_result->num_rows == 0){
    $message = "السلة فارغة! أضف بعض المنتجات أولاً.";
}

// عند إرسال النموذج لإتمام الدفع
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['name'], $_POST['address'], $_POST['phone'])){
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    // يمكن هنا إضافة عملية حفظ الطلب في جدول orders (مستقبلاً)
    // ثم مسح محتويات السلة بعد الدفع
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");
    $message = "تمت عملية الدفع بنجاح، شكرًا لطلبك $name!";
}

// تحديد عنوان الصفحة
$page_title = "الدفع - SR-Techno";

// بناء محتوى الصفحة
ob_start();
?>

<div class="checkout-container">
    <h1>الدفع</h1>

    <?php if($message != ""): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if($cart_result->num_rows > 0): ?>
    <table class="cart-table">
        <tr>
            <th>الصورة</th>
            <th>المنتج</th>
            <th>السعر</th>
            <th>الكمية</th>
            <th>المجموع</th>
        </tr>
        <?php
        $total_price = 0;
        while($row = $cart_result->fetch_assoc()):
            $subtotal = $row['price'] * $row['quantity'];
            $total_price += $subtotal;
        ?>
        <tr>
            <td>
                <?php if($row['image'] == 'placeholder' || empty($row['image'])): ?>
                    <div class="placeholder-image product-<?php echo $row['cart_id']; ?>" style="width:50px;height:50px;margin:auto;">
                        <div class="placeholder-text" style="font-size:0.6rem;"><?php echo $row['name']; ?></div>
                    </div>
                <?php else: ?>
                    <img src="<?php echo $row['image']; ?>" width="50" alt="<?php echo $row['name']; ?>">
                <?php endif; ?>
            </td>
            <td><?php echo $row['name']; ?></td>
            <td>$<?php echo $row['price']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td>$<?php echo $subtotal; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="total">الإجمالي الكلي: $<?php echo number_format($total_price, 2); ?></div>

    <h2>بيانات العميل</h2>
    <form method="POST" action="" class="checkout-form">
        <label for="name">الاسم الكامل</label>
        <input type="text" name="name" id="name" required placeholder="أدخل اسمك الكامل">

        <label for="address">العنوان</label>
        <textarea name="address" id="address" required placeholder="أدخل عنوانك"></textarea>

        <label for="phone">رقم الهاتف</label>
        <input type="text" name="phone" id="phone" required placeholder="أدخل رقم هاتفك">

        <button type="submit">إتمام الدفع</button>
    </form>

    <?php else: ?>
    <div class="empty-cart">
        <h2>السلة فارغة!</h2>
        <p>لم تقم بإضافة أي منتجات للسلة بعد.</p>
        <a href="product.php">تصفح المنتجات</a>
    </div>
    <?php endif; ?>
</div>

<?php
$page_content = ob_get_clean();

// تضمين ملف index.php
include 'index.php';
?>