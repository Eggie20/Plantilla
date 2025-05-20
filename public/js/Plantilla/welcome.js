// Set the current year
document.getElementById('currentYear').textContent = new Date().getFullYear();

// Back button functionality
function showText(element) {
    element.style.width = 'auto';
    element.style.padding = '0 20px';
    element.style.borderRadius = '20px';
    element.querySelector('span').style.fontSize = '16px';
}

function hideText(element) {
    element.style.width = '40px';
    element.style.padding = '0';
    element.style.borderRadius = '50%';
    element.querySelector('span').style.fontSize = '0';
}

function goBack() {
    window.history.back();
}

function openModal() {
    document.getElementById('customModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('customModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('customModal');
    if (event.target == modal) {
        closeModal();
    }
};
