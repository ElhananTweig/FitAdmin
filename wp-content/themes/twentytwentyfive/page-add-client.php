<?php
/**
 * דף הוספת מתאמנת חדשה
 * Template Name: Add Client
 */

// בדיקת אימות
global $crm_auth;
if ($crm_auth && !$crm_auth->is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
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
        
        // אם group_id הוא אובייקט, קח את ה-ID
        if (is_object($client_data['group_id'])) {
            $client_data['group_id'] = $client_data['group_id']->ID;
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

get_header(); ?>

<div class="crm-form-container">
    <div class="form-header">
        <div class="container">
            <div class="header-content">
                <div class="header-info">
                    <h1><?php echo $is_edit ? '📝 עריכת מתאמנת' : '👤 הוספת מתאמנת חדשה'; ?></h1>
                    <p><?php echo $is_edit ? 'עדכן את פרטי המתאמנת' : 'הכנס את פרטי המתאמנת החדשה למערכת'; ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo home_url('/clients/'); ?>" class="back-btn">
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

        <form id="client-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field($is_edit ? 'edit_client_action' : 'add_client_action', 'client_nonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit_client' : 'add_client'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="client_id" value="<?php echo $edit_client_id; ?>">
            <?php endif; ?>

            <div class="form-grid">
                <!-- פרטים אישיים -->
                <div class="form-section personal-info">
                    <h2>👤 פרטים אישיים</h2>
                    
                    <div class="fields-row">
                        <div class="field-group">
                            <label for="first_name">שם פרטי <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" required 
                                   value="<?php echo esc_attr($client_data['first_name'] ?? ''); ?>"
                                   placeholder="הכנס שם פרטי">
                        </div>
                        
                        <div class="field-group">
                            <label for="last_name">שם משפחה <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name" required 
                                   value="<?php echo esc_attr($client_data['last_name'] ?? ''); ?>"
                                   placeholder="הכנס שם משפחה">
                        </div>
                    </div>

                    <div class="fields-row">
                        <div class="field-group">
                            <label for="phone">טלפון <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" required 
                                   value="<?php echo esc_attr($client_data['phone'] ?? ''); ?>"
                                   placeholder="050-1234567">
                        </div>
                        
                        <div class="field-group">
                            <label for="email">אימייל</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo esc_attr($client_data['email'] ?? ''); ?>"
                                   placeholder="example@email.com">
                        </div>
                    </div>

                    <div class="fields-row">
                        <div class="field-group">
                            <label for="age">גיל</label>
                            <input type="number" id="age" name="age" min="1" max="120"
                                   value="<?php echo esc_attr($client_data['age'] ?? ''); ?>"
                                   placeholder="25">
                        </div>
                        
                        <div class="field-group">
                            <label for="referral_source">מקור הפניה</label>
                            <input type="text" id="referral_source" name="referral_source"
                                   value="<?php echo esc_attr($client_data['referral_source'] ?? ''); ?>"
                                   placeholder="פייסבוק, חברה, גוגל...">
                        </div>
                    </div>
                </div>

                <!-- ליווי וקבוצה -->
                <div class="form-section training">
                    <h2>👥 סוג ליווי</h2>
                    
                    <div class="field-group">
                        <label for="training_type">סוג ליווי <span class="required">*</span></label>
                        <select id="training_type" name="training_type" required>
                            <option value="personal" <?php selected($client_data['training_type'] ?? 'personal', 'personal'); ?>>👤 אישי</option>
                            <option value="group" <?php selected($client_data['training_type'] ?? '', 'group'); ?>>👥 קבוצתי</option>
                        </select>
                    </div>

                    <!-- מנטורית (ליווי אישי) -->
                    <div id="personal-mentor" style="<?php echo (isset($client_data['training_type']) && $client_data['training_type'] === 'group') ? 'display: none;' : 'display: block;'; ?>">
                        <div class="field-group">
                            <label for="mentor_id">מנטורית</label>
                            <select id="mentor_id" name="mentor_id">
                                <option value="">ללא מנטורית</option>
                                <?php foreach ($mentors as $mentor): 
                                    $mentor_name = get_field('mentor_first_name', $mentor->ID) . ' ' . get_field('mentor_last_name', $mentor->ID);
                                ?>
                                    <option value="<?php echo $mentor->ID; ?>" <?php selected($client_data['mentor_id'] ?? '', $mentor->ID); ?>><?php echo $mentor_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- קבוצה (ליווי קבוצתי) -->
                    <div id="group-selection" style="<?php echo (isset($client_data['training_type']) && $client_data['training_type'] === 'group') ? 'display: block;' : 'display: none;'; ?>">
                        <div class="field-group">
                            <label for="group_id">קבוצה</label>
                            <select id="group_id" name="group_id">
                                <option value="">בחר קבוצה...</option>
                                <?php foreach ($groups as $group): 
                                    $group_name = get_field('group_name', $group->ID);
                                ?>
                                    <option value="<?php echo $group->ID; ?>" <?php selected($client_data['group_id'] ?? '', $group->ID); ?>><?php echo $group_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- תאריכי תכנית -->
                <div class="form-section dates">
                    <h2>📅 תאריכי תכנית</h2>
                    
                    <div class="fields-row">
                        <div class="field-group">
                            <label for="start_date">תאריך התחלה <span class="required">*</span></label>
                            <input type="date" id="start_date" name="start_date" required
                                   value="<?php echo esc_attr($client_data['start_date'] ?? ''); ?>">
                        </div>
                        
                        <div class="field-group">
                            <label for="end_date">תאריך סיום <span class="required">*</span></label>
                            <input type="date" id="end_date" name="end_date" required
                                   value="<?php echo esc_attr($client_data['end_date'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- פרטי תשלום -->
                <div class="form-section payment">
                    <h2>💳 פרטי תשלום</h2>
                    
                    <div class="fields-row">
                        <div class="field-group">
                            <label for="payment_amount">סכום לתשלום <span class="required">*</span></label>
                            <input type="number" id="payment_amount" name="payment_amount" required min="0"
                                   value="<?php echo esc_attr($client_data['payment_amount'] ?? ''); ?>"
                                   placeholder="1500">
                        </div>
                        
                        <div class="field-group">
                            <label for="amount_paid">סכום ששולם</label>
                            <input type="number" id="amount_paid" name="amount_paid" min="0"
                                   value="<?php echo esc_attr($client_data['amount_paid'] ?? ''); ?>"
                                   placeholder="1500">
                        </div>
                    </div>

                    <div class="fields-row">
                        <div class="field-group">
                            <label for="payment_method">אמצעי תשלום</label>
                            <select id="payment_method" name="payment_method">
                                <option value="">בחר אמצעי תשלום...</option>
                                <option value="מזומן" <?php selected($client_data['payment_method'] ?? '', 'מזומן'); ?>>מזומן</option>
                                <option value="העברה בנקאית" <?php selected($client_data['payment_method'] ?? '', 'העברה בנקאית'); ?>>העברה בנקאית</option>
                                <option value="אשראי" <?php selected($client_data['payment_method'] ?? '', 'אשראי'); ?>>אשראי</option>
                                <option value="ביט" <?php selected($client_data['payment_method'] ?? '', 'ביט'); ?>>ביט</option>
                                <option value="פייבוקס" <?php selected($client_data['payment_method'] ?? '', 'פייבוקס'); ?>>פייבוקס</option>
                            </select>
                        </div>
                        
                        <div class="field-group">
                            <label for="payment_date">תאריך תשלום</label>
                            <input type="date" id="payment_date" name="payment_date"
                                   value="<?php echo esc_attr($client_data['payment_date'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- פרטי משקל -->
                <div class="form-section weight">
                    <h2>⚖️ פרטי משקל</h2>
                    
                    <div class="fields-row">
                        <div class="field-group">
                            <label for="start_weight">משקל התחלה <span class="required">*</span></label>
                            <input type="number" id="start_weight" name="start_weight" required min="0" step="0.1"
                                   value="<?php echo esc_attr($client_data['start_weight'] ?? ''); ?>"
                                   placeholder="70.5">
                        </div>
                        
                        <div class="field-group">
                            <label for="current_weight">משקל נוכחי</label>
                            <input type="number" id="current_weight" name="current_weight" min="0" step="0.1"
                                   value="<?php echo esc_attr($client_data['current_weight'] ?? ''); ?>"
                                   placeholder="68.2">
                        </div>
                    </div>

                    <div class="fields-row">
                        <div class="field-group">
                            <label for="target_weight">משקל יעד <span class="required">*</span></label>
                            <input type="number" id="target_weight" name="target_weight" required min="0" step="0.1"
                                   value="<?php echo esc_attr($client_data['target_weight'] ?? ''); ?>"
                                   placeholder="65.0">
                        </div>
                        
                        <div class="field-group">
                            <div class="weight-progress" id="weight-progress">
                                <label>התקדמות במשקל</label>
                                <div class="progress-display">
                                    <span id="progress-text">הכנס נתונים לצפייה בהתקדמות</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- הערות -->
                <div class="form-section notes">
                    <h2>📝 הערות</h2>
                    
                    <div class="field-group">
                        <label for="notes">הערות כלליות</label>
                        <textarea id="notes" name="notes" rows="4"
                                  placeholder="הערות נוספות על המתאמנת..."><?php echo esc_textarea($client_data['notes'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">
                    <span class="icon">💾</span>
                    <span class="btn-text"><?php echo $is_edit ? 'עדכן מתאמנת' : 'שמור מתאמנת'; ?></span>
                    <span class="btn-loader hidden">⏳</span>
                </button>
                
                <a href="<?php echo home_url('/clients/'); ?>" class="cancel-btn">
                    <span class="icon">❌</span>
                    <span>ביטול</span>
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.crm-form-container {
    min-height: 100vh;
    background: #f8fafc;
    direction: rtl;
}

.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    margin-bottom: 40px;
}

.container {
    max-width: 1200px;
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
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.weight-progress {
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}

.progress-display {
    margin-top: 10px;
    font-weight: 500;
    color: #6b7280;
}

.form-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    padding: 30px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.save-btn {
    background: linear-gradient(135deg, #10b981, #059669);
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
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
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

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .fields-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .header-info h1 {
        font-size: 2rem;
    }
}
</style>

<script>
// טיפול בשינוי סוג הליווי
document.getElementById('training_type').addEventListener('change', function() {
    const personalMentor = document.getElementById('personal-mentor');
    const groupSelection = document.getElementById('group-selection');
    
    if (this.value === 'group') {
        personalMentor.style.display = 'none';
        groupSelection.style.display = 'block';
        document.getElementById('mentor_id').value = '';
    } else {
        personalMentor.style.display = 'block';
        groupSelection.style.display = 'none';
        document.getElementById('group_id').value = '';
    }
});

// חישוב התקדמות במשקל
function calculateWeightProgress() {
    const startWeight = parseFloat(document.getElementById('start_weight').value);
    const currentWeight = parseFloat(document.getElementById('current_weight').value);
    const targetWeight = parseFloat(document.getElementById('target_weight').value);
    const progressText = document.getElementById('progress-text');
    
    if (startWeight && targetWeight) {
        if (currentWeight) {
            const totalToLose = startWeight - targetWeight;
            const lostSoFar = startWeight - currentWeight;
            const percentage = Math.round((lostSoFar / totalToLose) * 100);
            
            progressText.innerHTML = `
                <div style="color: #10b981; font-weight: 600;">
                    ירידה של ${lostSoFar.toFixed(1)} ק״ג (${percentage}% מהיעד)
                </div>
                <div style="font-size: 0.9rem; margin-top: 5px;">
                    נותרו ${Math.max(0, currentWeight - targetWeight).toFixed(1)} ק״ג ליעד
                </div>
            `;
        } else {
            const totalToLose = startWeight - targetWeight;
            progressText.innerHTML = `
                <div style="color: #6b7280;">
                    יעד: ירידה של ${totalToLose.toFixed(1)} ק״ג
                </div>
            `;
        }
    } else {
        progressText.textContent = 'הכנס נתונים לצפייה בהתקדמות';
    }
}

// הוספת מאזינים לשדות המשקל
['start_weight', 'current_weight', 'target_weight'].forEach(fieldId => {
    document.getElementById(fieldId).addEventListener('input', calculateWeightProgress);
});

// חישוב ראשוני
calculateWeightProgress();

// טיפול בשליחת הטופס
document.getElementById('client-form').addEventListener('submit', function(e) {
    const submitBtn = document.querySelector('.save-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    submitBtn.disabled = true;
});
</script>

<?php get_footer(); ?> 