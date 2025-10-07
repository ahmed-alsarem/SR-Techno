<!-- Footer -->
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>SR-Techno</h3>
            <p>متجرك الإلكتروني الموثوق لجميع احتياجاتك التقنية</p>
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        
        <div class="footer-section">
            <h4>روابط سريعة</h4>
            <ul>
                <li><a href="home.php">الرئيسية</a></li>
                <li><a href="product.php">المنتجات</a></li>
                <li><a href="cart.php">السلة</a></li>
                <li><a href="checkout.php">الدفع</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>حسابي</h4>
            <ul>
                <li><a href="login.php">تسجيل الدخول</a></li>
                <li><a href="register.php">إنشاء حساب</a></li>
                <li><a href="logout.php">تسجيل الخروج</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>تواصل معنا</h4>
            <div class="contact-info">
                <p><i class="fas fa-envelope"></i> info@srtechno.com</p>
                <p><i class="fas fa-phone"></i> +967 731133636</p>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2025 SR-Techno. جميع الحقوق محفوظة.</p>
    </div>
</footer>

<style>
/* Footer Styles */
footer {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 3rem 0 1rem 0;
    margin-top: auto;
    position: relative;
    bottom: 0;
    width: 100%;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.footer-section h3 {
    color: #fbbf24;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    font-weight: bold;
}

.footer-section h4 {
    color: #fbbf24;
    font-size: 1.2rem;
    margin-bottom: 1rem;
    font-weight: bold;
}

.footer-section p {
    line-height: 1.6;
    margin-bottom: 1rem;
    color: #e5e7eb;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section ul li a {
    color: #e5e7eb;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-section ul li a:hover {
    color: #fbbf24;
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: all 0.3s;
    font-size: 1.2rem;
}

.social-link:hover {
    background: #fbbf24;
    color: #1e3a8a;
    transform: translateY(-2px);
}

.contact-info p {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    color: #e5e7eb;
}

.contact-info i {
    margin-left: 0.5rem;
    color: #fbbf24;
    width: 20px;
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 2rem;
    padding-top: 1rem;
    text-align: center;
    color: #e5e7eb;
}

.footer-bottom p {
    margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .contact-info p {
        justify-content: center;
    }
}
</style>
