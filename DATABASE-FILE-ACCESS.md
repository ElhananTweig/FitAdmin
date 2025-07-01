# 📁 איך לגשת לקובץ מסד הנתונים

## 🎯 הקובץ שאתה צריך להעלות:
```
📂 C:\Users\michal\Local Sites\crm-tzuna\app\sql\
  └── 📄 local.sql (841KB)
```

## 🔍 דרכים שונות לגשת לקובץ:

### דרך 1: דרך File Explorer
1. פתח File Explorer
2. נווט ל: `C:\Users\michal\Local Sites\crm-tzuna\app\sql\`
3. תראה את `local.sql` - זה הקובץ!

### דרך 2: דרך LocalWP (קל יותר)
1. פתח את LocalWP
2. לחץ על האתר `crm-tzuna`
3. לחץ על כפתור "Open site folder"
4. נווט ל: `app` → `sql` → `local.sql`

### דרך 3: מהטרמינל (אם אתה בתיקיית public)
```bash
cd ../sql
ls -la local.sql
```

## 📊 פרטי הקובץ שאתה אמור לראות:
- **שם:** `local.sql`
- **גודל:** ~841KB (841,367 bytes)
- **סוג:** SQL Database File
- **תאריך:** אחרון שעבדת על האתר

## ⚠️ אם הקובץ לא קיים:
### צור גיבוי חדש מ-LocalWP:
1. פתח LocalWP
2. לחץ על האתר שלך
3. לחץ על "Database" בתפריט
4. לחץ על "Export"
5. שמור בתור `local.sql`

### או דרך phpMyAdmin המקומי:
1. לך ל: `http://crm-tzuna.local/wp-admin`
2. פתח phpMyAdmin (בדרך כלל קישור בצד)
3. בחר את מסד הנתונים
4. לחץ "Export"
5. בחר "Quick export" + "SQL format"
6. שמור כ-`local.sql`

## ✅ בדיקה שהקובץ תקין:
פתח את הקובץ בעורך טקסט - אתה אמור לראות:
```sql
-- phpMyAdmin SQL Dump
-- version 5.x.x
-- https://www.phpmyadmin.net/
...
CREATE TABLE `wp_posts` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `post_author` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
...
```

אם אתה רואה קוד SQL עם טבלאות wp_ - הקובץ תקין! ✅ 