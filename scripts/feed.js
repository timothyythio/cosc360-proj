var title;
var bodyText;
var numLikes;
var timePosted;
var authorName;

title = "This is a Test Post"
bodyText = "lorem ipsum blah blah blah The missile knows where it is at all times. It knows this because it knows where it isn't. By subtracting where it is from where it isn't, or where it isn't from where it is (whichever is greater), it obtains a difference, or deviation. The guidance subsystem uses deviations to generate corrective commands to drive the missile from a position where it is to a position where it isn't, and arriving at a position where it wasn't, it now is. Consequently, the position where it is, is now the position that it wasn't, and it follows that the position that it was, is now the position that it isn't. In the event that the position that it is in is not the position that it wasn't, the system has acquired a variation, the variation being the difference between where the missile is, and where it wasn't. If variation is considered to be a significant factor, it too may be corrected by the GEA. However, the missile must also know where it was. The missile guidance computer scenario works as follows. Because a variation has modified some of the information the missile has obtained, it is not sure just where it is. However, it is sure where it isn't, within reason, and it knows where it was. It now subtracts where it should be from where it wasn't, or vice-versa, and by differentiating this from the algebraic sum of where it shouldn't be, and where it was, it is able to obtain the deviation and its variation, which is called error."
numLikes = 99999;
timePosted = Date("2024-02-28T03:24:00");
authorName = "yo mama";

function createCard(title, bodyText, numLikes, timePosted, authorName) {
    document.getElementById('content').innerHTML = `
    <div class="postCard">
            <div class="cardTitle">
                <h1 id="cardTitleText"></h1>
            </div>
            <div class="cardBody">
                <p id="cardBodyText"></p>
            </div>
            <div class="cardFooter">
                <img src="${pathPrefix}assets/like.png" alt="like icon" class="likesIcon">
                <p id="cardFooterText"></p>
            </div>
    </div>
`;

    document.getElementById("cardTitleText").innerHTML = title;
    document.getElementById("cardBodyText").innerHTML = bodyText;
    document.getElementById("cardFooterText").innerHTML = numLikes + " Likes - " + timePosted + " posted by " + authorName;

    return;
}

createCard(title, bodyText, numLikes, timePosted, authorName);
createCard(title, bodyText, numLikes, timePosted, authorName);
createCard(title, bodyText, numLikes, timePosted, authorName);

