document.addEventListener('DOMContentLoaded', function() {

    const postTitleInput = document.getElementById('postTitle');
    const postCaptionTextarea = document.getElementById('postCaption');
    const saveButton = document.getElementById('save-draft');
    const postButton = document.getElementById('submit-post');

    postButton.addEventListener('click', function(event) {
        validateAndSubmit(event, 'post');
    });

    saveButton.addEventListener('click', function(event) {
        validateAndSubmit(event, 'draft');
    });

    /**
     * Validates form fields and handles submission
     * @param {Event} event - The click event
     * @param {string} action - Either 'post' or 'draft'
     */
    function validateAndSubmit(event, action) {
        event.preventDefault();

        const title = postTitleInput.value.trim();
        const caption = postCaptionTextarea.value.trim();

        if (title === '' || caption === '') {
            alert('Please fill in both the title and caption fields before ' + 
                  (action === 'post' ? 'posting' : 'saving as draft'));

            if (title === '') {
                postTitleInput.classList.add('error');
            } else {
                postTitleInput.classList.remove('error');
            }
            
            if (caption === '') {
                postCaptionTextarea.classList.add('error');
            } else {
                postCaptionTextarea.classList.remove('error');
            }
            
            return false;
        }

        postTitleInput.classList.remove('error');
        postCaptionTextarea.classList.remove('error');

        if (action === 'post') {
            alert('Post submitted successfully!');
            //REDIRECT TO OTHER PAGE (FEED?) LATER
            clearForm();
        } else {
            alert('Draft saved successfully!');
        }

        return true;
    }

    function clearForm() {
        postTitleInput.value = '';
        postCaptionTextarea.value = '';
        const topicSelect = document.querySelector('select');
        if (topicSelect) {
            topicSelect.selectedIndex = 0;
        }
    }

    postTitleInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            this.classList.remove('error');
        }
    });

    postCaptionTextarea.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            this.classList.remove('error');
        }
    });
    const style = document.createElement('style');
    style.textContent = `
        .error {
            border: 2px solid red !important;
            background-color: rgba(255, 0, 0, 0.05);
        }
    `;
    document.head.appendChild(style);
});