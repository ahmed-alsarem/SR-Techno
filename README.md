# متجر SR-Techno - دليل التطوير والتعديلات

## نظرة عامة
متجر إلكتروني متكامل مبني بـ PHP و MySQL مع تصميم عصري ومتجاوب.

## المشاكل التي تم حلها والتطويرات

### 1. مشكلة السلة - Foreign Key Constraint Error

#### المشكلة الأصلية:
```
Fatal error: Uncaught mysqli_sql_exception: Cannot add or update a child row: 
a foreign key constraint fails (`sr_techno`.`cart`, CONSTRAINT `cart_ibfk_2` 
FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE)
```

#### السبب:
- جدول `products` كان فارغاً
- جميع المنتجات في `product.php` كانت تستخدم `product_id = 1`
- محاولة إضافة منتجات غير موجودة للسلة

#### الحل:
1. **إضافة منتجات عينة:**
```sql
INSERT INTO products (name, price, image, description) VALUES 
('هاتف ذكي', 300.00, 'placeholder', 'هاتف ذكي متطور'),
('قميص رجالي', 50.00, 'placeholder', 'قميص رجالي أنيق'),
('محمصة خبز', 80.00, 'placeholder', 'محمصة خبز عالية الجودة'),
('سماعة بلوتوث', 60.00, 'placeholder', 'سماعة بلوتوث لاسلكية');
```

2. **تصحيح معرفات المنتجات:**
- المنتج 1: `product_id = 1`
- المنتج 2: `product_id = 2`
- المنتج 3: `product_id = 3`
- المنتج 4: `product_id = 4`

### 2. نظام Placeholder Images

#### المشكلة:
- الصور الخارجية لا تعمل
- مظهر غير احترافي للمنتجات

#### الحل:
1. **إنشاء ملف `placeholder.css`:**
```css
.placeholder-image {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-weight: bold;
}
```

2. **ألوان مختلفة للمنتجات:**
- المنتج 1: أزرق فاتح `#e3f2fd`
- المنتج 2: وردي فاتح `#fce4ec`
- المنتج 3: أخضر فاتح `#e8f5e8`
- المنتج 4: برتقالي فاتح `#fff3e0`

### 3. عرض حالة تسجيل الدخول

#### المشكلة:
- لا يظهر اسم المستخدم في الصفحة الرئيسية
- شريط التنقل لا يعكس حالة المستخدم

#### الحل:
1. **تحديث شريط التنقل:**
```php
<?php if(isset($_SESSION['user_id'])): ?>
    <li><span class="user-info">مرحباً، <?php echo htmlspecialchars($user_name); ?></span></li>
    <li><a href="logout.php" class="logout-btn">تسجيل الخروج</a></li>
<?php else: ?>
    <li><a href="login.php">تسجيل الدخول</a></li>
    <li><a href="register.php">تسجيل جديد</a></li>
<?php endif; ?>
```

### 4. إنشاء نظام Navigation منفصل

#### الهدف:
- تنظيم الكود
- سهولة الصيانة
- تجنب التكرار

#### التنفيذ:
1. **إنشاء `navigation.php`:**
```php
<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "زائر";
?>
<!-- Navigation HTML -->
```

2. **تحديث جميع الصفحات:**
```php
<?php include 'navigation.php'; ?>
```

### 5. توحيد CSS في ملف واحد

#### المشكلة:
- CSS مكرر في كل صفحة
- صعوبة الصيانة
- أحجام ملفات كبيرة

#### الحل:
1. **إنشاء `styles.css` شامل:**
- جميع أنماط الموقع
- تصميم متجاوب
- ألوان موحدة

2. **إزالة CSS المدمج:**
- حذف `<style>` من جميع الصفحات
- استخدام `link rel="stylesheet" href="styles.css"`

### 6. نظام Index.php الموحد

#### الهدف:
- تصميم موحد
- سهولة إضافة صفحات جديدة
- فصل المنطق عن التصميم

#### التنفيذ:
1. **إنشاء `index.php`:**
```php
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <title><?php echo isset($page_title) ? $page_title : 'SR-Techno'; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navigation.php'; ?>
    <main><?php echo $page_content; ?></main>
    <?php include 'footer.php'; ?>
</body>
</html>
```

2. **تحديث الصفحات:**
```php
<?php
$page_title = "عنوان الصفحة";
ob_start();
?>
<!-- محتوى الصفحة -->
<?php
$page_content = ob_get_clean();
include 'index.php';
?>
```

### 7. فوتر محسن ومنفصل

#### المميزات:
- تصميم احترافي مع ألوان متدرجة
- أقسام متعددة (الشركة، الروابط، التواصل)
- تأثيرات تفاعلية
- تصميم متجاوب

#### التنفيذ:
```php
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>SR-Techno</h3>
            <p>متجرك الإلكتروني الموثوق</p>
            <div class="social-links">
                <!-- روابط وسائل التواصل -->
            </div>
        </div>
        <!-- أقسام أخرى -->
    </div>
</footer>
```

### 8. جعل الفوتر ثابت

#### المشكلة:
- مساحات فارغة في الصفحات القصيرة
- مظهر غير احترافي

#### الحل:
```css
html, body { height: 100%; }
body { display: flex; flex-direction: column; }
main { flex: 1; }
footer { margin-top: auto; }
```

### 9. تحسين بطاقات المنتجات

#### التغييرات:
- **شكل مربع**: `grid-template-columns: repeat(auto-fit, minmax(280px, 280px))`
- **توسيط**: `justify-content: center`
- **زوايا مدورة**: `border-radius: 10px 10px 0 0`
- **تصميم متجاوب**: أحجام مختلفة للشاشات

### 10. تحديث العملة

#### التغيير:
- من الريال إلى الدولار
- تنسيق الأرقام: `number_format($price, 2)`
- رمز العملة: `$` قبل كل سعر

## هيكل الملفات النهائي

```
/DataBase/
├── index.php              # القالب الرئيسي
├── navigation.php         # شريط التنقل
├── footer.php            # الفوتر
├── styles.css            # جميع الأنماط
├── db.php               # اتصال قاعدة البيانات
├── home.php             # الصفحة الرئيسية
├── product.php          # صفحة المنتجات
├── cart.php             # صفحة السلة
├── checkout.php         # صفحة الدفع
├── login.php            # تسجيل الدخول
├── register.php         # التسجيل
├── logout.php           # تسجيل الخروج
├── .htaccess           # إعادة التوجيه
└── README.md           # هذا الملف
```

## المميزات النهائية

### 1. التصميم
- ✅ تصميم عصري ومتجاوب
- ✅ ألوان متناسقة ومهنية
- ✅ خطوط واضحة ومقروءة
- ✅ تأثيرات تفاعلية

### 2. الوظائف
- ✅ تسجيل الدخول والتسجيل
- ✅ عرض المنتجات
- ✅ إضافة للسلة
- ✅ عملية دفع
- ✅ إدارة الجلسات

### 3. الأداء
- ✅ ملف CSS واحد
- ✅ كود منظم ومحسن
- ✅ استعلامات قاعدة بيانات محسنة
- ✅ تحميل سريع

### 4. الصيانة
- ✅ ملفات منفصلة ومنظمة
- ✅ كود قابل لإعادة الاستخدام
- ✅ تعليقات واضحة
- ✅ سهولة التطوير

## كيفية الاستخدام

### إضافة صفحة جديدة:
```php
<?php
session_start();
include "db.php";

$page_title = "عنوان الصفحة الجديدة";

ob_start();
?>
<!-- محتوى الصفحة -->
<?php
$page_content = ob_get_clean();
include 'index.php';
?>
```

### إضافة CSS مخصص:
```php
$additional_css = '<link rel="stylesheet" href="custom.css">';
```

### إضافة JavaScript:
```php
$additional_js = '<script>/* كود JavaScript */</script>';
```

## قاعدة البيانات

### الجداول:
1. **users** - المستخدمين
2. **products** - المنتجات
3. **cart** - السلة

### العلاقات:
- `cart.user_id` → `users.id`
- `cart.product_id` → `products.id`

## المتطلبات

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- متصفح حديث

## التطوير المستقبلي

### ميزات مقترحة:
- [ ] نظام إدارة المنتجات
- [ ] نظام الطلبات
- [ ] نظام التقييمات
- [ ] دفع إلكتروني
- [ ] إشعارات
- [ ] تقارير المبيعات



---
**تم التطوير بـ ❤️ ـوسطه احمد الصارم**
