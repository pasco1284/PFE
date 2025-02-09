document.addEventListener("DOMContentLoaded", function () {
    const steps = document.querySelectorAll(".form-step");
    const nextButtons = document.querySelectorAll(".next-btn");
    const prevButtons = document.querySelectorAll(".prev-btn");

    let currentStep = 0;

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle("active", i === index);
        });
    }

    nextButtons.forEach((btn, index) => {
        btn.addEventListener("click", () => {
            if (index === 0 && !document.querySelector("input[name='email']").value) {
                alert("Veuillez entrer un e-mail valide.");
                return;
            }
            currentStep++;
            showStep(currentStep);
        });
    });

    prevButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            currentStep--;
            showStep(currentStep);
        });
    });
});
