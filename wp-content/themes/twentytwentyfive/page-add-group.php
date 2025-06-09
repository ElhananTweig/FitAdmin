<?php
/**
 * דף הוספת קבוצה חדשה
 * Template Name: Add Group
 */

// בדיקת אימות
global $crm_auth;
if ($crm_auth && !$crm_auth->is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

// בדיקה אם זה עריכה של קבוצה קיימת
$edit_group_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$is_edit = $edit_group_id > 0;

// טעינת נתוני הקבוצה אם זה עריכה
$group_data = array();
if ($is_edit) {
    $group_post = get_post($edit_group_id);
    if ($group_post && $group_post->post_type === 'groups') {
        $group_data = array(
            'group_name' => get_field('group_name', $edit_group_id),
            'group_mentor' => get_field('group_mentor', $edit_group_id),
            'group_description' => get_field('group_description', $edit_group_id),
            'group_start_date' => get_field('group_start_date', $edit_group_id),
            'group_end_date' => get_field('group_end_date', $edit_group_id),
            'group_max_participants' => get_field('group_max_participants', $edit_group_id)
        );
        
        // אם mentor הוא אובייקט, קח את ה-ID
        if (is_object($group_data['group_mentor'])) {
            $group_data['group_mentor'] = $group_data['group_mentor']->ID;
        }
    } else {
        $is_edit = false;
        $edit_group_id = 0;
    }
}

// קבלת רשימת מנטוריות
$mentors = get_posts(array(
    'post_type' => 'mentors',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

// אם זה עריכה, קבל את רשימת המתאמנות בקבוצה
$group_participants = array();
if ($is_edit) {
    $group_participants = get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'group_id',
                'value' => $edit_group_id,
                'compare' => '='
            )
        )
    ));
}

get_header(); ?>

<div class="crm-form-container">
    <div class="form-header">
        <div class="container">
            <div class="header-content">
                <div class="header-info">
                    <h1><?php echo $is_edit ? '📝 עריכת קבוצה' : '👥 הוספת קבוצה חדשה'; ?></h1>
                    <p><?php echo $is_edit ? 'עדכן את פרטי הקבוצה' : 'הכנס את פרטי הקבוצה החדשה למערכת'; ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo home_url('/groups/'); ?>" class="back-btn">
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
        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div class="alert alert-success">
                <span class="icon">✅</span>
                <span>הקבוצה נוצרה בהצלחה!</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
            <div class="alert alert-success">
                <span class="icon">✅</span>
                <span>הפרטים עודכנו בהצלחה!</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
            <div class="alert alert-error">
                <span class="icon">❌</span>
                <span>אירעה שגיאה. אנא נסה שוב.</span>
            </div>
        <?php endif; ?>

        <form id="group-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field($is_edit ? 'edit_group_action' : 'add_group_action', 'group_nonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit_group' : 'add_group'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="group_id" value="<?php echo $edit_group_id; ?>">
            <?php endif; ?>

            <div class="form-grid">
                <!-- פרטי קבוצה -->
                <div class="form-section group-info">
                    <h2>👥 פרטי קבוצה</h2>
                    
                    <div class="field-group">
                        <label for="group_name">שם הקבוצה <span class="required">*</span></label>
                        <input type="text" id="group_name" name="group_name" required 
                               value="<?php echo esc_attr($group_data['group_name'] ?? ''); ?>"
                               placeholder="קבוצת מתחילות - ינואר 2024">
                    </div>

                    <div class="field-group">
                        <label for="group_mentor">מנטורית הקבוצה <span class="required">*</span></label>
                        <select id="group_mentor" name="group_mentor" required>
                            <option value="">בחר מנטורית...</option>
                            <?php foreach ($mentors as $mentor): 
                                $mentor_name = get_field('mentor_first_name', $mentor->ID) . ' ' . get_field('mentor_last_name', $mentor->ID);
                            ?>
                                <option value="<?php echo $mentor->ID; ?>" <?php selected($group_data['group_mentor'] ?? '', $mentor->ID); ?>><?php echo $mentor_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="group_max_participants">מספר משתתפות מקסימלי <span class="required">*</span></label>
                        <input type="number" id="group_max_participants" name="group_max_participants" required 
                               min="1" max="50"
                               value="<?php echo esc_attr($group_data['group_max_participants'] ?? '12'); ?>"
                               placeholder="12">
                        <small class="field-help">מספר המתאמנות המקסימלי שיכולות להיות בקבוצה</small>
                    </div>

                    <div class="field-group">
                        <label for="group_description">תיאור הקבוצה</label>
                        <textarea id="group_description" name="group_description" rows="4"
                                  placeholder="תיאור מטרות הקבוצה, מה תכלול וכו'..."><?php echo esc_textarea($group_data['group_description'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- תאריכי קבוצה -->
                <div class="form-section dates">
                    <h2>📅 תאריכי קבוצה</h2>
                    
                    <div class="fields-row">
                        <div class="field-group">
                            <label for="group_start_date">תאריך התחלה <span class="required">*</span></label>
                            <input type="date" id="group_start_date" name="group_start_date" required
                                   value="<?php echo esc_attr($group_data['group_start_date'] ?? ''); ?>">
                        </div>
                        
                        <div class="field-group">
                            <label for="group_end_date">תאריך סיום <span class="required">*</span></label>
                            <input type="date" id="group_end_date" name="group_end_date" required
                                   value="<?php echo esc_attr($group_data['group_end_date'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="duration-display" id="duration-display">
                        <span>משך הקבוצה: הכנס תאריכים לחישוב</span>
                    </div>
                </div>

                <?php if ($is_edit && $group_participants): ?>
                <!-- משתתפות בקבוצה -->
                <div class="form-section participants">
                    <h2>👥 משתתפות בקבוצה (<?php echo count($group_participants); ?>)</h2>
                    
                    <div class="participants-grid">
                        <?php foreach ($group_participants as $participant): 
                            $first_name = get_field('first_name', $participant->ID);
                            $last_name = get_field('last_name', $participant->ID);
                            $phone = get_field('phone', $participant->ID);
                            $start_date = get_field('start_date', $participant->ID);
                        ?>
                            <div class="participant-card">
                                <div class="participant-info">
                                    <h4><?php echo $first_name . ' ' . $last_name; ?></h4>
                                    <p>📞 <?php echo $phone; ?></p>
                                    <p>📅 החלה: <?php echo date('d/m/Y', strtotime($start_date)); ?></p>
                                </div>
                                <div class="participant-actions">
                                    <a href="<?php echo home_url('/add-client/?edit=' . $participant->ID); ?>" class="edit-link">
                                        <span>✏️</span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">
                    <span class="icon">💾</span>
                    <span class="btn-text"><?php echo $is_edit ? 'עדכן קבוצה' : 'שמור קבוצה'; ?></span>
                    <span class="btn-loader hidden">⏳</span>
                </button>
                
                <a href="<?php echo home_url('/groups/'); ?>" class="cancel-btn">
                    <span class="icon">❌</span>
                    <span>ביטול</span>
                </a>
                
                <?php if ($is_edit): ?>
                    <a href="<?php echo home_url('/add-client/?group=' . $edit_group_id); ?>" class="add-client-btn">
                        <span class="icon">➕</span>
                        <span>הוסף מתאמנת לקבוצה</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- מידע מועיל -->
        <div class="info-panel">
            <h3>💡 מידע חשוב על קבוצות</h3>
            <div class="info-grid">
                <div class="info-item">
                    <h4>מספר משתתפות</h4>
                    <p>מומלץ לקבוע מספר מקסימלי של 8-15 משתתפות לקבוצה יעילה</p>
                </div>
                <div class="info-item">
                    <h4>משך קבוצה</h4>
                    <p>משך ממוצע של קבוצה הוא 3-4 חודשים</p>
                </div>
                <div class="info-item">
                    <h4>הוספת מתאמנות</h4>
                    <p>ניתן להוסיף מתאמנות לקבוצה במסך הוספת מתאמנת או בעריכת מתאמנת קיימת</p>
                </div>
                <div class="info-item">
                    <h4>מעקב התקדמות</h4>
                    <p>ניתן לעקוב אחר התקדמות הקבוצה דרך מסך הדוחות</p>
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
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
    margin-bottom: 20px;
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
    border-color: #f59e0b;
    background: white;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.field-help {
    color: #6b7280;
    font-size: 0.9rem;
    margin-top: 5px;
}

.duration-display {
    background: #f8fafc;
    padding: 15px 20px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    margin-top: 20px;
    font-weight: 500;
    color: #374151;
    text-align: center;
}

.participants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.participant-card {
    background: #f8fafc;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.participant-card:hover {
    border-color: #f59e0b;
    transform: translateY(-2px);
}

.participant-info h4 {
    margin: 0 0 8px 0;
    color: #1f2937;
    font-weight: 600;
}

.participant-info p {
    margin: 4px 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.edit-link {
    background: #f59e0b;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.edit-link:hover {
    background: #d97706;
    color: white;
}

.form-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
    padding: 30px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 40px;
}

.save-btn {
    background: linear-gradient(135deg, #f59e0b, #d97706);
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
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
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

.add-client-btn {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
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

.add-client-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    color: white;
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
    border-right: 4px solid #f59e0b;
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
    
    .participants-grid {
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
// חישוב משך הקבוצה
function calculateDuration() {
    const startDate = document.getElementById('group_start_date').value;
    const endDate = document.getElementById('group_end_date').value;
    const durationDisplay = document.getElementById('duration-display');
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const diffWeeks = Math.round(diffDays / 7);
        const diffMonths = Math.round(diffDays / 30);
        
        if (end < start) {
            durationDisplay.innerHTML = '<span style="color: #dc2626;">❌ תאריך הסיום חייב להיות אחרי תאריך ההתחלה</span>';
        } else {
            durationDisplay.innerHTML = `
                <span style="color: #10b981; font-weight: 600;">
                    ⏱️ משך הקבוצה: ${diffDays} ימים (${diffWeeks} שבועות, כ-${diffMonths} חודשים)
                </span>
            `;
        }
    } else {
        durationDisplay.innerHTML = '<span>משך הקבוצה: הכנס תאריכים לחישוב</span>';
    }
}

// מאזינים לשינויים בתאריכים
document.getElementById('group_start_date').addEventListener('change', function() {
    // הגדרת תאריך מינימום לסיום
    document.getElementById('group_end_date').min = this.value;
    
    // הצעת תאריך סיום (3 חודשים)
    if (this.value && !document.getElementById('group_end_date').value) {
        const startDate = new Date(this.value);
        startDate.setMonth(startDate.getMonth() + 3);
        document.getElementById('group_end_date').value = startDate.toISOString().split('T')[0];
    }
    
    calculateDuration();
});

document.getElementById('group_end_date').addEventListener('change', calculateDuration);

// חישוב ראשוני
calculateDuration();

// טיפול בשליחת הטופס
document.getElementById('group-form').addEventListener('submit', function(e) {
    const submitBtn = document.querySelector('.save-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    submitBtn.disabled = true;
});

// ולידציה נוספת
document.addEventListener('DOMContentLoaded', function() {
    // בדיקת מספר משתתפות תקין
    const participantsInput = document.getElementById('group_max_participants');
    
    participantsInput.addEventListener('blur', function() {
        const value = parseInt(this.value);
        if (value < 1) this.value = 1;
        if (value > 50) this.value = 50;
    });
});
</script>

<?php get_footer(); ?> 