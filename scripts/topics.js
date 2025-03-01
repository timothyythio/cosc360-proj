var title;
var bodyText;
var numLikes;
var timePosted;
var authorName;
var post;
var poster;
var topic;

//second post example
title2 = "This is a Test Text-Only Post";
authorName2 = localStorage.getItem("loggedInUser");
post2 = "post2";
poster2 = "profile";
pictureFile2 = "missile.png";
bodyText2 = "lorem ipsum  blah blah The missile knows where it is at all times. It knows this because it knows where it isn't. By subtracting where it is from where it isn't, or where it isn't from where it is (whichever is greater), it obtains a difference, or deviation. The guidance subsystem uses deviations to generate corrective commands to drive the missile from a position where it is to a position where it isn't, and arriving at a position where it wasn't, it now is. Consequently, the position where it is, is now the position that it wasn't, and it follows that the position that it was, is now the position that it isn't. In the event that the position that it is in is not the position that it wasn't, the system has acquired a variation, the variation being the difference between where the missile is, and where it wasn't. If variation is considered to be a significant factor, it too may be corrected by the GEA. However, the missile must also know where it was. The missile guidance computer scenario works as follows. Because a variation has modified some of the information the missile has obtained, it is not sure just where it is. However, it is sure where it isn't, within reason, and it knows where it was. It now subtracts where it should be from where it wasn't, or vice-versa, and by differentiating this from the algebraic sum of where it shouldn't be, and where it was, it is able to obtain the deviation and its variation, which is called error.";

//third post example
title3 = "This is a Test Topic Post";
authorName3 = "Shirakami Fubuki";
post3 = "post3";
poster3 = "profile";
pictureFile3 = "foob.jpg";
bodyText3 = "Glasses are really versatile. First, you can have glasses-wearing girls take them off and suddenly become beautiful, or have girls wearing glasses flashing those cute grins, or have girls stealing the protagonist's glasses and putting them on like, \"Haha, got your glasses!\" That's just way too cute! Also, boys with glasses! I really like when their glasses have that suspicious looking gleam, and it's amazing how it can look really cool or just be a joke. I really like how it can fulfill all those abstract needs. Being able to switch up the styles and colors of glasses based on your mood is a lot of fun too! It's actually so much fun! You have those half rim glasses, or the thick frame glasses, everything! It's like you're enjoying all these kinds of glasses at a buffet. I really want Luna to try some on or Marine to try some on to replace her eyepatch. We really need glasses to become a thing in hololive and start selling them for HoloComi. Don't. You. Think. We. Really. Need. To. Officially. Give. Everyone. Glasses?";
topic = "Hololive"

numLikes = 99999;
timePosted = new Date().toLocaleString("en-US", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
});

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

function createLikeBtn(postCard, numLikes, timePosted, authorName, poster) {
    let isLiked = false;
    let likesIcon = postCard.querySelector(".likesIcon");
    let likeCountSpan = postCard.querySelector(".like-count");

    likesIcon.addEventListener("click", function () {
        if (!isLiked) {
            numLikes++;
            likesIcon.src = "../assets/liked.png"; // Change to the liked icon image
            isLiked = true;
        } else {
            numLikes--;
            likesIcon.src = "../assets/like.png"; // Change back to the original like icon
            isLiked = false;
        }
        likeCountSpan.innerText = numLikes; // Update only the like count
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

function createTextCard(title, bodyText, numLikes, timePosted, authorName, post, poster) {
    let postCard = document.createElement("div");
    postCard.className = "postCard";

    postCard.innerHTML = `
        <div class="cardTitle">
            <h1><a href="${post}.html">${title}</a></h1>
        </div>
        <div class="textCardBody">
            <p class="post-text">${bodyText.substring(0, 500)}...</p> 
            <span class="read-more-btn">Read More</span>
        </div>
        <div class="cardFooter">
            <img src="../assets/like.png" alt="like icon" class="likesIcon">
            <p><span class="like-count">${numLikes}</span> Likes - ${timePosted} posted by <a href="${poster}.html">${authorName}</a></p>
        </div>
    `;

    createReadMoreBtn(postCard, bodyText);
    createLikeBtn(postCard, numLikes, timePosted, authorName, poster);

    document.getElementById("content").appendChild(postCard);
}

function createPhotoCard(title, bodyText, numLikes, timePosted, authorName, post, poster, pictureFile) {
    let postCard = document.createElement("div");
    postCard.className = "postCard";

    postCard.innerHTML = `
        <div class="cardTitle">
            <h1><a href="${post}.html">${title}</a></h1>
        </div>
        <div class="postPictureContainer">
            <img src="../assets/${pictureFile}" class="postPicture" alt="Post Picture">
        </div>
        <div class="photoCardBody">
            <p class="post-text">${bodyText.substring(0, 500)}...</p> 
            <span class="read-more-btn">Read More</span>
        </div>
        <div class="cardFooter">
            <img src="../assets/like.png" alt="like icon" class="likesIcon">
            <p><span class="like-count">${numLikes}</span> Likes - ${timePosted} posted by <a href="${poster}.html">${authorName}</a></p>
        </div>
    `;

    createReadMoreBtn(postCard, bodyText);
    createLikeBtn(postCard, numLikes, timePosted, authorName, poster);

    document.getElementById("content").appendChild(postCard);
}

function createTopicCard(title, bodyText, numLikes, timePosted, authorName, post, poster, pictureFile, topic) {
    let postCard = document.createElement("div");
    postCard.className = "postCard";

    postCard.innerHTML = `
        <div class="cardTitle">
            <h1><a href="${post}.html">${title}</a></h1>
            <h2 class="topicName"><a href=${topic}.html>Topic: ${topic}</a></h2>
        </div>
        <div class="topicPostPictureContainer">
            <img src="../assets/${pictureFile}" class="postPicture" alt="Post Picture">
        </div>
        <div class="topicCardBody">
            <p class="post-text">${bodyText.substring(0, 500)}...</p> 
            <span class="read-more-btn">Read More</span>
        </div>
        <div class="cardFooter">
            <img src="../assets/like.png" alt="like icon" class="likesIcon">
            <p><span class="like-count">${numLikes}</span> Likes - ${timePosted} posted by <a href="${poster}.html">${authorName}</a></p>
        </div>
    `;

    createReadMoreBtn(postCard, bodyText);
    createLikeBtn(postCard, numLikes, timePosted, authorName, poster);

    document.getElementById("content").appendChild(postCard);
}

createTextCard(title2, bodyText2, numLikes, timePosted, authorName2, post2, poster2, pictureFile2);
createTopicCard(title3, bodyText3, numLikes, timePosted, authorName3, post2, poster3, pictureFile3, topic);


