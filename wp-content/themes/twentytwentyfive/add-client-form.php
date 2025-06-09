<?php
/**
 * טופס הוספת מתאמנת חדשה
 */

if (!defined('ABSPATH')) {
    exit;
}

// בדיקה אם זה עריכה של מתאמנת קיימת
$edit_client_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$is_edit = $edit_client_id > 0;

// טעינת נתוני המתאמנת אם זה עריכה
$client_data = array();
if ($is_edit) {
    $client_post = get_post($edit_client_id);
    if ($client_post && $client_post->post_type === 'clients') {
        $client_data = array(
            'first_name' => get_field('first_name', $edit_client_id),
            'last_name' => get_field('last_name', $edit_client_id),
            'phone' => get_field('phone', $edit_client_id),
            'email' => get_field('email', $edit_client_id),
            'age' => get_field('age', $edit_client_id),
            'start_date' => get_field('start_date', $edit_client_id),
            'end_date' => get_field('end_date', $edit_client_id),
            'referral_source' => get_field('referral_source', $edit_client_id),
            'payment_amount' => get_field('payment_amount', $edit_client_id),
            'amount_paid' => get_field('amount_paid', $edit_client_id),
            'payment_method' => get_field('payment_method', $edit_client_id),
            'payment_date' => get_field('payment_date', $edit_client_id),
            'start_weight' => get_field('start_weight', $edit_client_id),
            'current_weight' => get_field('current_weight', $edit_client_id),
            'target_weight' => get_field('target_weight', $edit_client_id),
            'mentor_id' => get_field('mentor', $edit_client_id),
            'notes' => get_field('notes', $edit_client_id),
            'training_type' => get_field('training_type', $edit_client_id),
            'group_id' => get_field('group_id', $edit_client_id)
        );
        
        // אם mentor הוא אובייקט, קח את ה-ID
        if (is_object($client_data['mentor_id'])) {
            $client_data['mentor_id'] = $client_data['mentor_id']->ID;
        }
    } else {
        $is_edit = false;
        $edit_client_id = 0;
    }
}

// קבלת רשימת מנטוריות
$mentors = get_posts(array(
    'post_type' => 'mentors',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

// קבלת רשימת קבוצות
$groups = get_posts(array(
    'post_type' => 'groups',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

$referral_sources = array(
    'instagram' => 'אינסטגרם',
    'status' => 'סטטוס',
    'whatsapp' => 'וואצאפ',
    'referral' => 'המלצה',
    'mentor' => 'מנטורית',
    'unknown' => 'לא ידוע'
);

$payment_methods = array(
    'cash' => 'מזומן',
    'credit' => 'כרטיס אשראי',
    'bank_transfer' => 'העברה בנקאית',
    'paypal' => 'PayPal',
    'bit' => 'Bit'
);
?>

<div class="wrap" style="direction: rtl;">
    <h1><?php echo $is_edit ? '✏️ עריכת מתאמנת' : '👥 הוספת מתאמנת חדשה'; ?></h1>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="notice notice-error">
            <p>❌ אירעה שגיאה בשמירת המתאמנת. אנא נסה שוב.</p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="notice notice-success">
            <p>✅ המתאמנת עודכנה בהצלחה!</p>
        </div>
    <?php endif; ?>
    
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px;">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="add-client-form">
            <?php wp_nonce_field($is_edit ? 'edit_client_action' : 'add_client_action', $is_edit ? 'edit_client_nonce' : 'add_client_nonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit_client' : 'add_client'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="client_id" value="<?php echo $edit_client_id; ?>">
            <?php endif; ?>
            
            <!-- פרטים אישיים -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px;">
                    👤 פרטים אישיים
                </h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="first_name" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            שם פרטי <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?php echo isset($client_data['first_name']) ? esc_attr($client_data['first_name']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                    
                    <div>
                        <label for="last_name" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            שם משפחה <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?php echo isset($client_data['last_name']) ? esc_attr($client_data['last_name']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="phone" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            טלפון <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" required 
                               value="<?php echo isset($client_data['phone']) ? esc_attr($client_data['phone']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                    
                    <div>
                        <label for="email" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            אימייל
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo isset($client_data['email']) ? esc_attr($client_data['email']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;"
                               placeholder="example@email.com">
                    </div>
                    
                    <div>
                        <label for="age" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            גיל
                        </label>
                        <input type="number" id="age" name="age" min="1" max="120" 
                               value="<?php echo isset($client_data['age']) ? esc_attr($client_data['age']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                </div>
            </div>
            
            <!-- תאריכים -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 20px;">
                    📅 תאריכי טיפול
                </h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="start_date" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            תאריך התחלה <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date" required 
                               value="<?php echo isset($client_data['start_date']) ? esc_attr($client_data['start_date']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                    
                    <div>
                        <label for="end_date" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            תאריך סיום <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" required 
                               value="<?php echo isset($client_data['end_date']) ? esc_attr($client_data['end_date']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                </div>
            </div>
            
            <!-- מקור הגעה וליווי -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; margin-bottom: 20px;">
                    🎯 מקור הגעה וליווי
                </h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="referral_source" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            מקור הגעה <span style="color: #dc2626;">*</span>
                        </label>
                        <select id="referral_source" name="referral_source" required 
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                            <option value="">בחר מקור הגעה...</option>
                            <?php foreach ($referral_sources as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php selected(isset($client_data['referral_source']) ? $client_data['referral_source'] : '', $key); ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="training_type" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            סוג ליווי <span style="color: #dc2626;">*</span>
                        </label>
                        <select id="training_type" name="training_type" required 
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                            <option value="personal" <?php selected(isset($client_data['training_type']) ? $client_data['training_type'] : 'personal', 'personal'); ?>>&nbsp;👤 אישי</option>
                            <option value="group" <?php selected(isset($client_data['training_type']) ? $client_data['training_type'] : '', 'group'); ?>>&nbsp;👥 קבוצתי</option>
                        </select>
                    </div>
                </div>
                
                <!-- מנטורית (ליווי אישי) -->
                <div id="personal-mentor" style="<?php echo (isset($client_data['training_type']) && $client_data['training_type'] === 'group') ? 'display: none;' : 'display: block;'; ?>">
                    <div>
                        <label for="mentor_id" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            מנטורית
                        </label>
                        <select id="mentor_id" name="mentor_id" 
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                            <option value="">ללא מנטורית</option>
                            <?php foreach ($mentors as $mentor): 
                                $mentor_name = get_field('mentor_first_name', $mentor->ID) . ' ' . get_field('mentor_last_name', $mentor->ID);
                            ?>
                                <option value="<?php echo $mentor->ID; ?>" <?php selected(isset($client_data['mentor_id']) ? $client_data['mentor_id'] : '', $mentor->ID); ?>><?php echo $mentor_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- קבוצה (ליווי קבוצתי) -->
                <div id="group-selection" style="<?php echo (isset($client_data['training_type']) && $client_data['training_type'] === 'group') ? 'display: block;' : 'display: none;'; ?>">
                    <div>
                        <label for="group_id" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            בחירת קבוצה <span style="color: #dc2626;">*</span>
                        </label>
                        <select id="group_id" name="group_id" 
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                            <option value="">בחר קבוצה...</option>
                            <?php foreach ($groups as $group): 
                                $group_name = get_field('group_name', $group->ID);
                                $group_mentor = get_field('group_mentor', $group->ID);
                                $mentor_name = '';
                                if ($group_mentor) {
                                    $mentor_name = get_field('mentor_first_name', $group_mentor->ID) . ' ' . get_field('mentor_last_name', $group_mentor->ID);
                                }
                            ?>
                                <option value="<?php echo $group->ID; ?>" <?php selected(isset($client_data['group_id']) ? (is_object($client_data['group_id']) ? $client_data['group_id']->ID : $client_data['group_id']) : '', $group->ID); ?>>
                                    <?php echo $group_name; ?><?php echo $mentor_name ? ' (מנטורית: ' . $mentor_name . ')' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- תשלום -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #8b5cf6; padding-bottom: 10px; margin-bottom: 20px;">
                    💰 פרטי תשלום
                </h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="payment_amount" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            סכום לתשלום (₪) <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" id="payment_amount" name="payment_amount" min="0" step="0.01" required 
                               value="<?php echo isset($client_data['payment_amount']) ? esc_attr($client_data['payment_amount']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                    
                    <div>
                        <label for="amount_paid" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            סכום ששולם (₪)
                        </label>
                        <input type="number" id="amount_paid" name="amount_paid" min="0" step="0.01" 
                               value="<?php echo isset($client_data['amount_paid']) ? esc_attr($client_data['amount_paid']) : '0'; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                </div>
                
                <div id="payment-details" style="<?php echo (isset($client_data['amount_paid']) && $client_data['amount_paid'] > 0) ? 'display: block;' : 'display: none;'; ?>">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="payment_method" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                                אמצעי תשלום
                            </label>
                            <select id="payment_method" name="payment_method" 
                                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                                <option value="">בחר אמצעי תשלום...</option>
                                <?php foreach ($payment_methods as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php selected(isset($client_data['payment_method']) ? $client_data['payment_method'] : '', $key); ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="payment_date" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                                תאריך תשלום
                            </label>
                            <input type="date" id="payment_date" name="payment_date" 
                                   value="<?php echo isset($client_data['payment_date']) ? esc_attr($client_data['payment_date']) : ''; ?>"
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- משקל -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #ef4444; padding-bottom: 10px; margin-bottom: 20px;">
                    ⚖️ נתוני משקל
                </h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="start_weight" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            משקל התחלתי (ק"ג)
                        </label>
                        <input type="number" id="start_weight" name="start_weight" min="0" step="0.1" 
                               value="<?php echo isset($client_data['start_weight']) ? esc_attr($client_data['start_weight']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;"
                               placeholder="70.5">
                    </div>
                    
                    <div>
                        <label for="current_weight" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            משקל נוכחי (ק"ג)
                        </label>
                        <input type="number" id="current_weight" name="current_weight" min="0" step="0.1" 
                               value="<?php echo isset($client_data['current_weight']) ? esc_attr($client_data['current_weight']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;"
                               placeholder="יכולה להישאר ריק">
                    </div>
                    
                    <div>
                        <label for="target_weight" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            משקל יעד (ק"ג)
                        </label>
                        <input type="number" id="target_weight" name="target_weight" min="0" step="0.1" 
                               value="<?php echo isset($client_data['target_weight']) ? esc_attr($client_data['target_weight']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;"
                               placeholder="65.0">
                    </div>
                </div>
            </div>
            
            <!-- הערות -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #6b7280; padding-bottom: 10px; margin-bottom: 20px;">
                    📝 הערות
                </h2>
                
                <div>
                    <label for="notes" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                        הערות נוספות
                    </label>
                    <textarea id="notes" name="notes" rows="4" 
                              style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px; resize: vertical;"
                              placeholder="אלרגיות, העדפות, מטרות מיוחדות..."><?php echo isset($client_data['notes']) ? esc_textarea($client_data['notes']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- כפתורי פעולה -->
            <div style="display: flex; gap: 15px; justify-content: flex-start; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" 
                        style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 15px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                    <?php echo $is_edit ? '💾 עדכן מתאמנת' : '✅ שמור מתאמנת'; ?>
                </button>
                
                <a href="<?php echo admin_url('admin.php?page=crm-dashboard'); ?>" 
                   style="background: #6b7280; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block;">
                    ❌ ביטול
                </a>
                
                <?php if ($is_edit): ?>
                    <a href="<?php echo get_post_type_archive_link('clients'); ?>" 
                       style="background: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block;">
                        👥 חזור למתאמנות
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
// הצגת פרטי תשלום רק אם הוכנס סכום ששולם
document.getElementById('amount_paid').addEventListener('input', function() {
    const amountPaid = parseFloat(this.value) || 0;
    const paymentDetails = document.getElementById('payment-details');
    
    if (amountPaid > 0) {
        paymentDetails.style.display = 'block';
        // הגדרת תאריך תשלום לתאריך היום אם לא הוגדר
        if (!document.getElementById('payment_date').value) {
            document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
        }
    } else {
        paymentDetails.style.display = 'none';
    }
});

// הגדרת תאריך התחלה לתאריך היום כברירת מחדל (רק אם זה לא עריכה)
<?php if (!$is_edit): ?>
document.getElementById('start_date').value = new Date().toISOString().split('T')[0];
<?php endif; ?>

// העתקת משקל התחלתי למשקל נוכחי
document.getElementById('start_weight').addEventListener('input', function() {
    if (!document.getElementById('current_weight').value) {
        document.getElementById('current_weight').value = this.value;
    }
});

// החלפה בין ליווי אישי לקבוצתי
document.getElementById('training_type').addEventListener('change', function() {
    const personalMentor = document.getElementById('personal-mentor');
    const groupSelection = document.getElementById('group-selection');
    const groupSelect = document.getElementById('group_id');
    const mentorSelect = document.getElementById('mentor_id');
    
    if (this.value === 'group') {
        personalMentor.style.display = 'none';
        groupSelection.style.display = 'block';
        groupSelect.required = true;
        mentorSelect.required = false;
        mentorSelect.value = '';
    } else {
        personalMentor.style.display = 'block';
        groupSelection.style.display = 'none';
        groupSelect.required = false;
        mentorSelect.required = false;
        groupSelect.value = '';
    }
});

// אפקטים ויזואליים
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('focus', function() {
        this.style.borderColor = '#3b82f6';
        this.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)';
    });
    
    element.addEventListener('blur', function() {
        this.style.borderColor = '#d1d5db';
        this.style.boxShadow = 'none';
    });
});

// אפקט hover לכפתור השמירה
document.querySelector('button[type="submit"]').addEventListener('mouseenter', function() {
    this.style.transform = 'translateY(-2px)';
    this.style.boxShadow = '0 4px 12px rgba(16, 185, 129, 0.4)';
});

document.querySelector('button[type="submit"]').addEventListener('mouseleave', function() {
    this.style.transform = 'translateY(0)';
    this.style.boxShadow = 'none';
});
</script>

<style>
@media (max-width: 768px) {
    .form-section > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style> 