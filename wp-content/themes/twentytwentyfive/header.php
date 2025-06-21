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
            background: rgb(106, 158, 141);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.15);
        }
        
        .crm-header-content {
            max-width: 1800px;
            margin: 0 auto;
            padding: 0 30px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            direction: rtl;
            gap: 20px;
        }
        
        .crm-logo {
            font-size: 2rem;
            font-weight: 700;
            text-decoration: none;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            justify-self: start;
        }
        
        .crm-nav {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            justify-self: center;
        }
        
        .crm-nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 14px 24px;
            border-radius: 30px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            white-space: nowrap;
            backdrop-filter: blur(10px);
            min-height: 50px;
            display: flex;
            align-items: center;
        }
        
        .crm-nav a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .user-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-self: end;
        }
        
        .user-actions a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 1rem;
            min-height: 45px;
            display: flex;
            align-items: center;
        }
        
        .user-actions a:hover {
            background: rgba(255,255,255,0.35);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }
        
        /* רספונסיבי */
        @media (max-width: 1400px) {
            .crm-nav {
                gap: 10px;
            }
            
            .crm-nav a {
                padding: 12px 20px;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 1200px) {
            .crm-nav {
                gap: 8px;
            }
            
            .crm-nav a {
                padding: 10px 16px;
                font-size: 0.95rem;
            }
            
            .user-actions a {
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 992px) {
            .crm-header-content {
                padding: 0 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .crm-logo {
                order: 1;
            }
            
            .crm-nav {
                order: 2;
                gap: 8px;
                justify-content: center;
            }
            
            .user-actions {
                order: 3;
                justify-content: center;
            }
        }
        
        @media (max-width: 768px) {
            .crm-header {
                padding: 15px 0;
            }
            
            .crm-logo {
                font-size: 1.7rem;
            }
            
            .crm-nav {
                gap: 6px;
                flex-wrap: wrap;
            }
            
            .crm-nav a {
                font-size: 0.85rem;
                padding: 8px 12px;
                min-height: 40px;
            }
            
            .user-actions a {
                font-size: 0.85rem;
                padding: 8px 12px;
                min-height: 40px;
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
        </nav>
        
        <div class="user-actions">
            <?php if (is_user_logged_in()): ?>
                <a href="<?php echo wp_logout_url(home_url()); ?>">🚪 יציאה</a>
            <?php else: ?>
                <a href="<?php echo wp_login_url(); ?>">🔑 כניסה</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main id="main" class="site-main">
</main>

<?php wp_footer(); ?>
</body>
</html> 