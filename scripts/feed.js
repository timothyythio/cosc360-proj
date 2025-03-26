function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString("en-US", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function createReadMoreBtn(postCard, bodyText) {
    let readMoreBtn = postCard.querySelector(".read-more-btn");
    let postText = postCard.querySelector(".post-text");
    let isExpanded = false;

    readMoreBtn.addEventListener("click", () => {
        if (isExpanded) {
            postText.innerText = bodyText.substring(0, 500) + "...";
            readMoreBtn.innerText = "Read More";
            isExpanded = false;
        } else {
            postText.innerText = bodyText;
            readMoreBtn.innerText = "Show Less";
            isExpanded = true;
        }
    });
}

function createLikeBtn(postCard, numLikes, authorName, postId) {
    let isLiked = false;
    let likesIcon = postCard.querySelector(".likesIcon");
    let likeCountSpan = postCard.querySelector(".like-count");
    let currentLikes = parseInt(numLikes) || 0;

    likesIcon.addEventListener("click", function () {
        if (!isLiked) {
            currentLikes++;
            likesIcon.src = "../assets/liked.png"; 
            isLiked = true;
        } else {
            currentLikes--;
            likesIcon.src = "../assets/like.png"; 
            isLiked = false;
        }
        likeCountSpan.innerText = currentLikes; 

    });
}

function createTextCard(post) {
    let postCard = document.createElement("div");
    postCard.className = "postCard";
    
    const timePosted = formatDate(post.created_at);
    const numLikes = 0; 

    postCard.innerHTML = `
        <div class="cardTitle">
            <h1><a href="post.php?id=${post.post_id}">${post.title}</a></h1>
        </div>
        <div class="textCardBody">
            <p class="post-text">${post.content.substring(0, 500)}...</p> 
            <span class="read-more-btn">Read More</span>
        </div>
        <div class="cardFooter">
            <img src="../assets/like.png" alt="like icon" class="likesIcon">
            <p><span class="like-count">${numLikes}</span> Likes - ${timePosted} posted by <a href="profile.php?username=${post.username}">${post.username}</a></p>
        </div>
    `;

    createReadMoreBtn(postCard, post.content);
    createLikeBtn(postCard, numLikes, post.username, post.post_id);

    document.getElementById("content").appendChild(postCard);
}

function createPhotoCard(post) {
    let postCard = document.createElement("div");
    postCard.className = "postCard";
    
    const timePosted = formatDate(post.created_at);
    const numLikes = 0; 
    const imagePath = post.image_path || "../assets/default-post-image.jpg";

    postCard.innerHTML = `
        <div class="postCardContent">
            <div class="cardTitle">
                <h1><a href="post.php?id=${post.post_id}">${post.title}</a></h1>
            </div>
            <div class="photoCardBody">
                <p class="post-text">${post.content.substring(0, 500)}...</p> 
                <span class="read-more-btn">Read More</span>
            </div>
            <div class="cardFooter">
                <img src="../assets/like.png" alt="like icon" class="likesIcon">
                <p><span class="like-count">${numLikes}</span> Likes - ${timePosted} posted by <a href="profile.php?username=${post.username}">${post.username}</a></p>
            </div>
        </div>
        <div class="postPictureContainer">
            <img src="${imagePath}" class="postPicture" alt="Post Picture">
        </div>
    `;

    createReadMoreBtn(postCard, post.content);
    createLikeBtn(postCard, numLikes, post.username, post.post_id);

    document.getElementById("content").appendChild(postCard);
}

function createTopicCard(post) {
    let postCard = document.createElement("div");
    postCard.className = "postCard";
    
    const timePosted = formatDate(post.created_at);
    const numLikes = 0; 
    const imagePath = post.image_path || "../assets/default-post-image.jpg";
    const topicName = post.topic_name || "General";

    postCard.innerHTML = `
        <div class="cardTitle">
            <h1><a href="post.php?id=${post.post_id}">${post.title}</a> 
                <span class="topicName"><a href="topic.php?name=${topicName}">Topic: ${topicName}</a></span>
            </h1>
        </div>
        <div class="topicPostPictureContainer">
            <img src="${imagePath}" class="postPicture" alt="Post Picture">
        </div>
        <div class="topicCardBody">
            <p class="post-text">${post.content.substring(0, 500)}...</p> 
            <span class="read-more-btn">Read More</span>
        </div>
        <div class="cardFooter">
            <img src="../assets/like.png" alt="like icon" class="likesIcon">
            <p><span class="like-count">${numLikes}</span> Likes - ${timePosted} posted by <a href="profile.php?username=${post.username}">${post.username}</a></p>
        </div>
    `;

    createReadMoreBtn(postCard, post.content);
    createLikeBtn(postCard, numLikes, post.username, post.post_id);

    document.getElementById("content").appendChild(postCard);
}

function displayPosts() {
    const contentDiv = document.getElementById("content");
    contentDiv.innerHTML = "";
    
    if (typeof postsData !== 'undefined' && postsData.length > 0) {
        postsData.forEach(post => {
            if (post.topic_name && post.image_path) {
                createTopicCard(post);
            } else if (post.image_path) {
                createPhotoCard(post);
            } else {
                createTextCard(post);
            }
        });
    } else {
        contentDiv.innerHTML = `
            <div class="no-posts">
                <h2>No posts available</h2>
                <p>Be the first to create a post!</p>
                <a href="new-post.php" class="create-post-btn">Create Post</a>
            </div>
        `;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    displayPosts();
});