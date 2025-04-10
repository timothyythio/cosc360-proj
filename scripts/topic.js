document.addEventListener('DOMContentLoaded', function() {
    if (typeof topicData === 'undefined') {
        topicData = {
            id: 1,
            name: 'Topic Name',
            description: 'Topic Description',
            members: 0,
            image: 'siteicon.png'
        };
    }

    setTopicHeader(
        topicData.name,
        topicData.description,
        topicData.members,
        topicData.image
    );

    setupFollowButton();

    setupSortBar();

    fetchTopicPosts(topicData.id);
});

function setTopicHeader(topicName, desc, followCount, topicImg) {
    let topicNameEle = document.getElementById("topicName");
    let followCountEle = document.getElementById("topicFollows");
    let descEle = document.getElementById("topicDesc");
    let topicImgEle = document.getElementById("topicImg");

    if (topicNameEle) topicNameEle.textContent = topicName;
    if (followCountEle) followCountEle.textContent = `${followCount} Followers`;
    if (descEle) descEle.textContent = desc;
    if (topicImgEle) topicImgEle.src = `../assets/${topicImg}`;
}

function setupFollowButton() {
    let followBtn = document.getElementById("topicFollowBtn");
    let isFollowed = false; 

    if (followBtn) {
        followBtn.addEventListener("click", function() {
            isFollowed = !isFollowed;
            followBtn.textContent = isFollowed ? "Unfollow Topic" : "Follow Topic";
            
            // TODO: update follow status in database 
        });
    }
}

function setupSortBar() {
    let sortbar = document.getElementById("sortbar");
    if (sortbar) {
        sortbar.innerHTML = `
            <input id="toggle1" type="checkbox" />
                <label class="hamburger" for="toggle1">
                    <div class="top"></div>
                    <div class="meat"></div>
                    <div class="bottom"></div>
                </label>

            <nav class="burgerMenu">
                <h2 id="mostPopSelect">Most Popular</h2>
                <h2 id="hotSelect">Hot</h2>
                <h2 id="risingSelect">Rising</h2>
                <h2 id="newSelect">New</h2>
            </nav>`;
    }
}

function fetchTopicPosts(topicId) {
    fetch(`../php/get_topic_posts.php?id=${topicId}`)
        .then(response => response.json())
        .then(posts => {
            const postsContainer = document.getElementById("topicPosts");
            
            if (!postsContainer) {
                console.error("Posts container not found");
                return;
            }
            
            postsContainer.innerHTML = '';
            
            if (posts.error) {
                postsContainer.innerHTML = `<div class="error-message">${posts.error}</div>`;
                return;
            }
            
            if (posts.length === 0) {
                postsContainer.innerHTML = '<div class="no-posts">No posts in this topic yet.</div>';
                return;
            }
            
            posts.forEach(post => {
                if (post.image_path) {
                    createPhotoCard(
                        post.title,
                        post.content,
                        post.likes,
                        formatDate(post.created_at),
                        post.username,
                        post.post_id,
                        post.username,
                        post.image_path
                    );
                } else {
                    createTextCard(
                        post.title,
                        post.content,
                        post.likes,
                        formatDate(post.created_at),
                        post.username,
                        post.post_id,
                        post.username
                    );
                }
            });
        })
        .catch(error => {
            console.error('Error fetching posts:', error);
            document.getElementById("topicPosts").innerHTML = 
                '<div class="error-message">Failed to load posts. Please try again later.</div>';
        });
}

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
    
    if (!readMoreBtn || !postText) return;
    
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

function createLikeBtn(postCard, numLikes, postId, authorName) {
    let isLiked = false;
    let likesIcon = postCard.querySelector(".likesIcon");
    let likeCountSpan = postCard.querySelector(".like-count");
    
    if (!likesIcon || !likeCountSpan) return;

    likesIcon.addEventListener("click", function() {
        // redirect to login if not logged in
        if (typeof isLoggedIn !== 'undefined' && !isLoggedIn) {
            window.location.href = '../pages/login.php';
            return;
        }

        if (!isLiked) {
            numLikes++;
            likesIcon.src = "../assets/liked.png";
            isLiked = true;
        } else {
            numLikes--;
            likesIcon.src = "../assets/like.png";
            isLiked = false;
        }
        likeCountSpan.innerText = numLikes;
        
        fetch('../scripts/like_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `post_id=${postId}`
        })
        .then(response => response.json())
        .then(data => {
            console.log('Like status updated:', data);
        })
        .catch(error => {
            console.error('Error updating like:', error);
        });
    });
}

function createTextCard(title, bodyText, numLikes, timePosted, authorName, postId, poster) {
    let postCard = document.createElement("div");
    postCard.className = "postCard";

    const truncatedText = bodyText.length > 500 ? bodyText.substring(0, 500) + "..." : bodyText;
    const showReadMoreBtn = bodyText.length > 500;

    postCard.innerHTML = `
        <div class="cardTitle">
            <h1><a href="post.php?id=${postId}">${title}</a></h1>
        </div>
        <div class="textCardBody">
            <p class="post-text">${truncatedText}</p> 
            ${showReadMoreBtn ? '<span class="read-more-btn">Read More</span>' : ''}
        </div>
        <div class="cardFooter">
            <img src="../assets/like.png" alt="like icon" class="likesIcon">
            <p><span class="like-count">${numLikes}</span> Likes - ${timePosted} posted by <a href="profile.php?username=${poster}">${authorName}</a></p>
        </div>
    `;

    if (showReadMoreBtn) {
        createReadMoreBtn(postCard, bodyText);
    }
    
    createLikeBtn(postCard, numLikes, postId, authorName);

    document.getElementById("topicPosts").appendChild(postCard);
}

function createPhotoCard(title, bodyText, numLikes, timePosted, authorName, postId, poster, pictureFile) {
    let postCard = document.createElement("div");
    postCard.className = "postCard";

    const truncatedText = bodyText.length > 500 ? bodyText.substring(0, 500) + "..." : bodyText;
    const showReadMoreBtn = bodyText.length > 500;

    postCard.innerHTML = `
        <div class="cardTitle">
            <h1><a href="post.php?id=${postId}">${title}</a></h1>
        </div>
        <div class="postPictureContainer">
            <img src="../uploads/${pictureFile}" class="postPicture" alt="Post Picture" onerror="this.src='../assets/siteicon.png'">
        </div>
        <div class="photoCardBody">
            <p class="post-text">${truncatedText}</p> 
            ${showReadMoreBtn ? '<span class="read-more-btn">Read More</span>' : ''}
        </div>
        <div class="cardFooter">
            <img src="../assets/like.png" alt="like icon" class="likesIcon">
            <p><span class="like-count">${numLikes}</span> Likes - ${timePosted} posted by <a href="profile.php?username=${poster}">${authorName}</a></p>
        </div>
    `;

    if (showReadMoreBtn) {
        createReadMoreBtn(postCard, bodyText);
    }
    
    createLikeBtn(postCard, numLikes, postId, authorName);

    document.getElementById("topicPosts").appendChild(postCard);
}