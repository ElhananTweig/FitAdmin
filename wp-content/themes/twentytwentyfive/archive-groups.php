<?php
/**
 * עמוד ארכיון קבוצות
 */

get_header(); ?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; direction: rtl;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #d7dedc; margin: 0; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">👥 קבוצות</h1>
        <a href="<?php echo admin_url('admin.php?page=add-group-form'); ?>" 
           style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;">
            ➕ הוסף קבוצה חדשה
        </a>
    </div>

    <?php if (have_posts()) : ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php while (have_posts()) : the_post(); 
                $group_id = get_the_ID();
                $group_name = get_field('group_name');
                $group_mentor = get_field('group_mentor');
                $group_description = get_field('group_description');
                $group_start_date = get_field('group_start_date');
                $group_end_date = get_field('group_end_date');
                $group_max_participants = get_field('group_max_participants');
                
                // פרטי מנטורית
                $mentor_name = '';
                $mentor_phone = '';
                if ($group_mentor) {
                    $mentor_id = is_object($group_mentor) ? $group_mentor->ID : $group_mentor;
                    $mentor_name = get_field('mentor_first_name', $mentor_id) . ' ' . get_field('mentor_last_name', $mentor_id);
                    $mentor_phone = get_field('mentor_phone', $mentor_id);
                }
                
                // ספירת משתתפות בקבוצה
                $participants = get_posts(array(
                    'post_type' => 'clients',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'group_id',
                            'value' => $group_id,
                            'compare' => '='
                        )
                    )
                ));
                $participants_count = count($participants);
                
                // קביעת סטטוס הקבוצה
                $status = 'active';
                $status_label = 'פעילה';
                $status_color = '#10b981';
                $today = date('Y-m-d');
                
                if ($group_start_date && $group_start_date > $today) {
                    $status = 'future';
                    $status_label = 'עתידית';
                    $status_color = '#3b82f6';
                } elseif ($group_end_date && $group_end_date < $today) {
                    $status = 'ended';
                    $status_label = 'הסתיימה';
                    $status_color = '#6b7280';
                }
            ?>
                <div style="background: rgba(38, 59, 52, 0.70); backdrop-filter: blur(5.9px); -webkit-backdrop-filter: blur(5.9px); border: 1px solid rgba(255, 255, 255, 0.91); border-radius: 16px; padding: 20px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); transition: all 0.3s; cursor: pointer;"
                     onclick="window.location.href='<?php echo get_post_type_archive_link('clients') . '?group=' . $group_id; ?>';"
                     onmouseenter="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)';"
                     onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 30px rgba(0, 0, 0, 0.1)';">
                    
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin: 0; color: #d7dedc; font-size: 1.25rem; font-weight: bold; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);">
                                <?php echo $group_name; ?>
                            </h3>
                            <div style="background: <?php echo $status_color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 5px; display: inline-block;">
                                <?php echo $status_label; ?>
                            </div>
                        </div>
                        
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: bold; color: #d7dedc; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);">
                                <?php echo $participants_count; ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #d7dedc; opacity: 0.8;">
                                מתוך <?php echo $group_max_participants; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($mentor_name): ?>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #d7dedc;">👩‍🏫 מנטורית:</strong>
                            <span style="color: #ffffff; font-weight: 500;"><?php echo $mentor_name; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($group_start_date || $group_end_date): ?>
                        <div style="margin-bottom: 10px; color: #d7dedc; font-size: 0.875rem; opacity: 0.9;">
                            <strong>📅 תאריכים:</strong>
                            <?php if ($group_start_date): ?>
                                <?php echo date('d.m.Y', strtotime($group_start_date)); ?>
                            <?php endif; ?>
                            <?php if ($group_start_date && $group_end_date): ?> - <?php endif; ?>
                            <?php if ($group_end_date): ?>
                                <?php echo date('d.m.Y', strtotime($group_end_date)); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($group_description): ?>
                        <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px); padding: 10px; border-radius: 8px; margin: 10px 0; font-size: 0.875rem; color: #d7dedc; opacity: 0.9;">
                            <?php echo wp_trim_words($group_description, 15); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
                        <span style="background: #10b981; color: white; padding: 8px 16px; border-radius: 6px; font-size: 0.875rem; font-weight: bold;">
                            👥 צפה במשתתפות (<?php echo $participants_count; ?>)
                        </span>
                        
                        <?php if (current_user_can('manage_options')): ?>
                            <a href="<?php echo admin_url('admin.php?page=add-group-form&edit=' . $group_id); ?>" 
                               style="background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-size: 0.875rem; font-weight: bold;"
                               onclick="event.stopPropagation();">
                                ✏️ ערוך
                            </a>
                            <button type="button" onclick="event.stopPropagation(); deleteGroup(<?php echo $group_id; ?>, '<?php echo esc_js($group_name); ?>', <?php echo $participants_count; ?>);" 
                                    style="background: #ef4444; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: bold; cursor: pointer;">
                                🗑️ מחק
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div style="text-align: center; padding: 60px; background: rgba(38, 59, 52, 0.70); backdrop-filter: blur(5.9px); -webkit-backdrop-filter: blur(5.9px); border: 1px solid rgba(255, 255, 255, 0.91); border-radius: 16px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #d7dedc; margin-bottom: 20px; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);">אין קבוצות עדיין</h2>
            <p style="color: #d7dedc; opacity: 0.8; margin-bottom: 30px;">התחילי בהוספת הקבוצה הראשונה שלך!</p>
            <?php if (current_user_can('manage_options')): ?>
                <a href="<?php echo admin_url('admin.php?page=add-group-form'); ?>" 
                   style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
                    ➕ הוסף קבוצה חדשה
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
@media (max-width: 768px) {
    .container {
        padding: 10px !important;
    }
    
    .container > div:first-child {
        flex-direction: column !important;
        gap: 15px !important;
        text-align: center !important;
    }
    
    .container > div:nth-child(2) {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// פונקציה למחיקת קבוצה
function deleteGroup(groupId, groupName, participantsCount) {
    // בניית הודעת אזהרה מפורטת
    let warningMessage = `האם את בטוחה שברצונך למחוק את הקבוצה "${groupName}"?\n\n⚠️ זוהי פעולה בלתי הפיכה!\n\n`;
    
    if (participantsCount > 0) {
        warningMessage += `🚨 שימי לב: בקבוצה זו יש ${participantsCount} משתתפות!\n`;
        warningMessage += `מחיקת הקבוצה תגרום לכך שהמשתתפות יועברו לליווי אישי.\n\n`;
    }
    
    warningMessage += `מה יימחק:\n`;
    warningMessage += `• פרטי הקבוצה\n`;
    warningMessage += `• תיאור הקבוצה\n`;
    warningMessage += `• קישור למנטורית\n`;
    warningMessage += `• כל המידע הקשור לקבוצה\n\n`;
    
    if (participantsCount > 0) {
        warningMessage += `המשתתפות יישארו במערכת אבל יועברו לליווי אישי.\n\n`;
    }
    
    warningMessage += `האם להמשיך?`;
    
    const confirmation = confirm(warningMessage);
    
    if (!confirmation) {
        return; // המשתמש ביטל
    }
    
    // הצגת הודעת טעינה
    const loadingMessage = document.createElement('div');
    loadingMessage.id = 'delete-loading';
    loadingMessage.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 20px 30px;
        border-radius: 10px;
        z-index: 9999;
        text-align: center;
        font-size: 16px;
    `;
    loadingMessage.innerHTML = '🗑️ מוחקת קבוצה...';
    document.body.appendChild(loadingMessage);
    
    // שליחת בקשת AJAX למחיקה
    const formData = new FormData();
    formData.append('action', 'delete_group');
    formData.append('group_id', groupId);
    formData.append('nonce', '<?php echo wp_create_nonce("delete_group_nonce"); ?>');
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // הסרת הודעת הטעינה
        const loading = document.getElementById('delete-loading');
        if (loading) loading.remove();
        
        if (data.success) {
            // הצגת הודעת הצלחה
            let successMessage = `✅ הקבוצה "${groupName}" נמחקה בהצלחה!`;
            if (data.data.participants_updated > 0) {
                successMessage += `\n\n${data.data.participants_updated} משתתפות הועברו לליווי אישי.`;
            }
            alert(successMessage);
            
            // רענון הדף
            window.location.reload();
        } else {
            alert('❌ שגיאה: ' + (data.data || 'לא ניתן למחוק את הקבוצה'));
        }
    })
    .catch(error => {
        // הסרת הודעת הטעינה
        const loading = document.getElementById('delete-loading');
        if (loading) loading.remove();
        
        console.error('Error:', error);
        alert('❌ אירעה שגיאה במהלך המחיקה. נסה שוב.');
    });
}
</script>

<?php get_footer(); ?> 