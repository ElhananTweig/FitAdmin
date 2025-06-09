<?php
/**
 * דף כניסה למערכת CRM
 * Template Name: Login Page
 */

// אם המשתמש כבר מחובר, הפנה אותו לדשבורד
if (crm_is_user_logged_in()) {
    wp_redirect(home_url('/crm-dashboard/'));
    exit;
}

// קבלת פרמטרים מה-URL
$error = isset($_GET['error']) ? sanitize_text_field($_GET['error']) : '';
$redirect_to = isset($_GET['redirect_to']) ? sanitize_text_field($_GET['redirect_to']) : home_url('/crm-dashboard/');
$logged_out = isset($_GET['logged_out']) ? true : false;

get_header(); ?>

<div class="crm-login-container">
    <div class="login-wrapper">
        <div class="login-form-container">
            <div class="login-header">
                <h1>🏢 כניסה למערכת CRM</h1>
                <p>היכנס עם הפרטים שלך כדי לגשת למערכת הניהול</p>
            </div>

            <?php if ($logged_out): ?>
                <div class="login-message success">
                    <span class="icon">✅</span>
                    <span>יצאת בהצלחה מהמערכת</span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="login-message error">
                    <span class="icon">❌</span>
                    <span><?php echo esc_html($error); ?></span>
                </div>
            <?php endif; ?>

            <form id="crm-login-form" method="post" action="">
                <?php wp_nonce_field('crm_login_action', 'crm_login_nonce'); ?>
                <input type="hidden" name="crm_login_action" value="1">
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">

                <div class="form-group">
                    <label for="username">שם משתמש או אימייל</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" id="username" name="username" required 
                               placeholder="הכנס שם משתמש או אימייל" autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">סיסמה</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password" required 
                               placeholder="הכנס סיסמה" autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <span id="toggle-icon">👁️</span>
                        </button>
                    </div>
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <span class="checkmark"></span>
                        זכור אותי
                    </label>
                </div>

                <button type="submit" class="login-btn" id="login-submit">
                    <span class="btn-text">התחברות</span>
                    <span class="btn-loader hidden">⏳</span>
                </button>
            </form>

            <div class="login-footer">
                <p>נתקלת בבעיה? <a href="<?php echo wp_lostpassword_url(); ?>">שחזר סיסמה</a></p>
                <div class="system-info">
                    <small>מערכת CRM לניהול מתאמנות ומנטוריות</small>
                </div>
            </div>
        </div>

        <div class="login-info-panel">
            <div class="info-content">
                <h2>🎯 מערכת CRM מתקדמת</h2>
                <div class="features-list">
                    <div class="feature">
                        <span class="feature-icon">👥</span>
                        <div>
                            <h4>ניהול מתאמנות</h4>
                            <p>מעקב מלא אחר התקדמות וביצועים</p>
                        </div>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">👩‍🏫</span>
                        <div>
                            <h4>ניהול מנטוריות</h4>
                            <p>תיאום וניהול הצוות המקצועי</p>
                        </div>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">📊</span>
                        <div>
                            <h4>דוחות ואנליטיקס</h4>
                            <p>ניתוח נתונים ותובנות עסקיות</p>
                        </div>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">💳</span>
                        <div>
                            <h4>ניהול תשלומים</h4>
                            <p>מעקב הכנסות ותזרים מזומנים</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.crm-login-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    direction: rtl;
}

.login-wrapper {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 1000px;
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 600px;
}

.login-form-container {
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-header {
    text-align: center;
    margin-bottom: 40px;
}

.login-header h1 {
    color: #1f2937;
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.login-header p {
    color: #6b7280;
    font-size: 1.1rem;
}

.login-message {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.login-message.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.login-message.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 1rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    right: 15px;
    font-size: 1.2rem;
    z-index: 2;
}

.input-wrapper input {
    width: 100%;
    padding: 15px 50px 15px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.input-wrapper input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.password-toggle {
    position: absolute;
    left: 15px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    z-index: 2;
}

.checkbox-group {
    display: flex;
    align-items: center;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.95rem;
    color: #4b5563;
}

.checkbox-label input[type="checkbox"] {
    margin-left: 8px;
    width: 18px;
    height: 18px;
}

.login-btn {
    width: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 15px;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.login-btn:active {
    transform: translateY(0);
}

.btn-loader.hidden {
    display: none;
}

.login-footer {
    text-align: center;
    margin-top: 30px;
}

.login-footer a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.login-footer a:hover {
    text-decoration: underline;
}

.system-info {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.system-info small {
    color: #9ca3af;
}

.login-info-panel {
    background: linear-gradient(135deg, #1f2937, #374151);
    color: white;
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.info-content h2 {
    font-size: 2rem;
    margin-bottom: 30px;
    text-align: center;
}

.features-list {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.feature {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.feature-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.feature h4 {
    margin: 0 0 5px 0;
    font-size: 1.2rem;
}

.feature p {
    margin: 0;
    color: #d1d5db;
    font-size: 0.95rem;
}

@media (max-width: 768px) {
    .login-wrapper {
        grid-template-columns: 1fr;
        max-width: 400px;
    }
    
    .login-info-panel {
        display: none;
    }
    
    .login-form-container {
        padding: 40px 30px;
    }
    
    .login-header h1 {
        font-size: 2rem;
    }
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggle-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = '👁️';
    }
}

// טיפול בשליחת הטופס
document.getElementById('crm-login-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('login-submit');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    submitBtn.disabled = true;
});

// אפקטים ויזואליים
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
});
</script>

<?php get_footer(); ?> 