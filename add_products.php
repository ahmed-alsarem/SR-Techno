<?php
session_start();
include "db.php";

// التحقق من تسجيل الدخول
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$message = "";
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// معالجة العمليات
if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_POST['add_product'])){
        $name = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);
        $image_url = trim($_POST['image_url']);
        
        // معالجة الصورة
        $image = 'placeholder';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $upload_dir = 'uploads/';
            if(!file_exists($upload_dir)){
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if(in_array($file_extension, $allowed_extensions)){
                $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)){
                    $image = $upload_path;
                } else {
                    $message = "فشل في رفع الصورة!";
                }
            } else {
                $message = "نوع الملف غير مدعوم!";
            }
        } elseif(!empty($image_url)){
            if(filter_var($image_url, FILTER_VALIDATE_URL)){
                $image = $image_url;
            } else {
                $message = "رابط الصورة غير صحيح!";
            }
        }
        
        if(!empty($name) && $price > 0 && empty($message)){
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, category, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsss", $name, $price, $description, $category, $image);
            if($stmt->execute()){
                $message = "تم إضافة المنتج بنجاح!";
            } else {
                $message = "حدث خطأ أثناء إضافة المنتج!";
            }
        } elseif(empty($message)) {
            $message = "يرجى ملء جميع الحقول بشكل صحيح!";
        }
    }
    
    if(isset($_POST['edit_product'])){
        $name = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);
        $product_id = intval($_POST['product_id']);
        $image_url = trim($_POST['image_url']);
        
        $image = null;
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $upload_dir = 'uploads/';
            if(!file_exists($upload_dir)){
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if(in_array($file_extension, $allowed_extensions)){
                $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)){
                    $image = $upload_path;
                } else {
                    $message = "فشل في رفع الصورة!";
                }
            } else {
                $message = "نوع الملف غير مدعوم!";
            }
        } elseif(!empty($image_url)){
            if(filter_var($image_url, FILTER_VALIDATE_URL)){
                $image = $image_url;
            } else {
                $message = "رابط الصورة غير صحيح!";
            }
        }
        
        if(!empty($name) && $price > 0 && $product_id > 0 && empty($message)){
            if($image !== null){
                $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, category=?, image=? WHERE id=?");
                $stmt->bind_param("sdsssi", $name, $price, $description, $category, $image, $product_id);
            } else {
                $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, category=? WHERE id=?");
                $stmt->bind_param("sdssi", $name, $price, $description, $category, $product_id);
            }
            
            if($stmt->execute()){
                $message = "تم تحديث المنتج بنجاح!";
            } else {
                $message = "حدث خطأ أثناء تحديث المنتج!";
            }
        } elseif(empty($message)) {
            $message = "يرجى ملء جميع الحقول بشكل صحيح!";
        }
    }
}

// حذف منتج
if(isset($_GET['delete']) && $product_id > 0){
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    if($stmt->execute()){
        $message = "تم حذف المنتج بنجاح!";
    } else {
        $message = "حدث خطأ أثناء حذف المنتج!";
    }
}

// جلب بيانات منتج للتعديل
$edit_product = null;
if($action == 'edit' && $product_id > 0){
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_product = $result->fetch_assoc();
}

// جلب جميع المنتجات
$products_result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

// تحديد عنوان الصفحة
$page_title = "إدارة المنتجات - SR-Techno";

// بناء محتوى الصفحة
ob_start();
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>إدارة المنتجات</h1>
        <div class="admin-actions">
            <a href="?action=add" class="btn btn-primary">إضافة منتج جديد</a>
            <a href="home.php" class="btn btn-secondary">العودة للموقع</a>
        </div>
    </div>

    <?php if($message): ?>
        <div class="message <?php 
            if(strpos($message, 'حذف') !== false) {
                echo 'error'; // رسائل الحذف باللون الأحمر
            } elseif(strpos($message, 'نجح') !== false || strpos($message, 'تم إضافة') !== false || strpos($message, 'تم تحديث') !== false) {
                echo 'success'; // رسائل النجاح باللون الأخضر
            } else {
                echo 'error'; // باقي الرسائل باللون الأحمر
            }
        ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if($action == 'add' || $action == 'edit'): ?>
    <!-- نموذج إضافة/تعديل منتج -->
    <div class="admin-form">
        <h2><?php echo $action == 'add' ? 'إضافة منتج جديد' : 'تعديل المنتج'; ?></h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <?php if($action == 'edit'): ?>
                <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">اسم المنتج</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="price">السعر ($)</label>
                <input type="number" id="price" name="price" step="0.01" min="0"
                       value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="description">الوصف</label>
                <textarea id="description" name="description" rows="4"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="category">الفئة</label>
                <select id="category" name="category" required>
                    <option value="">اختر الفئة</option>
                    <option value="electronics" <?php echo ($edit_product && $edit_product['category'] == 'electronics') ? 'selected' : ''; ?>>إلكترونيات</option>
                    <option value="clothes" <?php echo ($edit_product && $edit_product['category'] == 'clothes') ? 'selected' : ''; ?>>ملابس</option>
                    <option value="home" <?php echo ($edit_product && $edit_product['category'] == 'home') ? 'selected' : ''; ?>>أدوات منزلية</option>
                    <option value="sports" <?php echo ($edit_product && $edit_product['category'] == 'sports') ? 'selected' : ''; ?>>رياضة</option>
                    <option value="books" <?php echo ($edit_product && $edit_product['category'] == 'books') ? 'selected' : ''; ?>>كتب</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>صورة المنتج</label>
                <div class="image-upload-section">
                    <div class="upload-option">
                        <label for="image" class="upload-label">
                            <i class="fas fa-upload"></i>
                            رفع صورة من الجهاز
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        <small class="upload-info">الأنواع المسموحة: JPG, PNG, GIF, WebP</small>
                    </div>
                    
                    <div class="or-divider">
                        <span>أو</span>
                    </div>
                    
                    <div class="url-option">
                        <label for="image_url">رابط الصورة</label>
                        <input type="url" id="image_url" name="image_url" 
                               placeholder="https://example.com/image.jpg"
                               value="<?php echo $edit_product && $edit_product['image'] != 'placeholder' ? htmlspecialchars($edit_product['image']) : ''; ?>">
                    </div>
                </div>
                
                <?php if($edit_product && $edit_product['image'] != 'placeholder'): ?>
                <div class="current-image">
                    <label>الصورة الحالية:</label>
                    <div class="image-preview">
                        <img src="<?php echo htmlspecialchars($edit_product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($edit_product['name']); ?>"
                             style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 10px;">
                    </div>
                </div>
                <?php endif; ?>
                
                <div id="image-preview" class="image-preview" style="display: none;">
                    <label>معاينة الصورة:</label>
                    <img id="preview-img" src="" alt="معاينة" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 10px;">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="<?php echo $action == 'add' ? 'add_product' : 'edit_product'; ?>" class="btn btn-primary">
                    <?php echo $action == 'add' ? 'إضافة المنتج' : 'تحديث المنتج'; ?>
                </button>
                <a href="admin_products.php" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <!-- قائمة المنتجات -->
    <div class="products-table">
        <h2>جميع المنتجات</h2>
        <table>
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>اسم المنتج</th>
                    <th>السعر</th>
                    <th>الوصف</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if($products_result && $products_result->num_rows > 0): ?>
                    <?php while($product = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="product-image">
                                <?php if($product['image'] == 'placeholder' || empty($product['image'])): ?>
                                    <div class="placeholder-image product-<?php echo $product['id']; ?>" style="width:60px;height:60px;">
                                        <div class="placeholder-text" style="font-size:0.6rem;"><?php echo $product['name']; ?></div>
                                    </div>
                                <?php else: ?>
                                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width:60px;height:60px;object-fit:cover;border-radius:5px;">
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . (strlen($product['description']) > 50 ? '...' : ''); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">تعديل</a>
                                <a href="?delete=1&id=<?php echo $product['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">حذف</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">لا توجد منتجات</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$page_content = ob_get_clean();

// إضافة JavaScript لمعاينة الصور
$additional_js = '
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById("image-preview");
            const previewImg = document.getElementById("preview-img");
            
            previewImg.src = e.target.result;
            preview.style.display = "block";
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>';

// إضافة CSS مخصص للإدارة
$additional_css = '
<style>
.admin-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 2rem;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.admin-header h1 {
    color: #1E3A8A;
    margin: 0;
}

.admin-actions {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-block;
}

.btn-primary {
    background: #1E3A8A;
    color: white;
}

.btn-primary:hover {
    background: #2563EB;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-warning {
    background: #f59e0b;
    color: white;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-danger {
    background: #dc2626;
    color: white;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.admin-form {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #1E3A8A;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #1E3A8A;
    box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.products-table {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.products-table table {
    width: 100%;
    border-collapse: collapse;
}

.products-table th,
.products-table td {
    padding: 1rem;
    text-align: right;
    border-bottom: 1px solid #e5e7eb;
}

.products-table th {
    background: #f8fafc;
    font-weight: bold;
    color: #1E3A8A;
}

.products-table tr:hover {
    background: #f8fafc;
}

.product-image {
    display: flex;
    justify-content: center;
    align-items: center;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.message {
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 1rem;
    font-weight: bold;
}

.message.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.message.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.image-upload-section {
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    padding: 1.5rem;
    background: #f9fafb;
}

.upload-option {
    text-align: center;
    margin-bottom: 1rem;
}

.upload-label {
    display: inline-block;
    padding: 1rem 2rem;
    background: #1E3A8A;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: bold;
}

.upload-label:hover {
    background: #2563EB;
    transform: translateY(-2px);
}

.upload-label i {
    margin-left: 0.5rem;
}

#image {
    display: none;
}

.upload-info {
    display: block;
    margin-top: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.or-divider {
    text-align: center;
    margin: 1rem 0;
    position: relative;
}

.or-divider::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #d1d5db;
}

.or-divider span {
    background: #f9fafb;
    padding: 0 1rem;
    color: #6b7280;
    font-weight: bold;
}

.url-option {
    margin-top: 1rem;
}

.url-option label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #1E3A8A;
}

.url-option input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    font-size: 1rem;
}

.url-option input:focus {
    outline: none;
    border-color: #1E3A8A;
    box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
}

.current-image {
    margin-top: 1rem;
    padding: 1rem;
    background: #f0f9ff;
    border-radius: 8px;
    border: 1px solid #bae6fd;
}

.current-image label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #0369a1;
}

.image-preview {
    text-align: center;
}

.image-preview label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #1E3A8A;
}

.image-preview img {
    border: 2px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .admin-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .products-table {
        overflow-x: auto;
    }
    
    .products-table table {
        min-width: 600px;
    }
    
    .upload-label {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
    }
}
</style>';

// تضمين ملف index.php
include 'index.php';
?>