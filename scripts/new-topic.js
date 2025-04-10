document.addEventListener('DOMContentLoaded', function() {

    const topicNameInput = document.getElementById('topicName');
    const topicDescriptionTextarea = document.getElementById('topicDescription');
    const createButton = document.getElementById('submit-topic');

    if (createButton) {
        createButton.addEventListener('click', function(event) {
            validateAndSubmit(event);
        });
    }

    /**
     * validates form fields and handle submission
     * @param {Event} event
     */
    function validateAndSubmit(event) {
        event.preventDefault();

        const topicName = topicNameInput.value.trim();
        const description = topicDescriptionTextarea.value.trim();

        if (topicName === '' || description === '') {
            alert('Please fill in both the topic name and description fields before creating the topic');

            if (topicName === '') {
                topicNameInput.classList.add('error');
            } else {
                topicNameInput.classList.remove('error');
            }
            
            if (description === '') {
                topicDescriptionTextarea.classList.add('error');
            } else {
                topicDescriptionTextarea.classList.remove('error');
            }
            
            return false;
        }

        topicNameInput.classList.remove('error');
        topicDescriptionTextarea.classList.remove('error');

        document.getElementById('topicForm').submit();
        
        return true;
    }

    if (topicNameInput) {
        topicNameInput.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('error');
            }
        });
    }

    if (topicDescriptionTextarea) {
        topicDescriptionTextarea.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('error');
            }
        });
    }

    const style = document.createElement('style');
    style.textContent = `
        .error {
            border: 2px solid red !important;
            background-color: rgba(255, 0, 0, 0.05);
        }
    `;
    document.head.appendChild(style);
});

const imageInput = document.getElementById('new-topic-image');
const previewImage = document.getElementById('new-topic-preview');
const uploadIcon = document.getElementById('upload-icon');

if (imageInput && previewImage && uploadIcon) {
    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                uploadIcon.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            previewImage.src = '#';
            previewImage.style.display = 'none';
            uploadIcon.style.display = 'block';
        }
    });
}