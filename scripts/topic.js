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

    setupFollowButton(topicData.id);

    setupSortBar();

    // set default sort to popular
    fetchTopicPosts(topicData.id, 'popular');
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

function setupFollowButton(topicId) {
    let followBtn = document.getElementById("topicFollowBtn");
    if (!followBtn) return;
    
    // redirect to login on follow btn click if not logged in
    if (typeof isLoggedIn !== 'undefined' && !isLoggedIn) {
        followBtn.addEventListener("click", function() {
            window.location.href = '../pages/login.php';
        });
        return;
    }
    
    // check if user already follows this topic
    fetch(`../php/check_topic_follow.php?topic_id=${topicId}`)
        .then(response => response.json())
        .then(data => {
            updateFollowButtonUI(followBtn, data.follows);
            
            followBtn.addEventListener("click", function() {
                const formData = new FormData();
                formData.append('topic_id', topicId);
                
                fetch('../php/follow_topic.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    // update ui with new follow status
                    const isFollowed = data.status === 'followed';
                    updateFollowButtonUI(followBtn, isFollowed);
                    
                    // update follower count
                    const followCountEle = document.getElementById("topicFollows");
                    if (followCountEle) {
                        followCountEle.textContent = `${data.followers} Followers`;
                    }
                })
                .catch(error => {
                    console.error('Error following topic:', error);
                });
            });
        })
        .catch(error => {
            console.error('Error checking follow status:', error);
        });
}

function updateFollowButtonUI(button, isFollowed) {
    if (isFollowed) {
        button.textContent = "Unfollow Topic";
        button.classList.add("following");
    } else {
        button.textContent = "Follow Topic";
        button.classList.remove("following");
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
                <h2 id="mostPopSelect" class="sort-option active">Most Popular</h2>
                <h2 id="hotSelect" class="sort-option">Hot</h2>
                <h2 id="newSelect" class="sort-option">New</h2>
            </nav>`;
            
        // event listeners for sort options
        document.getElementById("mostPopSelect").addEventListener("click", function() {
            setActiveSort(this);
            fetchTopicPosts(topicData.id, 'popular');
        });
        
        document.getElementById("hotSelect").addEventListener("click", function() {
            setActiveSort(this);
            fetchTopicPosts(topicData.id, 'hot');
        });
        
        document.getElementById("newSelect").addEventListener("click", function() {
            setActiveSort(this);
            fetchTopicPosts(topicData.id, 'new');
        });
    }
}

function setActiveSort(element) {
    document.querySelectorAll('.sort-option').forEach(el => {
        el.classList.remove('active');
    });
    
    element.classList.add('active');
    
    const burgerMenu = document.querySelector('.burgerMenu');
    
    // reorder the menu items once option is selected
    if (burgerMenu && element.parentNode === burgerMenu) {
        burgerMenu.insertBefore(element, burgerMenu.firstChild);
    }
    
    document.getElementById('toggle1').checked = false;
}

function fetchTopicPosts(topicId, sortBy = 'popular') {
    const postsContainer = document.getElementById("topicPosts");
    if (!postsContainer) {
        console.error("Posts container not found");
        return;
    }
    
    postsContainer.innerHTML = '<div class="loading">Loading posts...</div>';
    
    fetch(`../php/get_topic_posts.php?id=${topicId}&sort=${sortBy}`)
        .then(response => response.json())
        .then(posts => {
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
            postsContainer.innerHTML = 
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
        
        const formData = new FormData();
        formData.append('post_id', postId);
        
        fetch('../php/like_post.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.log('Response was not valid JSON:', text);
                    return { status: 'unknown' };
                }
            });
        })
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