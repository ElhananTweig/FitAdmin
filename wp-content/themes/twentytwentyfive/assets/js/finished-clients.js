// עמוד מעקב מתאמנות שסיימו - JavaScript מאופטם
document.addEventListener('DOMContentLoaded', function() {
    // אתחול הפילטרים
    initializeFilters();
    
    // אתחול השדות התאריכים
    initializeDateFields();
    
    // גלילה אוטומטית לכרטיס מעודכן
    scrollToUpdatedCard();
    
    // הוספת event listeners למודלים
    initializeModals();
});

// אתחול פילטרים
function initializeFilters() {
    const nameFilter = document.getElementById('nameFilter');
    const statusFilter = document.getElementById('statusFilter');
    const endDateFilter = document.getElementById('endDateFilter');
    const clearFilters = document.getElementById('clearFilters');
    const filterResults = document.getElementById('filterResults');
    const visibleCount = document.getElementById('visibleCount');
    const totalCount = document.getElementById('totalCount');
    
    const allCards = document.querySelectorAll('.client-follow-card');
    
    if (totalCount) {
        totalCount.textContent = allCards.length;
    }
    
    // פונקציה מאופטמת לפילטור - ביעילות גבוהה
    function filterClients() {
        const nameValue = nameFilter ? nameFilter.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value : '';
        const endDateValue = endDateFilter ? endDateFilter.value : '';
        
        let visibleCards = 0;
        
        // שימוש ב-DocumentFragment לביצועים טובים יותר
        allCards.forEach(card => {
            const name = card.dataset.name ? card.dataset.name.toLowerCase() : '';
            const status = card.dataset.status || '';
            const endDate = card.dataset.endDate || '';
            
            let show = true;
            
            // פילטר שם - חיפוש מהיר
            if (nameValue && !name.includes(nameValue)) {
                show = false;
            }
            
            // פילטר סטטוס
            if (statusValue && status !== statusValue) {
                show = false;
            }
            
            // פילטר תאריך
            if (endDateValue && endDate !== endDateValue) {
                show = false;
            }
            
            // עדכון תצוגה
            card.style.display = show ? 'block' : 'none';
            if (show) visibleCards++;
        });
        
        // עדכון ספירת תוצאות
        if (visibleCount) {
            visibleCount.textContent = visibleCards;
        }
        
        const hasActiveFilters = nameValue || statusValue || endDateValue;
        if (filterResults) {
            filterResults.style.display = hasActiveFilters ? 'block' : 'none';
        }
    }
    
    // פונקציה לניקוי פילטרים
    function clearAllFilters() {
        if (nameFilter) nameFilter.value = '';
        if (statusFilter) statusFilter.value = '';
        if (endDateFilter) endDateFilter.value = '';
        filterClients();
    }
    
    // הוספת Event Listeners
    if (nameFilter) {
        nameFilter.addEventListener('input', debounce(filterClients, 300));
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterClients);
    }
    if (endDateFilter) {
        endDateFilter.addEventListener('change', filterClients);
    }
    if (clearFilters) {
        clearFilters.addEventListener('click', clearAllFilters);
    }
}

// פונקצית debounce למניעת יותר מדי קריאות
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// אתחול שדות תאריכים
function initializeDateFields() {
    // התאריכים לכל שדות המעקב הקיימים
    const nextContactInputs = document.querySelectorAll('input[name="next_contact_date"]');
    nextContactInputs.forEach(input => {
        if (!input.value) {
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            input.value = nextWeek.toISOString().split('T')[0];
        }
    });
}

// גלילה אוטומטית לכרטיס שעודכן
function scrollToUpdatedCard() {
    const updatedCard = document.querySelector('.client-follow-card.recently-updated');
    if (updatedCard) {
        setTimeout(() => {
            updatedCard.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }, 500);
    }
}

// אתחול מודלים
function initializeModals() {
    // סגירת מודלים בלחיצה על הרקע
    window.addEventListener('click', function(event) {
        const finishedModal = document.getElementById('addFinishedClientModal');
        const leadModal = document.getElementById('addContactLeadModal');
        
        if (event.target === finishedModal) {
            closeAddFinishedClientModal();
        }
        if (event.target === leadModal) {
            closeAddContactLeadModal();
        }
    });
    
    // סגירת מודלים עם מקש ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAddFinishedClientModal();
            closeAddContactLeadModal();
        }
    });
}

// פונקציות מודלים - מאופטמות ומהירות
function openAddFinishedClientModal() {
    const modal = document.getElementById('addFinishedClientModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // איפוס והגדרת ברירות מחדל
        const form = document.getElementById('addFinishedClientForm');
        if (form) {
            form.reset();
            
            // תאריכי ברירת מחדל
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            const yesterdayStr = yesterday.toISOString().split('T')[0];
            
            const startDateInput = document.getElementById('add_start_date');
            const endDateInput = document.getElementById('add_end_date');
            const lastContactInput = document.getElementById('add_last_contact_date');
            
            if (startDateInput) startDateInput.value = yesterdayStr;
            if (endDateInput) endDateInput.value = yesterdayStr;
            if (lastContactInput) lastContactInput.value = new Date().toISOString().split('T')[0];
        }
    }
}

function closeAddFinishedClientModal() {
    const modal = document.getElementById('addFinishedClientModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function openAddContactLeadModal() {
    const modal = document.getElementById('addContactLeadModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // איפוס והגדרת ברירות מחדל
        const form = document.getElementById('addContactLeadForm');
        if (form) {
            form.reset();
            
            // תאריך קשר אחרון - היום
            const lastContactInput = document.getElementById('lead_last_contact_date');
            if (lastContactInput) {
                lastContactInput.value = new Date().toISOString().split('T')[0];
            }
        }
    }
}

function closeAddContactLeadModal() {
    const modal = document.getElementById('addContactLeadModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// פונקציה למחיקת מתאמנת - מאופטמת וידידותית
function deleteClient(clientId, clientName) {
    // וולידציה בסיסית
    if (!clientId || !clientName) {
        alert('❌ שגיאה: נתונים חסרים למחיקה');
        return;
    }
    
    const confirmation = confirm(
        `האם את בטוחה שברצונך למחוק את המתאמנת "${clientName}"?\n\n` +
        `⚠️ זוהי פעולה בלתי הפיכה!\n` +
        `כל הנתונים של המתאמנת יימחקו לצמיתות כולל:\n` +
        `• פרטים אישיים\n` +
        `• היסטוריית משקל\n` +
        `• הערות מעקב\n` +
        `• כל המידע הקשור אליה\n\n` +
        `האם להמשיך?`
    );
    
    if (!confirmation) return;
    
    // הצגת loading אופטימלי
    const loadingMessage = createLoadingMessage('🗑️ מוחקת מתאמנת...');
    document.body.appendChild(loadingMessage);
    
    // יצירת FormData לשליחה
    const formData = new FormData();
    formData.append('action', 'delete_client');
    formData.append('client_id', clientId);
    formData.append('nonce', finishedClientsData ? finishedClientsData.nonce : '');
    
    // שליחת בקשה עם error handling מלא
    fetch(finishedClientsData ? finishedClientsData.ajaxUrl : '/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        removeLoadingMessage(loadingMessage);
        
        if (data.success) {
            showSuccessMessage(`✅ המתאמנת "${clientName}" נמחקה בהצלחה!`);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showErrorMessage('❌ שגיאה: ' + (data.data || 'לא ניתן למחוק את המתאמנת'));
        }
    })
    .catch(error => {
        removeLoadingMessage(loadingMessage);
        console.error('Delete error:', error);
        showErrorMessage('❌ אירעה שגיאה במהלך המחיקה. בדוק את החיבור לאינטרנט ונסה שוב.');
    });
}

// פונקציה לפתיחת מודל עריכה
function openEditClientModal(clientId) {
    if (typeof window.openClientModal === 'function') {
        window.openClientModal(true, clientId);
    } else {
        console.warn('מודל עריכה לא זמין - הפונקציה openClientModal לא נמצאה');
        
        // fallback - הפניה לעמוד עריכה אם אין מודל
        if (confirm('האם לפתוח עריכה בעמוד נפרד?')) {
            window.location.href = `/clients/?edit=${clientId}`;
        }
    }
}

// פונקציות עזר לפידבק למשתמש
function createLoadingMessage(text) {
    const loadingMessage = document.createElement('div');
    loadingMessage.className = 'loading-overlay';
    loadingMessage.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(2px);
    `;
    
    const messageBox = document.createElement('div');
    messageBox.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        color: #374151;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 300px;
    `;
    messageBox.innerHTML = text;
    
    loadingMessage.appendChild(messageBox);
    return loadingMessage;
}

function removeLoadingMessage(loadingMessage) {
    if (loadingMessage && loadingMessage.parentNode) {
        loadingMessage.parentNode.removeChild(loadingMessage);
    }
}

function showSuccessMessage(text) {
    const message = document.createElement('div');
    message.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        font-weight: 600;
        z-index: 10000;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        animation: slideInRight 0.3s ease;
    `;
    message.textContent = text;
    
    document.body.appendChild(message);
    
    setTimeout(() => {
        if (message.parentNode) {
            message.parentNode.removeChild(message);
        }
    }, 4000);
}

function showErrorMessage(text) {
    const message = document.createElement('div');
    message.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ef4444;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        font-weight: 600;
        z-index: 10000;
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        animation: slideInRight 0.3s ease;
    `;
    message.textContent = text;
    
    document.body.appendChild(message);
    
    setTimeout(() => {
        if (message.parentNode) {
            message.parentNode.removeChild(message);
        }
    }, 6000);
}

// הוספת animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

// Export פונקציות גלובליות למודלים
window.openAddFinishedClientModal = openAddFinishedClientModal;
window.closeAddFinishedClientModal = closeAddFinishedClientModal;
window.openAddContactLeadModal = openAddContactLeadModal;
window.closeAddContactLeadModal = closeAddContactLeadModal;
window.deleteClient = deleteClient;
window.openEditClientModal = openEditClientModal; 