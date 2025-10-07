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

// إضافة منتج جديد للسلة
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['product_id'])){
    $product_id = intval($_POST['product_id']);

    // التحقق إذا المنتج موجود بالفعل في السلة
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        // زيادة الكمية بمقدار 1
        $stmt->bind_result($cart_id, $quantity);
        $stmt->fetch();
        $new_quantity = $quantity + 1;
        $update = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
        $update->bind_param("ii", $new_quantity, $cart_id);
        $update->execute();
    } else {
        // إضافة المنتج للسلة
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
    }
    header("Location: cart.php");
    exit;
}

// تعديل الكمية أو حذف المنتجات
if(isset($_POST['update_cart'])){
    foreach($_POST['quantities'] as $cart_id => $quantity){
        $quantity = intval($quantity);
        if($quantity > 0){
            $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
        }
    }
    $message = "تم تحديث السلة بنجاح!";
}

// حذف منتج من السلة
if(isset($_POST['remove_item'])){
    $cart_id = intval($_POST['remove_item']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $message = "تم حذف المنتج من السلة!";
    header("Location: cart.php");
    exit;
}

// جلب محتويات السلة
$result = $conn->query("
    SELECT c.id AS cart_id, p.name, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

// تحديد عنوان الصفحة
$page_title = "السلة - SR-Techno";

// بناء محتوى الصفحة
ob_start();
?>

<h2 class="page-title">سلة مشترياتك</h2>

<?php if($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<div class="cart-container">
    <table>
        <tr>
            <th>صورة</th>
            <th>المنتج</th>
            <th>السعر</th>
            <th>الكمية</th>
            <th>الإجمالي</th>
            <th>حذف</th>
        </tr>
        <?php
        $total = 0;
        if($result && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;
                echo "<tr>";
                echo "<td>";
                // استخدام placeholder image
                if($row['image'] == 'placeholder' || empty($row['image'])) {
                    echo "<div class='placeholder-image product-".$row['cart_id']."' style='width:80px;height:80px;margin:auto;'>";
                    echo "<div class='placeholder-text' style='font-size:0.7rem;'>".$row['name']."</div>";
                    echo "</div>";
                } else {
                    echo "<img src='".$row['image']."' alt='".$row['name']."'>";
                }
                echo "</td>";
                echo "<td>".$row['name']."</td>";
                echo "<td>$".$row['price']."</td>";
                echo "<td>".$row['quantity']."</td>";
                echo "<td>$".$subtotal."</td>";
                echo "<td><button onclick='removeFromCart(".$row['cart_id'].")'>حذف</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align:center;padding:2rem;'>السلة فارغة</td></tr>";
        }
        ?>
    </table>
    <div class="total">المجموع الكلي: $<?php echo number_format($total, 2); ?></div>
    <button class="checkout-btn" onclick="location.href='checkout.php'" style="display:block;margin:2rem auto;padding:0.8rem 2rem;background:#1E3A8A;color:white;font-weight:bold;border:none;border-radius:8px;cursor:pointer;transition:0.3s;">الدفع الآن</button>
</div>

<?php
$page_content = ob_get_clean();

// إضافة JavaScript لحذف المنتجات
$additional_js = '
<script>
function removeFromCart(cartId) {
    if(confirm("هل أنت متأكد من حذف هذا المنتج من السلة؟")) {
        // Create a form to submit the delete request
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "cart.php";
        
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "remove_item";
        input.value = cartId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>';

// تضمين ملف index.php
include 'index.php';
?>