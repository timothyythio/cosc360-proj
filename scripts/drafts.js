document.addEventListener('DOMContentLoaded', () => {
    const draftsContainer = document.querySelector('.drafts-container');

    draftsContainer.addEventListener('click', (e) => {
        const draftCard = e.target.closest('.draft-card');
        if (draftCard && !e.target.closest('.delete-draft')) {
            const title = draftCard.dataset.title;
            const content = draftCard.dataset.content;
            const topic = draftCard.dataset.topic;
            const draftId = draftCard.dataset.draftId;
            window.location.href = `new-post.php?draft_id=${draftId}&title=${encodeURIComponent(title)}&content=${encodeURIComponent(content)}&topic=${encodeURIComponent(topic)}`;
        }
    });

    draftsContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('delete-draft')) {
            const draftId = e.target.dataset.draftId;
            
            fetch('draft.php?action=delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `draft_id=${draftId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {

                    e.target.closest('.draft-card').remove();
                    if (draftsContainer.querySelectorAll('.draft-card').length === 0) {
                        draftsContainer.innerHTML = '<p class="no-drafts">Currently no drafts</p>';
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete draft');
            });
        }
    });
});