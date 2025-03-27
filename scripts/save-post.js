console.log("save-post.js is loaded and running!");

document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM Content Loaded event fired");
    
    const saveBtn = document.getElementById("save-btn");
    const saveIcon = document.getElementById("save-icon");
    const postContainer = document.getElementById("post-container");
    const postId = postContainer?.dataset?.postId;

    console.log("saveBtn:", saveBtn);
    console.log("saveIcon:", saveIcon);
    console.log("postId:", postId);

    if (!saveBtn || !postId) {
        console.error("Save button or post ID not found");
        return;
    }

    saveBtn.addEventListener("click", () => {
        console.log("Save button clicked, Post ID:", postId);

        fetch("../pages/save.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `post_id=${encodeURIComponent(postId)}`
        })
        .then(res => {
            console.log("Response status:", res.status);
            return res.json();
        })
        .then(data => {
            console.log("Server response:", data);
            if (data.success) {
                if (saveIcon) {
                    saveIcon.src = data.saved 
                        ? "../assets/bookmark-coloured.svg"
                        : "../assets/bookmark.svg";
                }
            } else {
                console.error("Save/unsave failed:", data.message || "Unknown error");
                alert(data.message || "Failed to save/unsave post");
            }
        })
        .catch(error => {
            console.error("Network or parsing error:", error);
            alert("An error occurred while saving the post");
        });
    });
});