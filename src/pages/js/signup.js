
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

document.querySelector('.form').addEventListener('submit', function(event) {
    event.preventDefault(); 

    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });


    fetch('/php/signup.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {

        if (data.role === 'Etudiant') {
            window.location.href = 'Etudiant.html'; 
        } else if (data.role === 'Enseignant') {
            window.location.href = 'Enseignant.html'; 
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur s\'est produite. Veuillez réessayer.');
    });
});