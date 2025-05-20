document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tableSearch');
    const clearButton = document.getElementById('clearSearch');
    const searchIcon = document.querySelector('.search-icon');
    const tableBody = document.querySelector('#personnelTable tbody');

    // Add placeholder text animation
    function animatePlaceholder() {
        const placeholder = searchInput.getAttribute('placeholder');
        const animation = setInterval(() => {
            const current = searchInput.getAttribute('placeholder');
            const next = current === placeholder ? '' : placeholder;
            searchInput.setAttribute('placeholder', next);
        }, 1000);

        // Clear animation when user focuses on input
        searchInput.addEventListener('focus', () => {
            clearInterval(animation);
            searchInput.setAttribute('placeholder', placeholder);
        });

        // Restart animation when user blurs and input is empty
        searchInput.addEventListener('blur', () => {
            if (searchInput.value === '') {
                animatePlaceholder();
            }
        });
    }

    // Initialize search functionality
    function initSearch() {
        // Add search icon animation
        if (searchIcon) {
            searchIcon.addEventListener('mouseenter', () => {
                searchIcon.classList.add('fa-pulse');
            });

            searchIcon.addEventListener('mouseleave', () => {
                searchIcon.classList.remove('fa-pulse');
            });
        }

        // Handle search input
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterTable(searchTerm);
        });

        // Handle clear button
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                filterTable('');
                searchInput.focus();
            });
        }

        // Show/hide clear button based on input
        searchInput.addEventListener('input', function() {
            clearButton.style.display = this.value ? 'block' : 'none';
        });

        // Initial clear button state
        clearButton.style.display = searchInput.value ? 'block' : 'none';
    }

    // Filter table based on search term
    function filterTable(searchTerm) {
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll('tr.employee-data');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matches = text.includes(searchTerm);
            row.style.display = matches ? '' : 'none';
        });
    }

    // Initialize everything
    animatePlaceholder();
    initSearch();
});
