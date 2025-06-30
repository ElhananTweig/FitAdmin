# 🚀 מדריך העלאת האתר להוסטינגר

## 📋 רשימת מטלות לפני התחלה
- [ ] חשבון הוסטינגר פעיל
- [ ] דומיין מחובר
- [ ] גישה לפאנל הניהול של הוסטינגר

## שלב 1: הגדרת מסד הנתונים בהוסטינגר

1. **התחבר לפאנל הניהול של הוסטינגר**
2. **לך ל: `Databases` → `MySQL Databases`**
3. **צור מסד נתונים חדש:**
   - שם מסד נתונים: `[your-username]_crm_tzuna`
   - שמור את השם המלא!

4. **צור משתמש חדש:**
   - שם משתמש: `[your-username]_admin`
   - סיסמה חזקה (שמור אותה!)

5. **הקצה את המשתמש למסד הנתונים**
   - בחר את המשתמש ואת מסד הנתונים
   - תן הרשאות מלאות (ALL PRIVILEGES)

## שלב 2: העלאת הקבצים דרך Git

### אופציה A: Git Deployment (מומלץ)
1. **בפאנל הוסטינגר, לך ל: `Git`**
2. **בחר: `Connect Repository`**
3. **הזן את הפרטים:**
   - Repository URL: `https://github.com/ElhananTweig/FitAdmin.git`
   - Branch: `main`
   - Target directory: `public_html` (או התיקיה הראשית)

4. **לחץ על: `Connect and Deploy`**

### אופציה B: הורדה ידנית + FTP
אם Git לא עובד:
1. הורד את הקבצים מגיטהאב כ-ZIP
2. העלה דרך File Manager או FTP לתיקיית `public_html`

## שלב 3: הגדרת קובץ wp-config.php

1. **בפאנל הוסטינגר, לך ל: `File Manager`**
2. **נווט לתיקיית `public_html`**
3. **צור קובץ חדש בשם: `wp-config.php`**
4. **העתק את התוכן מקובץ: `wp-config-hostinger.php`**
5. **עדכן את הערכים הבאים:**

```php
// החלף את הערכים האלה בפרטים האמיתיים שלך:
define( 'DB_NAME', 'הכנס_כאן_את_שם_מסד_הנתונים' );
define( 'DB_USER', 'הכנס_כאן_את_שם_המשתמש' );
define( 'DB_PASSWORD', 'הכנס_כאן_את_הסיסמה' );

// החלף בכתובת האתר שלך:
define( 'WP_HOME', 'https://your-domain.com' );
define( 'WP_SITEURL', 'https://your-domain.com' );
```

6. **צור מפתחות אבטחה חדשים:**
   - לך ל: https://api.wordpress.org/secret-key/1.1/salt/
   - העתק והדבק במקומות המתאימים

## שלב 4: העלאת מסד הנתונים

1. **בפאנל הוסטינגר, לך ל: `phpMyAdmin`**
2. **בחר את מסד הנתונים שיצרת**
3. **לחץ על: `Import`**
4. **העלה את הקובץ: `../sql/local.sql`**
5. **לחץ על: `Go`**

## שלב 5: עדכון כתובות במסד הנתונים

1. **ב-phpMyAdmin, לחץ על: `SQL`**
2. **העתק את התוכן מקובץ: `migrate-database.sql`**
3. **החלף `your-domain.com` בכתובת האתר האמיתית שלך**
4. **הרץ את הסקריפט**

## שלב 6: הגדרות אבטחה (חשוב!)

### קובץ .htaccess
צור קובץ `.htaccess` בתיקיית הרוט עם התוכן הבא:

```apache
# WordPress Security
<Files wp-config.php>
order allow,deny
deny from all
</Files>

# Hide sensitive files
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|inc|bak)$">
Order Allow,Deny
Deny from all
</FilesMatch>

# Prevent PHP execution in uploads
<Directory "/wp-content/uploads/">
    <Files "*.php">
        Order Deny,Allow
        Deny from All
    </Files>
</Directory>

# WordPress pretty permalinks
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
```

## שלב 7: בדיקות אחרונות

- [ ] האתר נטען כהלכה
- [ ] התמה פעילה
- [ ] הפלאגינים פועלים
- [ ] הנתונים הועלו (מתאמנות, קבוצות, מנטוריות)
- [ ] הטפסים עובדים
- [ ] הגדרות ה-ACF תקינות

## שלב 8: עדכונים אחרונות

1. **אם יש בעיות עם הרשאות קבצים:**
   ```
   תיקיות: 755
   קבצים: 644
   wp-config.php: 600
   ```

2. **עדכון permalink structure:**
   - לך לאדמין: `Settings` → `Permalinks`
   - שמור שוב (גם אם לא שינית כלום)

3. **נקה cache אם יש:**
   - בהוסטינגר: `Speed` → `Cache` → `Clear Cache`

## 🎉 סיימת!

האתר שלך אמור להיות פעיל כעת. אם יש בעיות:

### בעיות נפוצות ופתרונות:

**שגיאת חיבור למסד נתונים:**
- בדוק שפרטי מסד הנתונים נכונים ב-wp-config.php
- וודא שהמשתמש הוקצה למסד הנתונים

**האתר לא נטען:**
- בדוק שהקבצים נמצאים בתיקיית public_html
- וודא שקובץ index.php קיים

**תמונות/עיצוב לא נטען:**
- הרץ את סקריפט עדכון הכתובות שוב
- בדוק שהקבצים בתיקיית wp-content/uploads הועלו

**טפסים לא עובדים:**
- וודא שפלאגין ACF פעיל
- בדוק הרשאות כתיבה לתיקיות

---

**צריך עזרה נוספת? 📞**
פנה לתמיכה של הוסטינגר או שלח הודעה עם פרטי השגיאה. 