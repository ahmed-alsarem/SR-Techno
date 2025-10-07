<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // التحقق من البريد في قاعدة البيانات
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // تسجيل الجلسة
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            header("Location: home.php");
            exit;
        } else {
            $message = "كلمة المرور غير صحيحة!";
        }
    } else {
        $message = "البريد الإلكتروني غير موجود!";
    }
}

// تحديد عنوان الصفحة
$page_title = "تسجيل الدخول - SR-Techno";

// بناء محتوى الصفحة
ob_start();
?>

<div class="form-container">
    <h2>تسجيل الدخول</h2>

    <?php if($message != ""): ?>
        <div class="message error-message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" placeholder="example@gmail.com" required>

        <label for="password">كلمة المرور</label>
        <input type="password" id="password" name="password" placeholder="********" required>

        <button type="submit">تسجيل الدخول</button>
    </form>
    
    <div class="link">
        <p>ليس لديك حساب؟ <a href="register.php">سجل الآن</a></p>
    </div>
</div>

<?php
$page_content = ob_get_clean();

// تضمين ملف index.php
include 'index.php';
?>