</main>

<footer style="background: linear-gradient(135deg, #1f2937 0%, #374151 100%); padding: 60px 20px 40px; margin-top: 80px; text-align: center; direction: rtl; position: relative; overflow: hidden;">
    <!-- דקורציה עדינה ברקע -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"2\" fill=\"white\" opacity=\"0.3\"/><circle cx=\"20\" cy=\"20\" r=\"1\" fill=\"white\" opacity=\"0.2\"/><circle cx=\"80\" cy=\"30\" r=\"1.5\" fill=\"white\" opacity=\"0.25\"/><circle cx=\"30\" cy=\"80\" r=\"1\" fill=\"white\" opacity=\"0.2\"/><circle cx=\"70\" cy=\"70\" r=\"1.2\" fill=\"white\" opacity=\"0.3\"/></svg>');"></div>
    
    <div style="max-width: 1200px; margin: 0 auto; position: relative; z-index: 1;">
        <!-- לוגו/שם -->
        <div style="margin-bottom: 40px;">
            <h2 style="color: #ffffff; font-size: 2.5rem; font-weight: 300; margin: 0; letter-spacing: 2px;">
                MIRIAM KRYSHEVSKY
            </h2>
            <p style="color: #9ca3af; font-size: 1.1rem; margin: 8px 0 0 0; font-weight: 300;">
                איזון שמביא תוצאות
            </p>
        </div>
        
        <!-- קו דקורטיבי -->
        <div style="width: 80px; height: 2px; background: linear-gradient(90deg, transparent, #10b981, transparent); margin: 0 auto 40px;"></div>
        
        <!-- פרטי יצירת קשר -->
        <div style="display: flex; justify-content: center; align-items: center; gap: 40px; flex-wrap: wrap; margin-bottom: 40px;">
            <a href="mailto:miriam@nutrition.co.il" style="color: #10b981; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 1.1rem; transition: all 0.3s ease; padding: 10px 20px; border-radius: 25px; background: rgba(16, 185, 129, 0.1);" 
               onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.transform='translateY(0)'">
                <span style="font-size: 1.2rem;">📧</span>
                miriam@nutrition.co.il
            </a>
            
            <a href="tel:050-1234567" style="color: #10b981; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 1.1rem; transition: all 0.3s ease; padding: 10px 20px; border-radius: 25px; background: rgba(16, 185, 129, 0.1);"
               onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.transform='translateY(0)'">
                <span style="font-size: 1.2rem;">📱</span>
                050-1234567
            </a>
        </div>
        
        <!-- קו הפרדה -->
        <div style="width: 100%; height: 1px; background: linear-gradient(90deg, transparent, rgba(156, 163, 175, 0.3), transparent); margin: 0 auto 30px;"></div>
        
        <!-- זכויות יוצרים -->
        <div style="color: #9ca3af; font-size: 0.9rem; font-weight: 300;">
            <p style="margin: 0;">&copy; <?php echo date('Y'); ?> אלחנן טוויג - כל הזכויות שמורות</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>