/**
 * פופ-אפ מתאמנות - JavaScript
 */

class ClientModal {
    constructor() {
        this.modal = null;
        this.isEdit = false;
        this.currentClientId = null;
        this.init();
    }

    init() {
        // חיכה לטעינת הדף
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        this.modal = document.getElementById('client-modal');
        if (!this.modal) return;

        const modalClose = document.getElementById('modal-close');
        const modalBackdrop = document.getElementById('modal-backdrop');
        const cancelBtn = document.getElementById('cancel-btn');
        const clientForm = document.getElementById('client-form');

        // אירועי סגירה
        if (modalClose) modalClose.addEventListener('click', () => this.closeModal());
        if (modalBackdrop) modalBackdrop.addEventListener('click', () => this.closeModal());
        if (cancelBtn) cancelBtn.addEventListener('click', () => this.closeModal());

        // סגירה ב-ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.style.display === 'block') {
                this.closeModal();
            }
        });

        // שליחת טופס
        if (clientForm) {
            clientForm.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        // החלפה בין ליווי אישי לקבוצתי
        const trainingType = document.getElementById('training_type');
        if (trainingType) {
            trainingType.addEventListener('change', () => this.toggleTrainingType());
        }

        // הצגת פרטי תשלום
        const amountPaid = document.getElementById('amount_paid');
        if (amountPaid) {
            amountPaid.addEventListener('input', () => this.togglePaymentDetails());
        }

        // הצגת שדה מספר תשלומים עבור אשראי
        const paymentMethod = document.getElementById('payment_method');
        if (paymentMethod) {
            paymentMethod.addEventListener('change', () => this.toggleInstallments());
        }

        // העתקת משקל התחלתי למשקל נוכחי
        const startWeight = document.getElementById('start_weight');
        if (startWeight) {
            startWeight.addEventListener('input', () => this.copyStartWeight());
        }

        // עדכון תאריך סיום כאשר תאריך התחלה משתנה
        const startDate = document.getElementById('start_date');
        if (startDate) {
            startDate.addEventListener('change', () => this.updateEndDate());
        }

        // כפתור להוספת תשלום נוסף
        const addPaymentBtn = document.getElementById('add-payment-btn');
        if (addPaymentBtn) {
            addPaymentBtn.addEventListener('click', () => this.addAdditionalPayment());
        }

        // אפקטים ויזואליים
        this.setupFormEffects();

        // הוספת פונקציה גלובלית לפתיחת הפופ-אפ
        window.openClientModal = (isEdit = false, clientId = null) => {
            this.openModal(isEdit, clientId);
        };
    }

    setupFormEffects() {
        const formInputs = this.modal.querySelectorAll('input, select, textarea');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#3b82f6';
                this.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)';
            });

            input.addEventListener('blur', function() {
                if (!this.matches(':invalid')) {
                    this.style.borderColor = '#e5e7eb';
                    this.style.boxShadow = 'none';
                }
            });
        });
    }

    openModal(isEdit = false, clientId = null) {
        this.isEdit = isEdit;
        this.currentClientId = clientId;

        const modalTitle = document.getElementById('modal-title');
        const saveBtn = document.getElementById('save-btn');

        if (isEdit && clientId) {
            modalTitle.textContent = '✏️ עריכת מתאמנת';
            saveBtn.textContent = '💾 עדכן מתאמנת';
            document.getElementById('form-action').value = 'edit_client';
            document.getElementById('client-id').value = clientId;
            this.loadClientData(clientId);
        } else {
            modalTitle.textContent = '👥 הוספת מתאמנת חדשה';
            saveBtn.textContent = '✅ שמור מתאמנת';
            document.getElementById('form-action').value = 'add_client';
            document.getElementById('client-id').value = '';
            this.resetForm();
            this.setDefaultValues();
        }

        this.modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        this.modal.style.display = 'none';
        document.body.style.overflow = '';
        this.resetForm();
    }

    resetForm() {
        const clientForm = document.getElementById('client-form');
        clientForm.reset();
        
        document.getElementById('payment-details').style.display = 'none';
        document.getElementById('personal-mentor').style.display = 'block';
        document.getElementById('group-selection').style.display = 'none';
        document.getElementById('group_id').required = false;
        
        // הסתרת שדה מספר תשלומים וביטול חובה
        const installmentsSection = document.getElementById('installments-section');
        const installmentsInput = document.getElementById('installments');
        if (installmentsSection) {
            installmentsSection.style.display = 'none';
        }
        if (installmentsInput) {
            installmentsInput.required = false;
        }

        // איפוס תשלומים נוספים
        this.resetAdditionalPayments();

        // הסרת הודעות
        this.removeAllAlerts();
    }

    setDefaultValues() {
        document.getElementById('training_type').value = 'personal';
    }

    async loadClientData(clientId) {
        try {
            this.showAlert('info', 'טוען נתוני מתאמנת...');

            const formData = new FormData();
            formData.append('action', 'get_client_data_ajax');
            formData.append('client_id', clientId);
            formData.append('nonce', document.getElementById('form-nonce').value);

            const ajaxUrl = (typeof ajax_object !== 'undefined' && ajax_object.ajax_url) ? 
                           ajax_object.ajax_url : '/wp-admin/admin-ajax.php';

            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.populateForm(data.data);
                this.removeAlert('info');
            } else {
                this.showAlert('error', 'שגיאה בטעינת נתוני המתאמנת');
            }
        } catch (error) {
            console.error('שגיאה:', error);
            this.showAlert('error', 'שגיאה בחיבור לשרת');
        }
    }

    populateForm(clientData) {
        // מילוי השדות הבסיסיים
        Object.keys(clientData).forEach(key => {
            const field = document.getElementById(key);
            if (field && clientData[key] !== null && clientData[key] !== undefined) {
                // טיפול בגיל - אם הערך הוא 0, נשאיר את השדה ריק כדי שלא ייחשב כלא תקין
                let value = clientData[key];
                if (key === 'age' && (value === 0 || value === '0')) {
                    value = '';
                }
                field.value = value;
            }
        });

        // טיפול בשדות מיוחדים
        if (clientData.training_type === 'group') {
            document.getElementById('training_type').value = 'group';
            this.toggleTrainingType();
        }

        if (parseFloat(clientData.amount_paid || 0) > 0) {
            this.togglePaymentDetails();
        }

        // הפעלת לוגיקת מספר תשלומים אם נטענו נתוני תשלום
        if (clientData.payment_method) {
            this.toggleInstallments();
        }

        // טעינת תשלומים נוספים
        if (clientData.additional_payments && Array.isArray(clientData.additional_payments)) {
            this.loadAdditionalPayments(clientData.additional_payments);
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();

        if (!this.validateForm()) {
            return;
        }

        const saveBtn = document.getElementById('save-btn');
        saveBtn.classList.add('loading');
        saveBtn.disabled = true;

        try {
            const formData = new FormData(e.target);
            
            // הוספת action מותאם
            if (this.isEdit) {
                formData.set('action', 'edit_client_ajax');
            } else {
                formData.set('action', 'add_client_ajax');
            }

            const ajaxUrl = (typeof ajax_object !== 'undefined' && ajax_object.ajax_url) ? 
                           ajax_object.ajax_url : '/wp-admin/admin-ajax.php';

            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('success', data.data.message || 'המתאמנת נשמרה בהצלחה!');

                setTimeout(() => {
                    this.closeModal();
                    // רענון הדף או עדכון הרשימה
                    if (typeof updateClientsList === 'function') {
                        updateClientsList();
                    } else {
                        window.location.reload();
                    }
                }, 2000);
            } else {
                this.showAlert('error', data.data?.message || 'אירעה שגיאה בשמירה');
            }
        } catch (error) {
            console.error('שגיאה:', error);
            this.showAlert('error', 'שגיאה בחיבור לשרת');
        } finally {
            saveBtn.classList.remove('loading');
            saveBtn.disabled = false;
        }
    }

    validateForm() {
        const form = document.getElementById('client-form');
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = '#dc2626';
                isValid = false;
            } else {
                field.style.borderColor = '#e5e7eb';
            }
        });

        // בדיקת ולידציות מיוחדות
        const trainingType = document.getElementById('training_type').value;
        if (trainingType === 'group') {
            const groupId = document.getElementById('group_id');
            if (!groupId.value) {
                groupId.style.borderColor = '#dc2626';
                isValid = false;
            }
        }

        if (!isValid) {
            this.showAlert('error', 'אנא מלא את כל השדות הנדרשים');
        }

        return isValid;
    }

    toggleTrainingType() {
        const trainingType = document.getElementById('training_type').value;
        const personalMentor = document.getElementById('personal-mentor');
        const groupSelection = document.getElementById('group-selection');
        const groupSelect = document.getElementById('group_id');
        const mentorSelect = document.getElementById('mentor_id');

        if (trainingType === 'group') {
            personalMentor.style.display = 'none';
            groupSelection.style.display = 'block';
            groupSelect.required = true;
            mentorSelect.required = false;
            mentorSelect.value = '';
        } else {
            personalMentor.style.display = 'block';
            groupSelection.style.display = 'none';
            groupSelect.required = false;
            mentorSelect.required = false;
            groupSelect.value = '';
        }
    }

    togglePaymentDetails() {
        const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
        const paymentDetails = document.getElementById('payment-details');

        if (amountPaid > 0) {
            paymentDetails.style.display = 'block';
            // הגדרת תאריך ברירת מחדל
            const paymentDate = document.getElementById('payment_date');
            if (!paymentDate.value) {
                const startDateField = document.getElementById('start_date');
                const startDateValue = startDateField ? startDateField.value : '';

                // אם קיים תאריך התחלה – נשתמש בו כברירת מחדל, אחרת התאריך של היום
                paymentDate.value = startDateValue ? startDateValue : new Date().toISOString().split('T')[0];
            }
            // בדיקה אם צריך להציג שדה מספר תשלומים
            this.toggleInstallments();
        } else {
            paymentDetails.style.display = 'none';
            // הסתרת שדה מספר תשלומים וביטול חובה
            const installmentsSection = document.getElementById('installments-section');
            const installmentsInput = document.getElementById('installments');
            if (installmentsSection) {
                installmentsSection.style.display = 'none';
            }
            if (installmentsInput) {
                installmentsInput.required = false;
                installmentsInput.value = '';
            }
        }
    }

    toggleInstallments() {
        const paymentMethod = document.getElementById('payment_method');
        const installmentsSection = document.getElementById('installments-section');
        const installmentsInput = document.getElementById('installments');
        
        if (paymentMethod && installmentsSection && installmentsInput) {
            if (paymentMethod.value === 'credit') {
                installmentsSection.style.display = 'block';
                installmentsInput.required = true;
            } else {
                installmentsSection.style.display = 'none';
                installmentsInput.required = false;
                // איפוס הערך כשמסתירים
                installmentsInput.value = '';
            }
        }
    }

    copyStartWeight() {
        const startWeight = document.getElementById('start_weight');
        const currentWeight = document.getElementById('current_weight');
        
        if (startWeight.value && !currentWeight.value) {
            currentWeight.value = startWeight.value;
        }
    }

    updateEndDate() {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        
        if (startDate.value) {
            const startDateObj = new Date(startDate.value);
            if (startDateObj && !isNaN(startDateObj)) {
                // הוספת 3 חודשים לתאריך ההתחלה
                const endDateObj = new Date(startDateObj);
                endDateObj.setMonth(endDateObj.getMonth() + 3);
                
                // עדכון שדה תאריך הסיום
                endDate.value = endDateObj.toISOString().split('T')[0];
            }
        }
    }

    showAlert(type, message) {
        this.removeAlert(type);

        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        
        const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
        alert.innerHTML = `
            <span>${icon}</span>
            <span>${message}</span>
        `;

        const modalBody = document.querySelector('.modal-body');
        modalBody.insertBefore(alert, modalBody.firstChild);

        // גלילה לראש הפופ-אפ
        modalBody.scrollTop = 0;
    }

    removeAlert(type) {
        const existingAlert = this.modal.querySelector(`.alert-${type}`);
        if (existingAlert) {
            existingAlert.remove();
        }
    }

    removeAllAlerts() {
        const alerts = this.modal.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());
    }

    // פונקציות לניהול תשלומים נוספים
    addAdditionalPayment() {
        const container = document.getElementById('additional-payments-container');
        const list = document.getElementById('additional-payments-list');
        const paymentIndex = list.children.length + 1;

        const paymentHtml = this.createAdditionalPaymentHtml(paymentIndex);
        list.insertAdjacentHTML('beforeend', paymentHtml);

        // הצגת האזור
        container.style.display = 'block';

        // הוספת אירועים לשדות החדשים
        this.setupAdditionalPaymentEvents(paymentIndex);

        // עדכון כפתור ההוספה
        this.updateAddPaymentButton();
    }

    createAdditionalPaymentHtml(index) {
        const paymentMethods = {
            'cash': 'מזומן',
            'credit': 'כרטיס אשראי',
            'bank_transfer': 'העברה בנקאית',
            'paypal': 'PayPal',
            'bit': 'Bit'
        };

        const methodsOptions = Object.entries(paymentMethods)
            .map(([key, label]) => `<option value="${key}">${label}</option>`)
            .join('');

        return `
            <div class="additional-payment-item" data-payment-index="${index}">
                <div class="payment-header">
                    <h5>💳 תשלום #${index}</h5>
                    <button type="button" class="remove-payment-btn" onclick="clientModalInstance.removeAdditionalPayment(${index})">
                        🗑️ הסר
                    </button>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="additional_amount_${index}">סכום תשלום (₪) <span class="required">*</span></label>
                        <input type="number" id="additional_amount_${index}" name="additional_payments[${index}][amount]" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="additional_method_${index}">אמצעי תשלום</label>
                        <select id="additional_method_${index}" name="additional_payments[${index}][method]">
                            <option value="">בחר אמצעי תשלום...</option>
                            ${methodsOptions}
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="additional_date_${index}">תאריך תשלום</label>
                        <input type="date" id="additional_date_${index}" name="additional_payments[${index}][date]">
                    </div>
                    
                    <div class="form-group">
                        <label for="additional_installments_${index}">מספר תשלומים</label>
                        <input type="number" id="additional_installments_${index}" name="additional_payments[${index}][installments]" min="1" max="99" placeholder="הזן מספר תשלומים" style="display: none;">
                    </div>
                </div>
            </div>
        `;
    }

    setupAdditionalPaymentEvents(index) {
        // אירוע לשינוי אמצעי תשלום
        const methodSelect = document.getElementById(`additional_method_${index}`);
        if (methodSelect) {
            methodSelect.addEventListener('change', () => this.toggleAdditionalInstallments(index));
        }

        // הגדרת תאריך ברירת מחדל
        const dateInput = document.getElementById(`additional_date_${index}`);
        if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }
    }

    toggleAdditionalInstallments(index) {
        const methodSelect = document.getElementById(`additional_method_${index}`);
        const installmentsInput = document.getElementById(`additional_installments_${index}`);
        
        if (methodSelect && installmentsInput) {
            if (methodSelect.value === 'credit') {
                installmentsInput.style.display = 'block';
                installmentsInput.required = true;
            } else {
                installmentsInput.style.display = 'none';
                installmentsInput.required = false;
                installmentsInput.value = '';
            }
        }
    }

    removeAdditionalPayment(index) {
        const paymentItem = document.querySelector(`[data-payment-index="${index}"]`);
        if (paymentItem) {
            paymentItem.remove();
            this.updateAddPaymentButton();
            
            // אם אין יותר תשלומים נוספים, הסתר את האזור
            const list = document.getElementById('additional-payments-list');
            if (list.children.length === 0) {
                document.getElementById('additional-payments-container').style.display = 'none';
            }
        }
    }

    updateAddPaymentButton() {
        const addBtn = document.getElementById('add-payment-btn');
        const list = document.getElementById('additional-payments-list');
        const paymentCount = list.children.length;
        
        if (addBtn) {
            addBtn.textContent = `➕ הוסף תשלום נוסף (${paymentCount + 1})`;
        }
    }

    // פונקציה לאיפוס תשלומים נוספים
    resetAdditionalPayments() {
        const container = document.getElementById('additional-payments-container');
        const list = document.getElementById('additional-payments-list');
        
        if (container && list) {
            list.innerHTML = '';
            container.style.display = 'none';
            this.updateAddPaymentButton();
        }
    }

    // פונקציה לטעינת תשלומים נוספים
    loadAdditionalPayments(additionalPaymentsData) {
        const container = document.getElementById('additional-payments-container');
        const list = document.getElementById('additional-payments-list');
        
        if (!container || !list) return;
        
        additionalPaymentsData.forEach((payment, index) => {
            const paymentIndex = index + 1;
            const paymentHtml = this.createAdditionalPaymentHtml(paymentIndex);
            list.insertAdjacentHTML('beforeend', paymentHtml);
            
            // מילוי הנתונים
            const amountInput = document.getElementById(`additional_amount_${paymentIndex}`);
            const methodSelect = document.getElementById(`additional_method_${paymentIndex}`);
            const dateInput = document.getElementById(`additional_date_${paymentIndex}`);
            const installmentsInput = document.getElementById(`additional_installments_${paymentIndex}`);
            
            if (amountInput) amountInput.value = payment.amount || '';
            if (methodSelect) methodSelect.value = payment.method || '';
            if (dateInput) dateInput.value = payment.date || '';
            if (installmentsInput) installmentsInput.value = payment.installments || '';
            
            this.setupAdditionalPaymentEvents(paymentIndex);
        });
        
        // הצגת האזור
        container.style.display = 'block';
        this.updateAddPaymentButton();
    }
}

// אתחול המחלקה
let clientModalInstance;

// אתחול כשהדף נטען
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        clientModalInstance = new ClientModal();
    });
} else {
    clientModalInstance = new ClientModal();
}

// פונקציות עזר גלובליות
window.openAddClientModal = function() {
    if (window.openClientModal) {
        window.openClientModal(false);
    }
};

window.openEditClientModal = function(clientId) {
    if (window.openClientModal) {
        window.openClientModal(true, clientId);
    }
};

// פונקציה לפתיחת פופאפ צפייה בהערות
window.openViewClientModal = function(clientId) {
    // יצירת מודל צפייה
    const viewModal = document.createElement('div');
    viewModal.id = 'view-client-modal';
    viewModal.className = 'client-modal';
    viewModal.style.display = 'block';
    
    viewModal.innerHTML = `
        <div class="modal-backdrop"></div>
        <div class="modal-container">
            <div class="modal-header">
                <h2>📝 הערות המתאמנת</h2>
                <button type="button" class="modal-close" onclick="closeViewModal()">×</button>
            </div>
            <div class="modal-body">
                <div id="view-loading" style="text-align: center; padding: 40px; color: #d7dedc;">
                    🔄 טוען הערות...
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(viewModal);
    document.body.style.overflow = 'hidden';
    
    // טעינת הערות המתאמנת
    loadClientNotesForView(clientId);
};

// פונקציה לסגירת מודל הצפייה
window.closeViewModal = function() {
    const viewModal = document.getElementById('view-client-modal');
    if (viewModal) {
        viewModal.remove();
        document.body.style.overflow = '';
    }
};

// פונקציה לטעינת הערות מתאמנת לצפייה
async function loadClientNotesForView(clientId) {
    try {
        const formData = new FormData();
        formData.append('action', 'get_client_data_ajax');
        formData.append('client_id', clientId);
        formData.append('nonce', document.getElementById('form-nonce') ? 
                       document.getElementById('form-nonce').value : 
                       document.querySelector('[name*="nonce"]')?.value || '');

        const ajaxUrl = (typeof ajax_object !== 'undefined' && ajax_object.ajax_url) ? 
                       ajax_object.ajax_url : '/wp-admin/admin-ajax.php';

        const response = await fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            displayClientNotes(data.data);
        } else {
            document.getElementById('view-loading').innerHTML = '❌ שגיאה בטעינת הערות המתאמנת';
        }
    } catch (error) {
        console.error('שגיאה:', error);
        document.getElementById('view-loading').innerHTML = '❌ שגיאה בחיבור לשרת';
    }
}

// פונקציה להצגת הערות המתאמנת
function displayClientNotes(clientData) {
    const modalBody = document.querySelector('#view-client-modal .modal-body');
    
    const clientName = `${clientData.first_name || ''} ${clientData.last_name || ''}`.trim() || 'לא צוין';
    
    modalBody.innerHTML = `
        <div class="notes-view-container">
            <!-- פרטי המתאמנת -->
            <div class="client-info-header">
                <div class="client-name">
                    <strong>👤 ${clientName}</strong>
                </div>
                <div class="client-phone">
                    📞 <a href="tel:${clientData.phone}" style="color: #3b82f6; text-decoration: none;">${clientData.phone || 'לא צוין'}</a>
                </div>
            </div>
            
            <!-- הערות -->
            <div class="notes-section">
                ${clientData.notes ? `
                    <div class="notes-content">
                        ${clientData.notes.replace(/\n/g, '<br>')}
                    </div>
                ` : `
                    <div class="no-notes">
                        <div class="no-notes-icon">📝</div>
                        <div class="no-notes-text">אין הערות להצגה</div>
                        <div class="no-notes-subtitle">ניתן להוסיף הערות באמצעות עריכת פרטי המתאמנת</div>
                    </div>
                `}
            </div>
            
            <!-- פעולות מהירות -->
            <div class="quick-actions-notes">
                <button type="button" onclick="closeViewModal(); openEditClientModal(${clientData.client_id || clientData.ID});" class="action-btn primary">
                    ✏️ ערוך פרטים
                </button>
                <a href="https://wa.me/${clientData.phone && clientData.phone.startsWith('0') ? '972' + clientData.phone.substring(1) : clientData.phone}" target="_blank" class="action-btn whatsapp">
                    💬 וואצאפ
                </a>
            </div>
        </div>
    `;
}

// ===== פונקציות פופאפ מנטוריות =====

// פתיחת מודל הוספת מנטורית
function openAddMentorModal() {
    const modal = document.getElementById('mentor-modal');
    const title = document.getElementById('mentorModalTitle');
    const form = document.getElementById('mentorForm');
    const submitBtn = document.getElementById('mentor-save-btn');
    
    title.textContent = '👩‍💼 הוספת מנטורית חדשה';
    submitBtn.textContent = '✅ שמור מנטורית';
    
    // איפוס הטופס
    form.reset();
    document.getElementById('mentorFormType').value = 'add_mentor_ajax';
    document.getElementById('mentorId').value = '';
    document.getElementById('paymentPercentage').value = '40';
    
    // הסתרת הודעות
    document.getElementById('mentorModalMessage').style.display = 'none';
    
    // פתיחת המודל עם האפקט הנכון
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
    setTimeout(() => modal.classList.add('active'), 10);
    
    // פוקוס על השדה הראשון
    document.getElementById('mentorFirstName').focus();
}

// פתיחת מודל עריכת מנטורית
function openEditMentorModal(mentorId) {
    const modal = document.getElementById('mentor-modal');
    const title = document.getElementById('mentorModalTitle');
    const form = document.getElementById('mentorForm');
    const submitBtn = document.getElementById('mentor-save-btn');
    
    title.textContent = '✏️ עריכת מנטורית';
    submitBtn.textContent = '💾 עדכן מנטורית';
    
    // איפוס הטופס
    form.reset();
    document.getElementById('mentorFormType').value = 'edit_mentor_ajax';
    document.getElementById('mentorId').value = mentorId;
    
    // הסתרת הודעות
    document.getElementById('mentorModalMessage').style.display = 'none';
    
    // פתיחת המודל עם האפקט הנכון
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
    setTimeout(() => modal.classList.add('active'), 10);
    
    // טעינת נתוני המנטורית
    loadMentorData(mentorId);
}

// סגירת מודל מנטורית
function closeMentorModal() {
    const modal = document.getElementById('mentor-modal');
    modal.classList.remove('active');
    document.body.classList.remove('modal-open');
    setTimeout(() => {
        modal.style.display = 'none';
        document.getElementById('mentorForm').reset();
    }, 300);
}

// טעינת נתוני מנטורית
async function loadMentorData(mentorId) {
    try {
        showMentorLoading(true);
        
        const formData = new FormData();
        formData.append('action', 'get_mentor_data_ajax');
        formData.append('mentor_id', mentorId);
        formData.append('nonce', ajax_object.nonce);
        
        const response = await fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            
            // מילוי השדות
            document.getElementById('mentorFirstName').value = data.mentor_first_name || '';
            document.getElementById('mentorLastName').value = data.mentor_last_name || '';
            document.getElementById('mentorPhone').value = data.mentor_phone || '';
            document.getElementById('mentorEmail').value = data.mentor_email || '';
            document.getElementById('paymentPercentage').value = data.payment_percentage || '40';
            document.getElementById('mentorNotes').value = data.mentor_notes || '';
            
            document.getElementById('mentorFirstName').focus();
        } else {
            showMentorMessage('שגיאה בטעינת נתוני המנטורית: ' + (result.data.message || 'שגיאה לא ידועה'), 'error');
        }
    } catch (error) {
        console.error('Error loading mentor data:', error);
        showMentorMessage('שגיאה בטעינת נתוני המנטורית', 'error');
    } finally {
        showMentorLoading(false);
    }
}

// הצגת/הסתרת טעינה למנטורית
function showMentorLoading(show) {
    const submitBtn = document.getElementById('mentor-save-btn');
    
    if (show) {
        submitBtn.disabled = true;
        submitBtn.style.cursor = 'not-allowed';
        submitBtn.style.opacity = '0.6';
        submitBtn.textContent = '⏳ שומר...';
    } else {
        submitBtn.disabled = false;
        submitBtn.style.cursor = 'pointer';
        submitBtn.style.opacity = '1';
        // הטקסט יוחזר בפונקציות הפתיחה
    }
}

// הצגת הודעה למנטורית
function showMentorMessage(message, type = 'success') {
    const messageDiv = document.getElementById('mentorModalMessage');
    messageDiv.className = `modal-message ${type}`;
    messageDiv.innerHTML = message;
    messageDiv.style.display = 'block';
    
    // הסתרה אוטומטית אחרי 5 שניות אם זה הודעת הצלחה
    if (type === 'success') {
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }
}

// טיפול בשליחת טופס מנטורית
document.getElementById('mentorForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formAction = document.getElementById('mentorFormType').value;
    const isEdit = formAction === 'edit_mentor_ajax';
    
    // בדיקת שדות חובה
    const firstName = document.getElementById('mentorFirstName').value.trim();
    const lastName = document.getElementById('mentorLastName').value.trim();
    const phone = document.getElementById('mentorPhone').value.trim();
    
    if (!firstName || !lastName || !phone) {
        showMentorMessage('אנא מלא את כל השדות החובה', 'error');
        return;
    }
    
    try {
        showMentorLoading(true);
        showMentorMessage('', ''); // איפוס הודעות
        
        const formData = new FormData(this);
        formData.set('action', formAction);
        formData.set('mentor_nonce', ajax_object.nonce);
        
        const response = await fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMentorMessage(result.data.message || 'הפעולה בוצעה בהצלחה!', 'success');
            
            // סגירת המודל אחרי זמן קצר
            setTimeout(() => {
                closeMentorModal();
                // רענון הדף
                window.location.reload();
            }, 1500);
        } else {
            showMentorMessage('שגיאה: ' + (result.data.message || 'שגיאה לא ידועה'), 'error');
        }
    } catch (error) {
        console.error('Error submitting mentor form:', error);
        showMentorMessage('שגיאה בשליחת הטופס', 'error');
    } finally {
        showMentorLoading(false);
    }
});

// סגירת מודל מנטורית בלחיצה על רקע או ESC
document.getElementById('mentor-modal').addEventListener('click', function(e) {
    if (e.target === this || e.target.id === 'mentor-modal-backdrop') {
        closeMentorModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const mentorModal = document.getElementById('mentor-modal');
        const clientModal = document.getElementById('client-modal');
        
        if (mentorModal && mentorModal.style.display === 'block') {
            closeMentorModal();
        } else if (clientModal && clientModal.style.display === 'block') {
            closeClientModal();
        }
    }
}); 