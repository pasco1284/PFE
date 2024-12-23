// script.js
document.addEventListener("DOMContentLoaded", () => {
    const steps = document.querySelectorAll(".form-step");
    let currentStep = 0;

    const showStep = (index) => {
        steps.forEach((step, i) => {
            step.classList.toggle("active", i === index);
        });
    };

    const validateStep = () => {
        const inputs = steps[currentStep].querySelectorAll("input");
        for (let input of inputs) {
            if (!input.value.trim()) {
                alert("Veuillez remplir tous les champs requis avant de continuer.");
                return false;
            }
        }
        return true;
    };

    document.querySelectorAll(".next-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
            if (validateStep() && currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    document.querySelectorAll(".prev-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });

    // Initial display
    showStep(currentStep);
});
