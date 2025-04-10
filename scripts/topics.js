document.addEventListener('DOMContentLoaded', function() {
    fetchTopics();
});

function fetchTopics() {
    fetch('../php/get_topics.php')
        .then(response => response.json())
        .then(topics => {
            const topicContent = document.querySelector("#topicContent");
            topicContent.innerHTML = '';
            
            if (topics.length === 0) {
                topicContent.innerHTML = '<div class="no-topics">No topics found</div>';
                return;
            }
            
            topics.forEach(topic => {
                createTopicCard(
                    topic.name,
                    topic.description || 'No description available',
                    topic.id,
                    topic.follower_count || 0,
                    topic.post_count || 0,
                    topic.image_path
                );
            });
        })
        .catch(error => {
            console.error('Error fetching topics:', error);
            document.querySelector("#topicContent").innerHTML = 
                '<div class="error-message">Failed to load topics. Please try again later.</div>';
        });
}

function createReadMoreBtn(topicCard, desc) {
    let readMoreBtn = topicCard.querySelector(".read-more-btn");
    let topicText = topicCard.querySelector(".post-text");
    let isExpanded = false;

    readMoreBtn.addEventListener("click", () => {
        if (isExpanded) {
            topicText.innerText = desc.substring(0, 200) + "...";
            readMoreBtn.innerText = "Read More";
            isExpanded = false;
        } else {
            topicText.innerText = desc;
            readMoreBtn.innerText = "Show Less";
            isExpanded = true;
        }
    });
}

function createFollowBtn(topicCard, numMembers, numPosts) {
    let isFollowed = false;
    let followIcon = topicCard.querySelector(".likesIcon");
    let followCountSpan = topicCard.querySelector(".like-count");

    followIcon.addEventListener("click", function () {
        if (!isFollowed) {
            numMembers++;
            followIcon.src = "../assets/liked.png";
            isFollowed = true;
        } else {
            numMembers--;
            followIcon.src = "../assets/like.png"; 
            isFollowed = false;
        }
        followCountSpan.innerText = numMembers;
        
        // TODO: update follow status in database
    });
}

document.getElementById("sortbar").innerHTML = `
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
            <h2 id="new">New</h2>
        </nav>`;

function createTopicCard(topicName, desc, topicId, numMembers, numPosts, topicImg) {
    let topicCard = document.createElement("div");
    topicCard.className = "postCard";

    topicCard.innerHTML = `
        <div class="cardTitle">
            <h1><a href="topic.php?id=${topicId}">${topicName}</a></h1>
        </div>
        <div class="postPictureContainer">
            <img src="../assets/${topicImg}" class="postPicture" alt="${topicName}">
        </div>
        <div class="photoCardBody">
            <p class="post-text">${desc.substring(0, 200)}${desc.length > 200 ? '...' : ''}</p>
            ${desc.length > 200 ? '<span class="read-more-btn">Read More</span>' : ''}
        </div>
        <div class="cardFooter">
            <img src="../assets/like.png" alt="like icon" class="likesIcon">
            <p><span class="like-count">${numMembers}</span> Followers - ${numPosts} Posts</p>
        </div>
    `;

    if (desc && desc.length > 200) {
        topicCard.querySelector(".photoCardBody").appendChild(document.createElement("div")).className = "read-more-btn";
        createReadMoreBtn(topicCard, desc);
    }
    
    createFollowBtn(topicCard, numMembers, numPosts);

    document.querySelector("#topicContent").appendChild(topicCard);
}
