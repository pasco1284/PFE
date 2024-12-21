document.getElementById("fullname").textContent = "Dupont Jean";
document.getElementById("role").textContent = "Professeur";
document.getElementById("niveau").textContent = "Avancé";
document.getElementById('editButton').addEventListener('click', function() {
    document.getElementById('nom').removeAttribute('readonly');
    document.getElementById('prenom').removeAttribute('readonly');



    document.getElementById('editButton').style.display = 'none';
    document.getElementById('saveButton').style.display = 'block';
});
function toggleMenu() {
    const menu = document.getElementById('dropdownMenu');
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
    } else {
        menu.style.display = 'none';
    }
}


window.onclick = function(event) {
    if (!event.target.matches('#profileIcon')) {
        const dropdowns = document.getElementsByClassName("dropdown-menu");
        for (let i = 0; i < dropdowns.length; i++) {
            let openDropdown = dropdowns[i];
            if (openDropdown.style.display === 'block') {
                openDropdown.style.display = 'none';
            }
        }
    }
}

function setPreviousPage() {
    const currentPage = window.location.pathname;
    localStorage.setItem('previousPage', currentPage);
}


function goBack() {
    const previousPage = localStorage.getItem('previousPage');
    

    if (previousPage === 'http://localhost:4321/etudiant') {
        window.location.href = 'http://localhost:4321/etudiant';
    } else if (previousPage === 'http://localhost:4321/enseignant') {
        window.location.href = 'http://localhost:4321/enseignant';
    } else {
        window.location.href = 'http://localhost:4321/home';  
    }
}


document.getElementById('backLink').addEventListener('click', goBack);