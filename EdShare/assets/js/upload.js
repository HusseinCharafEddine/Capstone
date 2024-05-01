document.addEventListener('DOMContentLoaded', function () {
    const uploadForm = document.getElementById('uploadForm');

    uploadForm.addEventListener('submit', function (event) {
        // Prevent the form from submitting
        event.preventDefault();

        // Reset error messages
        const errorMessages = document.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(function (errorMessage) {
            errorMessage.style.display = 'none';
        });

        // Validate form fields
        const courseName = document.getElementById('course-name-input').value.trim();
        const courseCode = document.getElementById('course-code-input').value.trim();
        const title = document.querySelector('[name="title"]').value.trim();
        const category = document.getElementById('category-input').value.trim();
        const type = document.getElementById('type-hidden').value.trim();
        const file = document.getElementById('inputGroupFile02').value.trim();
        let invalid = false;

        if (courseName === '') {
            document.getElementById('inv-course-name').style.display = 'block';
            invalid = true;
        }

        if (courseCode === '') {
            document.getElementById('inv-course-code').style.display = 'block';
            invalid = true;
        }

        if (title === '') {
            document.getElementById('inv-title').style.display = 'block';
            invalid = true;
        }

        if (category === '') {
            document.getElementById('inv-category').style.display = 'block';
            invalid = true;
        }

        if (type === '') {
            document.getElementById('inv-type').style.display = 'block';
            invalid = true;
        }

        if (file === '') {
            document.getElementById('inv-file').style.display = 'block';
            invalid = true;
        }

        if (invalid) {
            return;
        }
        showLoadingSpinner();
        // If all fields are valid, submit the form
        uploadForm.submit();
    });
});
function showLoadingSpinner() {
    console.log("hit 1");
    document.getElementById('loadingContainer').style.display = 'block';
}

// Function to hide the loading spinner
function hideLoadingSpinner() {
    console.log("hit 2");
    document.getElementById('loadingContainer').style.display = 'none';
}

// Simulate upload completion after 3 seconds (you can replace this with actual upload logic)
setTimeout(function () {
    hideLoadingSpinner(); // Hide loading spinner after 3 seconds (simulating upload completion)
}, 3000);