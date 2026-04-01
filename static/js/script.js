// Navigation
function goToPage(page) {
    window.location.href = page;
}

function goBack() {
    window.history.back();
}

function goPage(page) {
    window.location.href = page;
}

// Toast
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 2000);
}

