# Bloggit 

## Introduction
COSC360 Introduction to Web Development Final Project <br/>
Team: Eddy Tian, Nikola Rowe, Timothy Thio <br/>
Professor: Dr. Ifeoma Adaji

Welcome to Bloggit, a blogging platform where users interact with communities through topics, posts, likes, and more. This walkthrough demonstrates the website's core essential user functionality. For the best experience, please log in with an account to get the full flow of post creation, post interaction, and user interaction.

## Website Link:
https://cosc360.ok.ubc.ca/timnthio/cosc360-proj

## Summary & Walkthrough Documents: 


## Summary of Testing: 
- Tested login/register input validation to ensure feedback is present and user knows what fields were invalid
![image](https://github.com/user-attachments/assets/be630001-7db7-4e59-bfbb-04fb18908d02)
![image](https://github.com/user-attachments/assets/2f1a0b2b-0083-46ff-9f52-bed46b3c3a57)

- Tested the search page by searching for invalid items, valid items, and empty queries to see if they return the correct posts and if the hot topics & posts are hidden when there are results

- Tested the edit profile page's input validation to ensure no invalid fields can be sent to the database
![image](https://github.com/user-attachments/assets/569d31b2-4a6b-444b-a878-04fb052e84e4)

- Tested the navbar by logging in as different user groups to see whether the nav links properly reflect the user's role (admin should have admin button, logged-in user should see profile and notifications)
  ![image](https://github.com/user-attachments/assets/a25408ad-4728-47c2-8166-28ff5b15c3f7)
  
- Tested asynchronous updates (likes, comments, saves, notifications) to ensure that they are updated seamlessly between users without having to refresh the page

