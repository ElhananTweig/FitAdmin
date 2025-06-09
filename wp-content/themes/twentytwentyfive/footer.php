</main>

<footer style="background: #f8fafc; padding: 40px 20px; margin-top: 60px; text-align: center; direction: rtl;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 30px;">
            <div>
                <h3 style="color: #1f2937; margin-bottom: 15px;">📞 יצירת קשר</h3>
                <p style="color: #6b7280; margin: 5px 0;">מרים קרישבסקי - תזונאית קלינית</p>
                <p style="color: #6b7280; margin: 5px 0;">📧 miriam@nutrition.co.il</p>
                <p style="color: #6b7280; margin: 5px 0;">📱 050-1234567</p>
            </div>
            
            <div>
                <h3 style="color: #1f2937; margin-bottom: 15px;">🔗 קישורים מהירים</h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="<?php echo get_post_type_archive_link('clients') ?: home_url('/clients/'); ?>" style="color: #3b82f6; text-decoration: none;">👥 רשימת מתאמנות</a>
                    <a href="<?php echo home_url('/finished-clients/'); ?>" style="color: #3b82f6; text-decoration: none;">📝 מתאמנות שסיימו</a>
                    <a href="<?php echo get_post_type_archive_link('mentors') ?: home_url('/mentors/'); ?>" style="color: #3b82f6; text-decoration: none;">👩‍💼 מנטוריות</a>
                </div>
            </div>
            
            <div>
                <h3 style="color: #1f2937; margin-bottom: 15px;">📊 המערכת</h3>
                <p style="color: #6b7280; margin: 5px 0;">מערכת CRM מתקדמת לניהול מתאמנות</p>
                <p style="color: #6b7280; margin: 5px 0;">בנויה בטכנולוגיית WordPress</p>
                <p style="color: #6b7280; margin: 5px 0;">תמיכה מלאה בעברית</p>
            </div>
        </div>
        
        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; color: #6b7280;">
            <p>&copy; <?php echo date('Y'); ?> אלחנן טוויג - כל הזכויות שמורות | מערכת CRM תזונה מתקדמת</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html> 