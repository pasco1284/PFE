
function afficherOptions() {
    const role = document.getElementById('role').value;
    const choixElements = document.getElementById('choixElements');
    const checkboxes = document.querySelectorAll('#checkboxes input[type="checkbox"]');

    if (role === 'etudiant') {
        choixElements.style.display = 'block';
      
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.disabled = false;
        });
    } else if (role === 'enseignant') {
        choixElements.style.display = 'block';
     
        checkboxes.forEach(checkbox => {
            checkbox.checked = false; 
            checkbox.disabled = false; 
        });
    } else {
        choixElements.style.display = 'none';
    }
}

function verifierSelection(selectedCheckbox) {
    const checkboxes = document.querySelectorAll('#checkboxes input[type="checkbox"]');


    if (document.getElementById('role').value === 'etudiant') {
        if (selectedCheckbox.checked) {
            checkboxes.forEach(checkbox => {
                if (checkbox !== selectedCheckbox) {
                    checkbox.disabled = true; 
                }
            });
        } else {
     
            checkboxes.forEach(checkbox => {
                checkbox.disabled = false; 
            });
        }
    }

}