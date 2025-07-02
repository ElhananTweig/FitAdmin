<?php
/**
 * טופס הוספת קבוצה חדשה
 */

if (!defined('ABSPATH')) {
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

// קבלת רשימת מנטוריות (ללא מנטוריות שנמחקו)
$mentors = get_posts(array(
    'post_type' => 'mentors',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'is_deleted',
            'value' => false,
            'compare' => '='
        ),
        array(
            'key' => 'is_deleted',
            'value' => 'false',
            'compare' => '='
        ),
        array(
            'key' => 'is_deleted',
            'value' => '',
            'compare' => '='
        ),
        array(
            'key' => 'is_deleted',
            'compare' => 'NOT EXISTS'
        )
    )
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
?>

<div class="wrap" style="direction: rtl;">
    <h1><?php echo $is_edit ? '✏️ עריכת קבוצה' : '👥 הוספת קבוצה חדשה'; ?></h1>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="notice notice-error">
            <p>❌ אירעה שגיאה בשמירת הקבוצה. אנא נסה שוב.</p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="notice notice-success">
            <p>✅ הקבוצה עודכנה בהצלחה!</p>
        </div>
    <?php endif; ?>
    
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px;">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="add-group-form">
            <?php wp_nonce_field($is_edit ? 'edit_group_action' : 'add_group_action', $is_edit ? 'edit_group_nonce' : 'add_group_nonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit_group' : 'add_group'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="group_id" value="<?php echo $edit_group_id; ?>">
            <?php endif; ?>
            
            <!-- פרטי קבוצה בסיסיים -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px;">
                    📋 פרטי קבוצה בסיסיים
                </h2>
                
                <div style="margin-bottom: 20px;">
                    <label for="group_name" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                        שם הקבוצה <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="group_name" name="group_name" required 
                           value="<?php echo isset($group_data['group_name']) ? esc_attr($group_data['group_name']) : ''; ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;"
                           placeholder="לדוגמה: קבוצת מתחילות - פברואר 2024">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="group_mentor" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            מנטורית הקבוצה <span style="color: #dc2626;">*</span>
                        </label>
                        <select id="group_mentor" name="group_mentor" required 
                                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                            <option value="">בחר מנטורית...</option>
                            <?php foreach ($mentors as $mentor): 
                                $mentor_name = get_field('mentor_first_name', $mentor->ID) . ' ' . get_field('mentor_last_name', $mentor->ID);
                            ?>
                                <option value="<?php echo $mentor->ID; ?>" <?php selected(isset($group_data['group_mentor']) ? $group_data['group_mentor'] : '', $mentor->ID); ?>><?php echo $mentor_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="group_max_participants" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            מספר משתתפות מקסימלי <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" id="group_max_participants" name="group_max_participants" min="1" max="50" required 
                               value="<?php echo isset($group_data['group_max_participants']) ? esc_attr($group_data['group_max_participants']) : '10'; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                </div>
                
                <div>
                    <label for="group_description" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                        תיאור הקבוצה
                    </label>
                    <textarea id="group_description" name="group_description" rows="4" 
                              style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px; resize: vertical;"
                              placeholder="תיאור קצר של הקבוצה, יעדיה ומאפייניה המיוחדים..."><?php echo isset($group_data['group_description']) ? esc_textarea($group_data['group_description']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- תאריכים -->
            <div class="form-section" style="margin-bottom: 30px;">
                <h2 style="color: #1f2937; border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 20px;">
                    📅 תאריכי הקבוצה
                </h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="group_start_date" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            תאריך התחלה
                        </label>
                        <input type="date" id="group_start_date" name="group_start_date" 
                               value="<?php echo isset($group_data['group_start_date']) ? esc_attr($group_data['group_start_date']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                    
                    <div>
                        <label for="group_end_date" style="display: block; font-weight: bold; margin-bottom: 5px; color: #374151;">
                            תאריך סיום צפוי
                        </label>
                        <input type="date" id="group_end_date" name="group_end_date" 
                               value="<?php echo isset($group_data['group_end_date']) ? esc_attr($group_data['group_end_date']) : ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    </div>
                </div>
            </div>
            
            <?php if ($is_edit && !empty($group_participants)): ?>
                <!-- משתתפות בקבוצה -->
                <div class="form-section" style="margin-bottom: 30px;">
                    <h2 style="color: #1f2937; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; margin-bottom: 20px;">
                        👥 משתתפות בקבוצה (<?php echo count($group_participants); ?>)
                    </h2>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                        <?php foreach ($group_participants as $participant): 
                            $first_name = get_field('first_name', $participant->ID);
                            $last_name = get_field('last_name', $participant->ID);
                            $phone = get_field('phone', $participant->ID);
                            $start_date = get_field('start_date', $participant->ID);
                        ?>
                            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <div style="font-weight: bold; color: #1f2937; margin-bottom: 5px;">
                                    <?php echo $first_name . ' ' . $last_name; ?>
                                </div>
                                <?php if ($phone): ?>
                                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 5px;">
                                        📞 <a href="tel:<?php echo $phone; ?>" style="color: #3b82f6;"><?php echo $phone; ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($start_date): ?>
                                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 10px;">
                                        📅 התחילה: <?php echo date('d/m/Y', strtotime($start_date)); ?>
                                    </div>
                                <?php endif; ?>
                                <div style="display: flex; gap: 8px;">
                                    <a href="<?php echo admin_url('admin.php?page=add-client-form&edit=' . $participant->ID); ?>" 
                                       style="background: #3b82f6; color: white; padding: 4px 8px; text-decoration: none; border-radius: 4px; font-size: 0.75rem;">
                                        ✏️ ערוך
                                    </a>
                                    <a href="tel:<?php echo $phone; ?>" 
                                       style="background: #10b981; color: white; padding: 4px 8px; text-decoration: none; border-radius: 4px; font-size: 0.75rem;">
                                        📞 התקשר
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- כפתורי פעולה -->
            <div style="display: flex; gap: 15px; justify-content: flex-start; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" 
                        style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 15px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                    <?php echo $is_edit ? '💾 עדכן קבוצה' : '✅ שמור קבוצה'; ?>
                </button>
                
                <a href="<?php echo admin_url('admin.php?page=groups-list'); ?>" 
                   style="background: #6b7280; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block;">
                    ❌ ביטול
                </a>
                
                <?php if ($is_edit): ?>
                    <a href="<?php echo get_post_type_archive_link('clients') . '?group=' . $edit_group_id; ?>" 
                       style="background: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block;">
                        👥 צפה במשתתפות
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
// הגדרת תאריכים ברירת מחדל
<?php if (!$is_edit): ?>
// הגדרת תאריך התחלה לתאריך היום
document.getElementById('group_start_date').value = new Date().toISOString().split('T')[0];

// הגדרת תאריך סיום לחודש מהיום
const endDate = new Date();
endDate.setMonth(endDate.getMonth() + 1);
document.getElementById('group_end_date').value = endDate.toISOString().split('T')[0];
<?php endif; ?>

// וולידציה של תאריכים
document.getElementById('group_start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDateInput = document.getElementById('group_end_date');
    const endDate = new Date(endDateInput.value);
    
    if (endDateInput.value && endDate <= startDate) {
        const newEndDate = new Date(startDate);
        newEndDate.setMonth(newEndDate.getMonth() + 1);
        endDateInput.value = newEndDate.toISOString().split('T')[0];
    }
});

document.getElementById('group_end_date').addEventListener('change', function() {
    const endDate = new Date(this.value);
    const startDateInput = document.getElementById('group_start_date');
    const startDate = new Date(startDateInput.value);
    
    if (startDateInput.value && endDate <= startDate) {
        alert('תאריך הסיום חייב להיות אחרי תאריך ההתחלה');
        this.value = '';
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