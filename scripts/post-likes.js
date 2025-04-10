// Create a file: ../scripts/post-likes.js
document.addEventListener('DOMContentLoaded', function() {
    // Get the necessary elements
    const likeButton = document.getElementById('like-button');
    const likeIcon = document.getElementById('like-icon');
    const likeCountDisplay = document.getElementById('like-count-number');
    
    // Exit if any elements are missing
    if (!likeButton || !likeIcon || !likeCountDisplay) {
        console.error("Missing required elements for like functionality");
        return;
    }
    
    // Get post ID from button's data attribute
    const postId = likeButton.getAttribute('data-post-id');
    if (!postId) {
        console.error("No post ID found");
        return;
    }
    
    let currentLikes = parseInt(likeCountDisplay.innerText) || 0;
    let isLiked = likeIcon.classList.contains("liked"); // Optional way to track state
    // Set up click event for like button
    likeIcon.addEventListener("click", function () {
        fetch("../php/like_post.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "post_id=" + encodeURIComponent(postId)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "liked") {
                likeIcon.src = '../assets/heart-circle-coloured.jpg';
                currentLikes++;
                isLiked = true;
            } else if (data.status === "unliked") {
                likeIcon.src = '../assets/heart-circle-svgrepo-com.svg';
                currentLikes--;
                isLiked = false;
            }
            likeCountDisplay.innerText = currentLikes + " likes";
        })
        .catch(error => {
            console.error("Error updating like:", error);
        });
    });

});