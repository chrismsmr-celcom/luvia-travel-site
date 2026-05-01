// ============================================
// LUVIA - JAVASCRIPT PRINCIPAL
// ============================================

// Fonction pour changer la devise (globale)
function changeCurrency(currency) {
    document.cookie = "selected_currency=" + currency + "; path=/; max-age=" + (30 * 24 * 60 * 60);
    location.reload();
}

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // 1. MENU MOBILE
    // ============================================
    const menuBtn = document.getElementById('mobile-menu-button');
    const menuPanel = document.getElementById('mobile-menu');
    const closeMenuBtn = document.getElementById('close-menu-btn');
    const overlay = document.getElementById('mobile-overlay');
    
    function openMenu() {
        if(menuPanel) {
            menuPanel.classList.remove('hidden');
            if(overlay) overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
            document.body.classList.add('menu-open');
        }
    }
    
    function closeMenu() {
        if(menuPanel) {
            menuPanel.classList.add('hidden');
            if(overlay) overlay.style.display = 'none';
            document.body.style.overflow = '';
            document.body.classList.remove('menu-open');
        }
    }
    
    if(menuBtn && menuPanel) {
        menuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if(menuPanel.classList.contains('hidden')) {
                openMenu();
            } else {
                closeMenu();
            }
        });
    }
    
    if(closeMenuBtn) {
        closeMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMenu();
        });
    }
    
    // Fermer le menu en cliquant sur l'overlay
    if(overlay) {
        overlay.addEventListener('click', function() {
            closeMenu();
        });
    }
    
    if(menuPanel) {
        const menuLinks = menuPanel.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if(e.key === 'Escape' && menuPanel && !menuPanel.classList.contains('hidden')) {
            closeMenu();
        }
    });
    
    window.addEventListener('pageshow', function() {
        closeMenu();
    });
    
    // ============================================
    // 2. DROPDOWNS (Devise)
    // ============================================
    const currencyBtn = document.getElementById('currencyButton');
    const currencyDropdown = document.getElementById('currencyDropdown');
    
    if(currencyBtn && currencyDropdown) {
        currencyBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            currencyDropdown.classList.toggle('hidden');
        });
    }
    
    const mobileCurrencyBtn = document.getElementById('mobileCurrencyButton');
    const mobileCurrencyDropdown = document.getElementById('mobileCurrencyDropdown');
    
    if(mobileCurrencyBtn && mobileCurrencyDropdown) {
        mobileCurrencyBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileCurrencyDropdown.classList.toggle('hidden');
        });
    }
    
    document.addEventListener('click', function(e) {
        if(currencyDropdown && !currencyBtn.contains(e.target)) {
            currencyDropdown.classList.add('hidden');
        }
        if(mobileCurrencyDropdown && !mobileCurrencyBtn.contains(e.target)) {
            mobileCurrencyDropdown.classList.add('hidden');
        }
    });
    
    // ============================================
    // 3. NAVIGATION SCROLL EFFECT
    // ============================================
    const nav = document.querySelector('nav');
    
    window.addEventListener('scroll', function() {
        if(window.scrollY > 100) {
            nav.classList.add('shadow-lg');
        } else {
            nav.classList.remove('shadow-lg');
        }
    });
    
    // ============================================
    // 4. ANIMATION AU SCROLL
    // ============================================
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    animateElements.forEach(el => observer.observe(el));
    
    // ============================================
    // 5. VALIDATION DES FORMULAIRES
    // ============================================
    function showError(field, message) {
        let errorDiv = field.parentElement.querySelector('.error-message');
        if(!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
            field.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
    
    function removeError(field) {
        const errorDiv = field.parentElement.querySelector('.error-message');
        if(errorDiv) {
            errorDiv.remove();
        }
    }
    
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if(!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    showError(field, 'Ce champ est requis');
                } else {
                    field.classList.remove('border-red-500');
                    removeError(field);
                }
            });
            
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(email => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if(email.value && !emailRegex.test(email.value)) {
                    isValid = false;
                    showError(email, 'Email invalide');
                }
            });
            
            if(!isValid) {
                e.preventDefault();
            }
        });
    });
    
    // ============================================
    // 6. TOAST NOTIFICATIONS
    // ============================================
    window.showToast = function(message, type = 'success') {
        // Supprimer les toasts existants
        const existingToasts = document.querySelectorAll('.toast');
        existingToasts.forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast ${type === 'success' ? 'bg-green-500' : (type === 'warning' ? 'bg-yellow-500' : 'bg-red-500')} text-white px-6 py-3 rounded-lg shadow-lg fixed bottom-4 right-4 z-50 animate-fade-in`;
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'}
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };
    
    // ============================================
    // 7. LOADING SPINNER
    // ============================================
    window.showLoading = function() {
        const existingOverlay = document.getElementById('loading-overlay');
        if(existingOverlay) existingOverlay.remove();
        
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        overlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex flex-col items-center gap-4">
                <div class="loader"></div>
                <p class="text-gray-600 text-sm">Chargement en cours...</p>
            </div>
        `;
        document.body.appendChild(overlay);
    };
    
    window.hideLoading = function() {
        const overlay = document.getElementById('loading-overlay');
        if(overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => overlay.remove(), 300);
        }
    };
    
    // ============================================
    // 8. BACK TO TOP BUTTON
    // ============================================
    const backToTop = document.createElement('button');
    backToTop.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l7.5-7.5 7.5 7.5m-7.5-7.5v15" />
        </svg>
    `;
    backToTop.className = 'fixed bottom-6 right-6 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition z-40 hidden';
    backToTop.setAttribute('aria-label', 'Retour en haut');
    document.body.appendChild(backToTop);
    
    window.addEventListener('scroll', function() {
        if(window.scrollY > 300) {
            backToTop.classList.remove('hidden');
        } else {
            backToTop.classList.add('hidden');
        }
    });
    
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // ============================================
// 9. DATE PICKER - Désactiver les dates passées (SAUF pour la date de naissance)
// ============================================
function disablePastDates() {
    const today = new Date().toISOString().split('T')[0];
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // ✅ EXCLURE la date de naissance
        if(input.name === 'birth_date' || input.id === 'birth_date') {
            return; // Ne pas appliquer la restriction
        }
        
        input.setAttribute('min', today);
        
        if(input.value && input.value < today) {
            input.value = today;
        }
        
        input.addEventListener('change', function() {
            if(this.value && this.value < today) {
                this.value = today;
                if(window.showToast) {
                    showToast('Vous ne pouvez pas sélectionner une date passée', 'warning');
                }
            }
        });
    });
}

disablePastDates();

const dateObserver = new MutationObserver(function(mutations) {
    let shouldUpdate = false;
    mutations.forEach(function(mutation) {
        if(mutation.addedNodes.length) {
            shouldUpdate = true;
        }
    });
    if(shouldUpdate) disablePastDates();
});

dateObserver.observe(document.body, { childList: true, subtree: true });

    // ============================================
    // 10. CONFIRMATION AVANT ACTION
    // ============================================
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Êtes-vous sûr de vouloir continuer ?';
            if(!confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
    
    // ============================================
    // 11. PRÉVENTION DOUBLE SOUMISSION
    // ============================================
    forms.forEach(form => {
        let submitted = false;
        form.addEventListener('submit', function(e) {
            if(submitted) {
                e.preventDefault();
                if(window.showToast) {
                    showToast('Veuillez patienter, traitement en cours...', 'warning');
                }
            } else {
                submitted = true;
                setTimeout(() => {
                    submitted = false;
                }, 5000);
            }
        });
    });
    
    // ============================================
    // 12. MISE À JOUR AUTO DU COPYRIGHT
    // ============================================
    const copyrightElement = document.querySelector('footer p:last-child');
    if(copyrightElement && copyrightElement.textContent.includes('2026')) {
        const year = new Date().getFullYear();
        copyrightElement.textContent = copyrightElement.textContent.replace('2026', year);
    }
    
    // ============================================
    // 13. INITIALISATION DES LIENS EXTERNES
    // ============================================
    const externalLinks = document.querySelectorAll('a[target="_blank"]');
    externalLinks.forEach(link => {
        link.setAttribute('rel', 'noopener noreferrer');
    });
    
    // ============================================
    // 14. GESTION DES IMAGES (LAZY LOADING)
    // ============================================
    if('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    if(src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    console.log('Luvia - Site chargé avec succès');
});
