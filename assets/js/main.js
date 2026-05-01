document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            mobileMenuBtn.querySelector('i').classList.toggle('fa-bars');
            mobileMenuBtn.querySelector('i').classList.toggle('fa-times');
        });
    }

    // FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const header = item.querySelector('h4');
        header.addEventListener('click', () => {
            const icon = header.querySelector('i');
            const content = item.querySelector('p');
            
            if (content) {
                content.style.display = content.style.display === 'block' ? 'none' : 'block';
                icon.classList.toggle('fa-plus-circle');
                icon.classList.toggle('fa-minus-circle');
            } else if (!item.querySelector('p')) {
                // Add demo text if not present
                const p = document.createElement('p');
                p.style.marginTop = '15px';
                p.style.color = 'var(--text-muted)';
                p.style.fontSize = '0.9rem';
                p.textContent = "Please contact our support at laemma50@gmail.com for detailed information regarding this question.";
                item.appendChild(p);
                icon.classList.remove('fa-plus-circle');
                icon.classList.add('fa-minus-circle');
            }
        });
    });

    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
});
