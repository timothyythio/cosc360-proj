document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.getElementById('like-button');
    const likeIcon = document.getElementById('like-icon');
    const likeCountDisplay = document.getElementById('like-count-number');

    if (!likeBtn || !likeIcon || !likeCountDisplay) return;

    likeBtn.addEventListener('click', function () {
        const postId = this.dataset.postId;

        fetch('../pages/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${encodeURIComponent(postId)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                likeIcon.src = '../assets/heart-circle-coloured.jpg';
                likeCountDisplay.textContent = `${data.likes} likes`;
            } else {
                alert(data.message || 'Error processing like.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to process like.');
        });
    });
});
