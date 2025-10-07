<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // التحقق من صحة البيانات
    if (empty($name) || empty($email) || empty($password)) {
        $message = "جميع الحقول مطلوبة!";
    } elseif ($password !== $confirm_password) {
        $message = "كلمة المرور غير متطابقة!";
    } elseif (strlen($password) < 6) {
        $message = "كلمة المرور يجب أن تكون 6 أحرف على الأقل!";
    } else {
        // التحقق من وجود البريد الإلكتروني
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "البريد الإلكتروني مستخدم بالفعل!";
        } else {
            // تسجيل المستخدم الجديد
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.";
            } else {
                $message = "حدث خطأ أثناء إنشاء الحساب!";
            }
        }
    }
}

// تحديد عنوان الصفحة
$page_title = "تسجيل جديد - SR-Techno";

// بناء محتوى الصفحة
ob_start();
?>

<div class="form-container">
    <h2>تسجيل حساب جديد</h2>

    <?php if($message != ""): ?>
        <div class="message <?php echo strpos($message, 'نجح') !== false ? '' : 'error-message'; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">الاسم الكامل</label>
        <input type="text" id="name" name="name" placeholder="أدخل اسمك الكامل" required>

        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" placeholder="example@gmail.com" required>

        <label for="password">كلمة المرور</label>
        <input type="password" id="password" name="password" placeholder="********" required>

        <label for="confirm_password">تأكيد كلمة المرور</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="********" required>

        <button type="submit">إنشاء الحساب</button>
    </form>
    
    <div class="link">
        <p>لديك حساب بالفعل؟ <a href="login.php">سجل الدخول</a></p>
    </div>
</div>

<?php
$page_content = ob_get_clean();

// تضمين ملف index.php
include 'index.php';
?>