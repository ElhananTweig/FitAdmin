<?php
/**
 * דף הוספת מנטורית חדשה
 * Template Name: Add Mentor
 */

// בדיקת אימות
global $crm_auth;
if ($crm_auth && !$crm_auth->is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

// בדיקה אם זה עריכה של מנטורית קיימת
$edit_mentor_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$is_edit = $edit_mentor_id > 0;

// טעינת נתוני המנטורית אם זה עריכה
$mentor_data = array();
if ($is_edit) {
    $mentor_post = get_post($edit_mentor_id);
    if ($mentor_post && $mentor_post->post_type === 'mentors') {
        $mentor_data = array(
            'mentor_first_name' => get_field('mentor_first_name', $edit_mentor_id),
            'mentor_last_name' => get_field('mentor_last_name', $edit_mentor_id),
            'mentor_phone' => get_field('mentor_phone', $edit_mentor_id),
            'mentor_email' => get_field('mentor_email', $edit_mentor_id),
            'payment_percentage' => get_field('payment_percentage', $edit_mentor_id),
            'mentor_notes' => get_field('mentor_notes', $edit_mentor_id)
        );
    } else {
        $is_edit = false;
        $edit_mentor_id = 0;
    }
}

get_header(); ?>

<div class="crm-form-container">
    <div class="form-header">
        <div class="container">
            <div class="header-content">
                <div class="header-info">
                    <h1><?php echo $is_edit ? '📝 עריכת מנטורית' : '👩‍🏫 הוספת מנטורית חדשה'; ?></h1>
                    <p><?php echo $is_edit ? 'עדכן את פרטי המנטורית' : 'הכנס את פרטי המנטורית החדשה למערכת'; ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo home_url('/mentors/'); ?>" class="back-btn">
                        <span class="icon">←</span>
                        <span>חזרה לרשימה</span>
                    </a>
                    <a href="<?php echo home_url('/crm-dashboard/'); ?>" class="dashboard-btn">
                        <span class="icon">🏢</span>
                        <span>דשבורד</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container form-content">
        <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
            <div class="alert alert-success">
                <span class="icon">✅</span>
                <span>הפרטים עודכנו בהצלחה!</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
            <div class="alert alert-error">
                <span class="icon">❌</span>
                <span>אירעה שגיאה בעדכון הפרטים. אנא נסה שוב.</span>
            </div>
        <?php endif; ?>

        <form id="mentor-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field($is_edit ? 'edit_mentor_action' : 'add_mentor_action', 'mentor_nonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit_mentor' : 'add_mentor'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="mentor_id" value="<?php echo $edit_mentor_id; ?>">
            <?php endif; ?>

            <div class="form-grid">
                <!-- פרטים אישיים -->
                <div class="form-section personal-info">
                    <h2>👩‍🏫 פרטים אישיים</h2>
                    
                    <div class="fields-row">
                        <div class="field-group">
                            <label for="mentor_first_name">שם פרטי <span class="required">*</span></label>
                            <input type="text" id="mentor_first_name" name="mentor_first_name" required 
                                   value="<?php echo esc_attr($mentor_data['mentor_first_name'] ?? ''); ?>"
                                   placeholder="הכנס שם פרטי">
                        </div>
                        
                        <div class="field-group">
                            <label for="mentor_last_name">שם משפחה <span class="required">*</span></label>
                            <input type="text" id="mentor_last_name" name="mentor_last_name" required 
                                   value="<?php echo esc_attr($mentor_data['mentor_last_name'] ?? ''); ?>"
                                   placeholder="הכנס שם משפחה">
                        </div>
                    </div>

                    <div class="fields-row">
                        <div class="field-group">
                            <label for="mentor_phone">טלפון <span class="required">*</span></label>
                            <input type="tel" id="mentor_phone" name="mentor_phone" required 
                                   value="<?php echo esc_attr($mentor_data['mentor_phone'] ?? ''); ?>"
                                   placeholder="050-1234567">
                        </div>
                        
                        <div class="field-group">
                            <label for="mentor_email">אימייל</label>
                            <input type="email" id="mentor_email" name="mentor_email" 
                                   value="<?php echo esc_attr($mentor_data['mentor_email'] ?? ''); ?>"
                                   placeholder="example@email.com">
                        </div>
                    </div>
                </div>

                <!-- פרטי תשלום -->
                <div class="form-section payment">
                    <h2>💰 פרטי תשלום</h2>
                    
                    <div class="field-group">
                        <label for="payment_percentage">אחוז עמלה <span class="required">*</span></label>
                        <div class="percentage-input">
                            <input type="number" id="payment_percentage" name="payment_percentage" required 
                                   min="0" max="100" step="1"
                                   value="<?php echo esc_attr($mentor_data['payment_percentage'] ?? '40'); ?>"
                                   placeholder="40">
                            <span class="percentage-symbol">%</span>
                        </div>
                        <small class="field-help">אחוז העמלה שמגיע למנטורית מכל תשלום של מתאמנת</small>
                    </div>

                    <div class="commission-calculator">
                        <h4>🧮 מחשבון עמלות</h4>
                        <div class="calculator-row">
                            <div class="calc-input">
                                <label>סכום תשלום מתאמנת</label>
                                <input type="number" id="calc-payment" placeholder="1500" min="0">
                            </div>
                            <div class="calc-result">
                                <label>עמלה למנטורית</label>
                                <div class="result-display" id="commission-result">₪0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- הערות -->
                <div class="form-section notes">
                    <h2>📝 הערות</h2>
                    
                    <div class="field-group">
                        <label for="mentor_notes">הערות על המנטורית</label>
                        <textarea id="mentor_notes" name="mentor_notes" rows="4"
                                  placeholder="התמחויות, ניסיון קודם, הערות נוספות..."><?php echo esc_textarea($mentor_data['mentor_notes'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">
                    <span class="icon">💾</span>
                    <span class="btn-text"><?php echo $is_edit ? 'עדכן מנטורית' : 'שמור מנטורית'; ?></span>
                    <span class="btn-loader hidden">⏳</span>
                </button>
                
                <a href="<?php echo home_url('/mentors/'); ?>" class="cancel-btn">
                    <span class="icon">❌</span>
                    <span>ביטול</span>
                </a>
            </div>
        </form>

        <!-- מידע מועיל -->
        <div class="info-panel">
            <h3>💡 מידע חשוב</h3>
            <div class="info-grid">
                <div class="info-item">
                    <h4>אחוז עמלה</h4>
                    <p>ברירת המחדל היא 40%, אך ניתן לשנות בהתאם לצורך</p>
                </div>
                <div class="info-item">
                    <h4>קישור מתאמנות</h4>
                    <p>לאחר יצירת המנטורית, ניתן לקשר אליה מתאמנות בעריכת פרטי המתאמנת</p>
                </div>
                <div class="info-item">
                    <h4>דוחות</h4>
                    <p>המערכת תחשב אוטומטית את העמלות המגיעות למנטורית</p>
                </div>
                <div class="info-item">
                    <h4>עדכון פרטים</h4>
                    <p>ניתן לערוך את פרטי המנטורית בכל עת מרשימת המנטוריות</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.crm-form-container {
    min-height: 100vh;
    background: #f8fafc;
    direction: rtl;
}

.form-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    padding: 40px 0;
    margin-bottom: 40px;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 20px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-info h1 {
    font-size: 2.5rem;
    margin: 0 0 10px 0;
    font-weight: 700;
}

.header-info p {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 15px;
}

.back-btn, .dashboard-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.back-btn:hover, .dashboard-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.form-grid {
    display: grid;
    gap: 30px;
    margin-bottom: 40px;
}

.form-section {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.form-section h2 {
    font-size: 1.5rem;
    color: #1f2937;
    margin: 0 0 25px 0;
    font-weight: 600;
    padding-bottom: 15px;
    border-bottom: 2px solid #f3f4f6;
}

.fields-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.field-group {
    display: flex;
    flex-direction: column;
}

.field-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 1rem;
}

.required {
    color: #dc2626;
}

.field-group input,
.field-group select,
.field-group textarea {
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.field-group input:focus,
.field-group select:focus,
.field-group textarea:focus {
    outline: none;
    border-color: #8b5cf6;
    background: white;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.percentage-input {
    position: relative;
    display: flex;
    align-items: center;
}

.percentage-input input {
    padding-left: 40px !important;
}

.percentage-symbol {
    position: absolute;
    left: 15px;
    font-weight: 600;
    color: #8b5cf6;
    font-size: 1.1rem;
}

.field-help {
    color: #6b7280;
    font-size: 0.9rem;
    margin-top: 5px;
}

.commission-calculator {
    background: #f8fafc;
    padding: 20px;
    border-radius: 10px;
    border: 2px solid #e5e7eb;
    margin-top: 20px;
}

.commission-calculator h4 {
    margin: 0 0 15px 0;
    color: #374151;
}

.calculator-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: end;
}

.calc-input label,
.calc-result label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.calc-input input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
}

.result-display {
    background: #8b5cf6;
    color: white;
    padding: 12px 15px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 1.1rem;
    text-align: center;
}

.form-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    padding: 30px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 40px;
}

.save-btn {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.save-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
}

.cancel-btn {
    background: #f3f4f6;
    color: #374151;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cancel-btn:hover {
    background: #e5e7eb;
    color: #1f2937;
}

.btn-loader.hidden {
    display: none;
}

.info-panel {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 40px;
}

.info-panel h3 {
    color: #1f2937;
    margin: 0 0 20px 0;
    font-weight: 600;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    border-right: 4px solid #8b5cf6;
}

.info-item h4 {
    margin: 0 0 8px 0;
    color: #374151;
    font-size: 1rem;
}

.info-item p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .fields-row {
        grid-template-columns: 1fr;
    }
    
    .calculator-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .header-info h1 {
        font-size: 2rem;
    }
}
</style>

<script>
// חישוב עמלה בזמן אמת
function calculateCommission() {
    const percentage = parseFloat(document.getElementById('payment_percentage').value) || 0;
    const payment = parseFloat(document.getElementById('calc-payment').value) || 0;
    const commission = (payment * percentage / 100);
    
    document.getElementById('commission-result').textContent = `₪${commission.toFixed(0)}`;
}

// מאזינים לשינויים
document.getElementById('payment_percentage').addEventListener('input', calculateCommission);
document.getElementById('calc-payment').addEventListener('input', calculateCommission);

// חישוב ראשוני
calculateCommission();

// טיפול בשליחת הטופס
document.getElementById('mentor-form').addEventListener('submit', function(e) {
    const submitBtn = document.querySelector('.save-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    submitBtn.disabled = true;
});

// ולידציה נוספת
document.addEventListener('DOMContentLoaded', function() {
    // בדיקת אחוז תקין
    const percentageInput = document.getElementById('payment_percentage');
    
    percentageInput.addEventListener('blur', function() {
        const value = parseFloat(this.value);
        if (value < 0) this.value = 0;
        if (value > 100) this.value = 100;
    });
});
</script>

<?php get_footer(); ?> 