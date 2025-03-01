var topicName;
var desc;
var topicId;
var numMembers;
var numPosts;
var topicImg;

//third post example
topicName = "Hololive";
topicId = "topic";
topicImg = "foob.jpg";
desc = "Hololive Production (Japanese: ホロライブプロダクション) is a virtual YouTuber agency owned by Japanese tech entertainment company Cover Corporation. In addition to acting as a multi-channel network, Hololive Production also handles licensing, merchandising, music production and concert organization. As of November 2024, the agency manages over 90 VTubers in three target languages (Japanese, Indonesian and English), totaling over 80 million subscribers, including several of the most subscribed VTubers on YouTube and some of the most watched female streamers in the world.";
numMembers = 99999;
numPosts = 39183214125;

function createReadMoreBtn(postCard, desc) {
    let readMoreBtn = postCard.querySelector(".read-more-btn");
    let postText = postCard.querySelector(".post-text");
    let isExpanded = false;

    readMoreBtn.addEventListener("click", () => {
        if (isExpanded) {
            postText.innerText = desc.substring(0, 500) + "...";
            readMoreBtn.innerText = "Read More";
            isExpanded = false;
        } else {
            postText.innerText = desc;
            readMoreBtn.innerText = "Show Less";
            isExpanded = true;
        }
    });
}

function createFollowBtn(postCard, numMembers, numPosts) {
    let isFollowed = false;
    let followIcon = postCard.querySelector(".likesIcon");
    let followCountSpan = postCard.querySelector(".like-count");

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
            <h2 id="hotSelecT">Hot</h2>
            <h2 id="risingSelect">Rising</h2>
            <h2 id="new">New</h2>
        </nav>`;

        function createPhotoCard(topicName, desc, topicId, numMembers, numPosts, topicImg) {
            let postCard = document.createElement("div");
            postCard.className = "postCard";
        
            postCard.innerHTML = `
                <div class="cardTitle">
                    <h1><a href="${topicId}.html">${topicName}</a></h1>
                </div>
                <div class="postPictureContainer">
                    <img src="../assets/${topicImg}" class="postPicture" alt="Post Picture">
                </div>
                <div class="photoCardBody">
                    <p class="post-text">${desc.substring(0, 500)}...</p> 
                                <span class="read-more-btn">Read More</span>
                </div>
                <div class="cardFooter">
                    <img src="../assets/like.png" alt="like icon" class="likesIcon">
                    <p><span class="like-count">${numMembers}</span> Followers - ${numPosts} Posts</a></p>
                </div>
            `;
        
            createFollowBtn(postCard, numMembers, numPosts);
            createReadMoreBtn(postCard, desc);
        
            document.getElementById("content").appendChild(postCard);
        }

createPhotoCard(topicName, desc, topicId, numMembers, numPosts, topicImg);
createPhotoCard(topicName, desc, topicId, numMembers, numPosts, topicImg);
createPhotoCard(topicName, desc, topicId, numMembers, numPosts, topicImg);
createPhotoCard(topicName, desc, topicId, numMembers, numPosts, topicImg);


