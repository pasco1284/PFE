document.getElementById('send-btn').addEventListener('click', function() {
    const email = document.getElementById('email').value;
    
    if (email) {
 
        
        document.getElementById('email-form').style.display = 'none';
        document.getElementById('code-verification-form').style.display = 'block';
    } 
});

document.getElementById('verify-btn').addEventListener('click', function() {
    const code = document.getElementById('verification-code').value;

    if (code === '123456') {  

      
        document.getElementById('code-verification-form').style.display = 'none';
        document.getElementById('new-password-form').style.display = 'block';
    }
});

document.getElementById('confirm-btn').addEventListener('click', function(event) {
    event.preventDefault();
    
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (newPassword === confirmPassword) {
    
        

        window.location.href = 'login.html';
    }
});