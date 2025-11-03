// Navegación del menú
document.addEventListener('DOMContentLoaded', function() {
    // Cambiar entre secciones del menú
    const menuButtons = document.querySelectorAll('.menu-btn');
    const menuSections = document.querySelectorAll('.menu-section');
    
    menuButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetSection = this.getAttribute('data-section');
            
            // Actualizar botones activos
            menuButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Mostrar sección correspondiente
            menuSections.forEach(section => {
                section.classList.remove('active');
                if (section.id === targetSection) {
                    section.classList.add('active');
                }
            });
        });
    });
    
    // Navegación a tests
    const testCards = document.querySelectorAll('.test-card');
    testCards.forEach(card => {
        card.addEventListener('click', function() {
            const testId = this.getAttribute('data-test-id');
            const category = this.getAttribute('data-category');
            window.location.href = `test.php?test_id=${testId}&category=${category}`;
        });
    });
    
    // Selección de opciones en el test
    const options = document.querySelectorAll('.option');
    options.forEach(option => {
        option.addEventListener('click', function() {
            const radioInput = this.querySelector('input[type="radio"]');
            if (radioInput) {
                radioInput.checked = true;
                
                // Actualizar visualmente la selección
                options.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            }
        });
    });
});