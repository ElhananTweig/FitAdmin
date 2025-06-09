<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    
    <style>
        /* עיצוב מותאם לכותרת */
        .crm-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .crm-header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            direction: rtl;
        }
        
        .crm-logo {
            font-size: 1.8rem;
            font-weight: 700;
            text-decoration: none;
            color: white;
        }
        
        .crm-nav {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .crm-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s;
        }
        
        .crm-nav a:hover {
            background: rgba(255,255,255,0.2);
            opacity: 0.9;
        }
        
        .admin-link {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        @media (max-width: 768px) {
            .crm-header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .crm-nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
        }
    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="crm-header">
    <div class="crm-header-content">
        <a href="<?php echo home_url(); ?>" class="crm-logo">
            🌟 CRM תזונה - מרים קרישבסקי
        </a>
        
        <nav class="crm-nav">
            <a href="<?php echo home_url(); ?>">🏠 מסך הבית</a>
            <a href="<?php echo get_post_type_archive_link('clients') ?: home_url('/clients/'); ?>">👥 מתאמנות</a>
            <a href="<?php echo get_post_type_archive_link('mentors') ?: home_url('/mentors/'); ?>">👩‍💼 מנטוריות</a>
            <a href="<?php echo get_post_type_archive_link('groups') ?: home_url('/groups/'); ?>">🎯 קבוצות</a>
            <a href="<?php echo home_url('/finished-clients/'); ?>">📝 מתאמנות שסיימו</a>
            
            <?php if (current_user_can('manage_options')): ?>
                <a href="<?php echo admin_url('admin.php?page=crm-dashboard'); ?>" class="admin-link">
                    ⚙️ ניהול
                </a>
            <?php endif; ?>
            
            <?php if (is_user_logged_in()): ?>
                <a href="<?php echo wp_logout_url(home_url()); ?>">🚪 יציאה</a>
            <?php else: ?>
                <a href="<?php echo wp_login_url(); ?>">🔑 כניסה</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main id="main" class="site-main">
</main>

<?php wp_footer(); ?>
</body>
</html> 