document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const fileInputContainer = document.querySelector('.file-input-container');
    
    // Listen for file selection
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            // Check if a file is selected
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Check if the selected file is an image
                if (file.type.match('image.*')) {
                    // Create a FileReader to read the image
                    const reader = new FileReader();
                    
                    // Set up the FileReader to display the image when loaded
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.style.display = 'block';
                        fileInputContainer.classList.add('has-image');
                    };
                    
                    // Read the image file
                    reader.readAsDataURL(file);
                } else {
                    // Not an image file, hide the preview
                    imagePreviewContainer.style.display = 'none';
                    fileInputContainer.classList.remove('has-image');
                    alert('Please select an image file (JPEG, PNG, or GIF).');
                    imageInput.value = ''; // Clear the file input
                }
            } else {
                // No file selected, hide the preview
                imagePreviewContainer.style.display = 'none';
                fileInputContainer.classList.remove('has-image');
            }
        });
    }

    // Add code for the remove button
    const removeImageBtn = document.getElementById('removeImage');
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = ''; // Clear the file input
            imagePreviewContainer.style.display = 'none'; // Hide the preview
            fileInputContainer.classList.remove('has-image');
        });
    }
    
    // Hamburger menu functionality - updated for existing button in HTML
    const hamburger = document.querySelector('.hamburger');
    if (hamburger) {
        console.log('Found hamburger button');
        
        // Add click event listener to the hamburger button
        hamburger.addEventListener('click', function() {
            console.log('Hamburger clicked');
            const navList = document.querySelector('nav ul');
            if (navList) {
                navList.classList.toggle('show');
                console.log('Toggled menu visibility');
            }
        });
    } else {
        console.log('Hamburger button not found');
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const navList = document.querySelector('nav ul');
        const hamburger = document.querySelector('.hamburger');
        
        if (navList && 
            navList.classList.contains('show') && 
            event.target !== hamburger && 
            event.target !== navList && 
            !navList.contains(event.target)) {
            navList.classList.remove('show');
        }
    });
});