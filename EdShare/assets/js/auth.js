document.addEventListener('DOMContentLoaded', function () {
    const signUpForm = document.getElementById('signUpForm');
  
    signUpForm.addEventListener('submit', function (event) {
      // Prevent the form from submitting
      event.preventDefault();
  
      // Reset error messages
      const errorMessages = document.querySelectorAll('[id^="inv-"]');
      errorMessages.forEach(function (errorMessage) {
        errorMessage.style.display = 'none';
      });
  
      // Validate form fields
      const username = document.getElementById('username').value.trim();
      const firstName = document.getElementById('firstName').value.trim();
      const lastName = document.getElementById('lastName').value.trim();
      const university = document.getElementById('university').value.trim();
      const email = document.getElementById('email').value.trim();
      const universityAcronym = document.getElementById('universityAcronym').value.trim();
      const password = document.getElementById('password').value.trim();
      let invalid = false; 
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      
      if (!emailRegex.test(email)) {
        document.getElementById('inv-email').style.display = 'block';
        invalid = true;
      }
      if (username.length < 8) {
        document.getElementById('inv-username').style.display = 'block';
        invalid = true;
      }
  
      if (firstName === '') {
        document.getElementById('inv-firstname').style.display = 'block';
        invalid = true;
      }
  
      if (lastName === '') {
        document.getElementById('inv-lastname').style.display = 'block';
        invalid = true;
      }
  
      if (university === '') {
        document.getElementById('inv-university').style.display = 'block';
        invalid = true;
      }
  
      if (universityAcronym === '') {
        document.getElementById('inv-universityAcronym1').style.display = 'block';
        invalid = true;
      }
  
      if (universityAcronym.length < 2 || universityAcronym.length > 4) {
        document.getElementById('inv-universityAcronym2').style.display = 'block';
        invalid = true;
      }
  
      if (password.length < 8) {
        document.getElementById('inv-password').style.display = 'block';
        invalid = true;
      }

      if (invalid){
        return;
      }
      // If all fields are valid, submit the form
      signUpForm.submit();
    });
  });
