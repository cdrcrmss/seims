import './bootstrap';

const SUBMIT_LOADING_HTML = `
    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    Processing...
`;

function setButtonLoading(button) {
    if (!button.dataset.originalHtml) {
        button.dataset.originalHtml = button.innerHTML;
    }
    button.innerHTML = SUBMIT_LOADING_HTML;
    button.disabled = true;
}

function resetButton(button) {
    if (button.dataset.originalHtml) {
        button.innerHTML = button.dataset.originalHtml;
    }
    button.disabled = false;
}

function filenameFromDisposition(disposition) {
    if (!disposition) {
        return 'download';
    }

    const utf8Match = disposition.match(/filename\*=UTF-8''([^;]+)/i);
    if (utf8Match) {
        return decodeURIComponent(utf8Match[1]);
    }

    const match = disposition.match(/filename="?([^";\n]+)"?/i);
    return match ? match[1] : 'download';
}

function shouldSkipSubmitLoading(form) {
    if (!form) {
        return true;
    }

    if (form.hasAttribute('data-file-download')) {
        return true;
    }

    if (form.hasAttribute('x-data') || form.querySelector('[x-data]')) {
        return true;
    }

    return false;
}

async function handleFileDownloadSubmit(form) {
    const button = form.querySelector('button[type="submit"]');
    if (!button) {
        return;
    }

    setButtonLoading(button);

    try {
        const response = await fetch(form.action, {
            method: (form.method || 'POST').toUpperCase(),
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/octet-stream',
            },
            credentials: 'same-origin',
        });

        const disposition = response.headers.get('Content-Disposition') || '';
        if (!response.ok || !disposition.includes('attachment')) {
            throw new Error('Export failed');
        }

        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filenameFromDisposition(disposition);
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    } catch {
        window.alert('Export failed. Please try again.');
    } finally {
        resetButton(button);
    }
}

// Enhanced user interactions
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // File download forms: fetch blob so the button resets after download
    document.querySelectorAll('form[data-file-download]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFileDownloadSubmit(form);
        });
    });

    // Loading state for normal form submits (not file downloads)
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        const form = button.closest('form');
        if (shouldSkipSubmitLoading(form)) {
            return;
        }

        form.addEventListener('submit', function() {
            setButtonLoading(button);
        });
    });

    // Enhanced input focus effects
    document.querySelectorAll('input, textarea, select').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-blue-500/50');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-blue-500/50');
        });
    });

    // Add particle effect on click
    document.addEventListener('click', function(e) {
        if (e.target.matches('.btn-primary, button[class*="bg-gradient"]')) {
            createRipple(e);
        }
    });

    function createRipple(event) {
        const button = event.target;
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
        circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
        circle.classList.add('ripple');

        const ripple = button.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);
    }

    // Auto-hide notifications
    document.querySelectorAll('[data-auto-hide]').forEach(notification => {
        const delay = parseInt(notification.dataset.autoHide) || 5000;
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, delay);
    });
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
