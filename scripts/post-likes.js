document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.getElementById('like-button');
    const likeIcon = document.getElementById('like-icon');
    const likeCountDisplay = document.getElementById('like-count-number');
    const postId = likeBtn?.dataset?.postId;

    if (!likeBtn || !likeIcon || !likeCountDisplay) return;

    fetch(`../pages/like.php?post_id=${encodeURIComponent(postId)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.liked) {
                likeIcon.src = '../assets/heart-circle-coloured.jpg';
            }
            likeCountDisplay.textContent = `${data.likes} likes`;
        })
        .catch(err => console.error("Failed to fetch like status", err));

    likeBtn.addEventListener('click', function () {
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
                likeIcon.src = data.liked
                    ? '../assets/heart-circle-coloured.jpg'
                    : '../assets/heart-circle-svgrepo-com.svg';
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