<?php
/**
 * טופס הוספת מנטורית חדשה
 */

if (!defined('ABSPATH')) {
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
?>

<div class="wrap" style="direction: rtl;">
    <h1><?php echo $is_edit ? '✏️ עריכת מנטורית' : '👩‍💼 הוספת מנטורית חדשה'; ?></h1>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="notice notice-error">
            <p>❌ אירעה שגיאה בשמירת המנטורית. אנא נסה שוב.</p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="notice notice-success">
            <p>✅ המנטורית עודכנה בהצלחה!</p>
        </div>
    <?php endif; ?>
    
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px;">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="add-mentor-form">
            <?php wp_nonce_field($is_edit ? 'edit_mentor_action' : 'add_mentor_action', $is_edit ? 'edit_mentor_nonce' : 'add_mentor_nonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit_mentor' : 'add_mentor'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="mentor_id" value="<?php echo $edit_mentor_id; ?>">
            <?php endif; ?>
            
            <!-- פרטים אישיים -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #8b5cf6; padding-bottom: 10px; margin-bottom: 20px;">
                    👤 פרטים אישיים
                </h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="mentor_first_name" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            שם פרטי <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" id="mentor_first_name" name="mentor_first_name" required 
                               value="<?php echo isset($mentor_data['mentor_first_name']) ? esc_attr($mentor_data['mentor_first_name']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                    
                    <div>
                        <label for="mentor_last_name" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            שם משפחה <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" id="mentor_last_name" name="mentor_last_name" required 
                               value="<?php echo isset($mentor_data['mentor_last_name']) ? esc_attr($mentor_data['mentor_last_name']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="mentor_phone" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            טלפון <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="tel" id="mentor_phone" name="mentor_phone" required 
                               value="<?php echo isset($mentor_data['mentor_phone']) ? esc_attr($mentor_data['mentor_phone']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;"
                               placeholder="050-1234567">
                    </div>
                    
                    <div>
                        <label for="mentor_email" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            אימייל <span style="color: #dc2626;">
                        </label>
                        <input type="email" id="mentor_email" name="mentor_email"  
                               value="<?php echo isset($mentor_data['mentor_email']) ? esc_attr($mentor_data['mentor_email']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;"
                               placeholder="mentor@example.com">
                    </div>
                </div>
            </div>
            
            <!-- פרטי עמלה -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 20px;">
                    💰 פרטי עמלה
                </h2>
                
                <div>
                    <label for="payment_percentage" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                        אחוז עמלה (%)
                    </label>
                    <input type="number" id="payment_percentage" name="payment_percentage" min="0" max="100" step="0.1" 
                           value="<?php echo isset($mentor_data['payment_percentage']) ? esc_attr($mentor_data['payment_percentage']) : '40'; ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;"
                           placeholder="40">
                    <small style="color: #6b7280; font-size: 0.875rem; margin-top: 5px; display: block;">
                        ברירת מחדל: 40%. ניתן לשנות בהתאם לצורך.
                    </small>
                </div>
            </div>
            
            <!-- הערות -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; margin-bottom: 20px;">
                    📝 הערות ומידע נוסף
                </h2>
                
                <div>
                    <label for="mentor_notes" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                        הערות ומידע נוסף
                    </label>
                    <textarea id="mentor_notes" name="mentor_notes" rows="4" 
                              style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px; resize: vertical;"
                              placeholder="התמחויות, ניסיון, הערות מיוחדות..."><?php echo isset($mentor_data['mentor_notes']) ? esc_textarea($mentor_data['mentor_notes']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- כפתורי פעולה -->
            <div style="display: flex; gap: 15px; justify-content: flex-start; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" 
                        style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 15px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                    <?php echo $is_edit ? '💾 עדכן מנטורית' : '✅ שמור מנטורית'; ?>
                </button>
                
                <a href="<?php echo admin_url('admin.php?page=crm-dashboard'); ?>" 
                   style="background: #6b7280; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block;">
                    ❌ ביטול
                </a>
                
                <?php if ($is_edit): ?>
                    <a href="<?php echo get_post_type_archive_link('mentors'); ?>" 
                       style="background: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block;">
                        👩‍💼 חזור למנטוריות
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- מידע נוסף -->
    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-top: 20px; max-width: 600px;">
        <h3 style="color: #1f2937; margin-bottom: 15px;">💡 מידע חשוב</h3>
        <ul style="color: #6b7280; line-height: 1.6;">
            <li><strong>אחוז עמלה:</strong> ברירת המחדל היא 40%, אך ניתן לשנות בהתאם לצורך</li>
            <li><strong>קישור מתאמנות:</strong> לאחר יצירת המנטורית, ניתן לקשר אליה מתאמנות בעריכת פרטי המתאמנת</li>
            <li><strong>דוחות:</strong> המערכת תחשב אוטומטית את העמלות המגיעות למנטורית</li>
            <li><strong>עדכון פרטים:</strong> ניתן לערוך את פרטי המנטורית בכל עת מרשימת המנטוריות</li>
        </ul>
    </div>
</div>

<script>
// אפקטים ויזואליים
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('focus', function() {
        this.style.borderColor = '#8b5cf6';
        this.style.boxShadow = '0 0 0 3px rgba(139, 92, 246, 0.1)';
    });
    
    element.addEventListener('blur', function() {
        this.style.borderColor = '#d1d5db';
        this.style.boxShadow = 'none';
    });
});

// אפקט hover לכפתור השמירה
document.querySelector('button[type="submit"]').addEventListener('mouseenter', function() {
    this.style.transform = 'translateY(-2px)';
    this.style.boxShadow = '0 4px 12px rgba(139, 92, 246, 0.4)';
});

document.querySelector('button[type="submit"]').addEventListener('mouseleave', function() {
    this.style.transform = 'translateY(0)';
    this.style.boxShadow = 'none';
});

// ולידציה לאחוז עמלה
document.getElementById('payment_percentage').addEventListener('input', function() {
    const value = parseFloat(this.value);
    if (value < 0) this.value = 0;
    if (value > 100) this.value = 100;
});

// הוספת אפקט לשדה אחוז עמלה
document.getElementById('payment_percentage').addEventListener('change', function() {
    const value = parseFloat(this.value);
    if (value > 50) {
        this.style.borderColor = '#f59e0b';
        this.style.backgroundColor = '#fef3c7';
    } else {
        this.style.borderColor = '#d1d5db';
        this.style.backgroundColor = 'white';
    }
});
</script>

<style>
@media (max-width: 768px) {
    .form-section > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php
$payment_methods = array(
    'cash' => 'מזומן',
    'credit' => 'כרטיס אשראי',
    'bank_transfer' => 'העברה בנקאית',
    'paypal' => 'PayPal',
    'bit' => 'Bit'
); 