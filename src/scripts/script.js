
	                    let currentSlide = 0;
                const slides = document.querySelectorAll('.slide');
                const texts = document.querySelectorAll('.slide-text');
            
                function showSlide(index) {
                    slides.forEach((slide, i) => {
                        slide.classList.toggle('active', i === index);
                        texts[i].classList.toggle('active', i === index);
                    });
                }
            
                function nextSlide() {
                    currentSlide = (currentSlide + 1) % slides.length; // Cycle à travers les diapositives
                    showSlide(currentSlide);
                }
            
                // Changez de diapositive toutes les 5 secondes
                setInterval(nextSlide, 5000);
            
                // Afficher la première diapositive au chargement
                showSlide(currentSlide);
