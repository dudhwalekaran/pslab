document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        const icon = mobileMenuBtn.querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    });

    // Add smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Ensure dropdowns work on hover for larger screens with a delay
    const menuItems = document.querySelectorAll('.group');
    menuItems.forEach(item => {
        let timeout;
        item.addEventListener('mouseenter', () => {
            clearTimeout(timeout);
            const dropdown = item.querySelector('ul');
            if (dropdown) {
                dropdown.classList.remove('hidden');
            }
        });
        item.addEventListener('mouseleave', () => {
            const dropdown = item.querySelector('ul');
            if (dropdown) {
                timeout = setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200); // 200ms delay before closing
            }
        });
        // Keep dropdown open when hovering over it
        const dropdown = item.querySelector('ul');
        if (dropdown) {
            dropdown.addEventListener('mouseenter', () => {
                clearTimeout(timeout);
            });
            dropdown.addEventListener('mouseleave', () => {
                timeout = setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200);
            });
        }
    });
});