<?php
/**
 * עמוד ארכיון קבוצות
 */

get_header(); ?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; direction: rtl;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #1f2937; margin: 0;">👥 קבוצות</h1>
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
                <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: all 0.3s; cursor: pointer; border: 1px solid #e5e7eb;"
                     onclick="window.location.href='<?php echo get_post_type_archive_link('clients') . '?group=' . $group_id; ?>';"
                     onmouseenter="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)';"
                     onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                    
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin: 0; color: #1f2937; font-size: 1.25rem; font-weight: bold;">
                                <?php echo $group_name; ?>
                            </h3>
                            <div style="background: <?php echo $status_color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 5px; display: inline-block;">
                                <?php echo $status_label; ?>
                            </div>
                        </div>
                        
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: bold; color: #3b82f6;">
                                <?php echo $participants_count; ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #6b7280;">
                                מתוך <?php echo $group_max_participants; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($mentor_name): ?>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #374151;">👩‍🏫 מנטורית:</strong>
                            <span style="color: #8b5cf6;"><?php echo $mentor_name; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($group_start_date || $group_end_date): ?>
                        <div style="margin-bottom: 10px; color: #6b7280; font-size: 0.875rem;">
                            <strong>📅 תאריכים:</strong>
                            <?php if ($group_start_date): ?>
                                <?php echo date('d/m/Y', strtotime($group_start_date)); ?>
                            <?php endif; ?>
                            <?php if ($group_start_date && $group_end_date): ?> - <?php endif; ?>
                            <?php if ($group_end_date): ?>
                                <?php echo date('d/m/Y', strtotime($group_end_date)); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($group_description): ?>
                        <div style="background: #f9fafb; padding: 10px; border-radius: 6px; margin: 10px 0; font-size: 0.875rem; color: #6b7280;">
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
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div style="text-align: center; padding: 60px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #6b7280; margin-bottom: 20px;">אין קבוצות עדיין</h2>
            <p style="color: #9ca3af; margin-bottom: 30px;">התחילי בהוספת הקבוצה הראשונה שלך!</p>
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

<?php get_footer(); ?> 