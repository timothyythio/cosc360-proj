var title;
var bodyText;
var numLikes;
var timePosted;
var authorName;
var post;
var poster;

//first post example
title = "Matcha Cafe Rave!!";
authorName = "CoffeeLover99";
post = "post";
poster = "profile2";
pictureFile = "matcha.jpg";
bodyText = `GUUUUUUUYYYSSSSSSS

I went to get boba at the place I always go to and get my usual order - my ankles break
I literally ended up eating dirt and my drink splattered against everything in the shop.
My hair lowkey got soaked and I legit just had to pick myself up and sit in my drink.
I WAS SO TEMPTED TO LEAVE AND CRY but I felt so bad that every counter top, wall, and
their mother fell victim to my clumsiness. What a waste of $9
#CryingForEternity #SomeoneVenmoMe9Dollars #MyPoorBabyBoba

TT

I kept apologizing over and over, and I literally was about to get a mountain of paper towel
from their bathroom to clean, but THANKFULLY a very kind angel of a human being that worked
there told me it was okay and that they’ve had messier messes to clean (not sure how??) and
that they will properly mop it up and such. BLESS and shout out to that worker
(lets all collectively say “Thank you Jina~”).
BUT THATS NOT ALL
A VERY VERY HANDSOME MAN (Cha Eun-Woo level I’m talkin here guys) WHO WATCHED THE ENTIRE THING
OFFERED TO BUY ME A NEW ONE??!!? He insisted, and wished me a good day.
#LifeIsGood #ChaEunWooTalkedToMe #Fate #DoIPropose`;

//second post example
title2 = "This is a Test Text-Only Post";
authorName2 = localStorage.getItem("loggedInUser");
post2 = "post2";
poster2 = "profile";
pictureFile2 = "missile.png";
bodyText2 = "lorem ipsum  blah blah The missile knows where it is at all times. It knows this because it knows where it isn't. By subtracting where it is from where it isn't, or where it isn't from where it is (whichever is greater), it obtains a difference, or deviation. The guidance subsystem uses deviations to generate corrective commands to drive the missile from a position where it is to a position where it isn't, and arriving at a position where it wasn't, it now is. Consequently, the position where it is, is now the position that it wasn't, and it follows that the position that it was, is now the position that it isn't. In the event that the position that it is in is not the position that it wasn't, the system has acquired a variation, the variation being the difference between where the missile is, and where it wasn't. If variation is considered to be a significant factor, it too may be corrected by the GEA. However, the missile must also know where it was. The missile guidance computer scenario works as follows. Because a variation has modified some of the information the missile has obtained, it is not sure just where it is. However, it is sure where it isn't, within reason, and it knows where it was. It now subtracts where it should be from where it wasn't, or vice-versa, and by differentiating this from the algebraic sum of where it shouldn't be, and where it was, it is able to obtain the deviation and its variation, which is called error.";

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

createPhotoCard(title, bodyText, numLikes, timePosted, authorName, post, poster, pictureFile);
createTextCard(title2, bodyText2, numLikes, timePosted, authorName2, post2, poster2, pictureFile2);


