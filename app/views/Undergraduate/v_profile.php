<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undergraduate Profile</title>
    <link rel="stylesheet" href="../public/css/style.css">

    <style>
        .main-container{
            display: flex;
            width: 100%;
            height: 100vh;
            background-color: #ff0000;
        }
        .left-panel{
            background-color: white;
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2em;
            padding: 10px;

        }

        .right-panel{
            background-color: white;
            flex: 1;
            width: 100%;
            padding: 10px;
            
        }

        .post-creation{
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100px;
            background-color: #cccccc ;
            padding: 10px;
            border-radius: 5px;
        }

        .post-image-caption{
            display: flex;
            width: 100%;
            height: 70%;
            background-color: #e6e6e6;
            padding: 10px;
             border-radius: 5px;
        }

        .post-image{
            display: flex;
            width: 13%;
            height: 100%;
            background-color: #595959;
            margin-right: 10px;
            border-radius: 5px;
        }
        .post-caption{
            display: flex;
            width: 87%;
            height: 100%;
            background-color: #d9d9d9;
            border-radius: 5px;
        }
        .post-creation-buttons{
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
        }
        .badge-title{
            font-weight: bold;
            
        }
        .badges{
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100px;
            background-color: #cccccc;
            padding: 10px;
            border-radius: 5px;
            gap: 1em;
        }
        .badge-list{
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            gap: 1em;
            
        }
        .badge{
            display: flex;
            width: 30px;
            height: 30px;
            background-color: #595959;
            border-radius: 5px;
            
        }
        .analytics{
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 400px;
            background-color: #cccccc;
            padding: 10px;
            border-radius: 5px;
        }
        .analytics-title{
            font-weight: bold;
            font-size: 1.2em;
        }
        hr {
            border: none;        
            height: 1px;         
            background-color: #606060; 
            width: 100%;
        }
        .analytics-content{
            display: flex;
            flex-direction: column;
            gap: 1em;
        }
        .analytics-profiles{
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px;
            
        }
        .analytics-post-impressions{
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px;
            
        }
        .analytics-connections{
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px;
           
        }
        .analytics-footer{
            display: flex;
            height: 40px;
            justify-content: center;
            align-items: flex-end;
            background-color: #cccccc;
            cursor: pointer;
        }
        /* Center panel */
        .center-panel {
            background-color: white;
            display: flex;
            flex-direction: column;
            width: 100%;
            padding:10px;
            flex: 2;
            gap: 1rem;

            & > .profile {
                width: 100%;
                height: 200px;
                position: relative;

                & > .profile-up-part{
                    width: 100%;
                    height: 40%;
                    border-radius: 5px 5px 0 0;
                    background-color: #e6e6e6;
                }

                & > .profile-down-part {
                    width: 100%;
                    height: 60%;
                    border-radius: 0 0 5px 5px;
                    background-color: #cccccc;

                    & > .profile-image {
                        display: flex;
                        position: absolute;
                        z-index: 10;
                        top: 40%;    
                        left: 50%; 
                        transform: translate(-50%, -50%);
                        width: 60px;
                        height: 60px;
                        background-color: gray;
                        border-radius: 5px;
                    }

                    & > .profile-name {
                        padding-top: 40px;
                        font-weight: bold;
                        font-size: 1.5em;
                        text-align: center;
                    }

                    & > .profile-bio {
                        font-size: 0.8em;
                        max-width: 200px;
                        margin: 0 auto 10px;
                        text-align: center;
                    }
                }
            }

            & > .post-or-info{
                display: flex;
                width: 100%;
                height: 44px;
                gap: 1em;
                padding: 0.5rem;
                background-color: #e6e6e6;
                border-radius: 0.5rem;
                
                & > div {
                    display:flex;
                    flex: 1;
                    justify-content: center;
                    align-items: center; 
                    text-weight: bold;
                }
            }
        }
        
        .posts{
            display: flex;
            width: 45%;
            height: 100%;
            background-color: #e6e6e6;
            cursor: pointer;
            font-weight: bold;
            justify-content: center;
            
        }
        .info{
            display: flex;
            width: 45%;
            height: 100%;
            border-radius: 5px;
            background-color: #cccccc;
            cursor: pointer;
            font-weight: bold;
            justify-content: center;

        }
        .certificates{
            display: flex;
            flex-direction: column;
            font-weight: bold;
            font-size: 1.2em;
            justify-content: left;
            width: 100%;
            height: 300px;
            border-radius: 5px 5px 0 0 ;
            background-color: #cccccc;
            padding: 30px;
            gap: 1em;
        }
        .certificate-1{
            display: flex;
            flex-direction: row;
            width: 500px;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px; 
        }
        .certificate-1-icon{
            display: flex;
            width: 40px;
            height: 40px;
            background-color: #595959;
            margin-right: 10px;
            border-radius: 5px; 
        }
        .certificate-2{
            display: flex;
            flex-direction: row;
            width: 500px;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px; 
        }

        .certificate-2-icon{
            display: flex;
            width: 40px;
            height: 40px;
            background-color: #595959;
            margin-right: 10px;
            border-radius: 5px; 
        }

        /* right side panel */
        .event-list-title{
            font-weight: bold;
            font-size: 1.2em;
        }
        .hr{
            border: none;
            height: 1px;
            background-color: #606060;
            width: 100%;
        }
        .events{
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 400px;
            background-color: #cccccc;
            padding: 10px;
            border-radius: 5px;  
        }
        .event-list{
            display: flex;
            flex-direction: column;
            gap: 1em;
            padding: 10px;
        }
        .event-1{
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px;
        }
        .event-1-image{
            display: flex;
            width: 13%;
            height: 100%;
            background-color: #595959;
            margin-right: 10px;
            border-radius: 5px;
        }
        .event-2{
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px;
        }
        .event-2-image{
            display: flex;
            width: 13%;
            height: 100%;
            background-color: #595959;
            margin-right: 10px;
            border-radius: 5px;
        }
        .event-3{
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px;
        }
        .event-3-image{
            display: flex;
            width: 13%;
            height: 100%;
            background-color: #595959;
            margin-right: 10px;
            border-radius: 5px;
        }
        .event-4{
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 60px;
            background-color: #e6e6e6;
            padding: 10px;
            border-radius: 5px;
        }
        .event-4-image{
            display: flex;
            width: 13%;
            height: 100%;
            background-color: #595959;
            margin-right: 10px;
            border-radius: 5px;
        }

    </style>
</head>
<body>
    <div class="main-container">
        <div class="left-panel">
            <div class="post-creation">
                <div class="post-image-caption">
                    <div class="post-image"></div>
                    <div class="post-caption">
                        <textarea name="caption" id="caption" rows="2" cols="50">Post something...</textarea>
                    </div>
                </div>
                <div class="post-creation-buttons">
                    <button type="submit">Attach</button>
                    <button type="button">Post</button>
                </div>
            </div>
            <div class="badges">
                <div class="badge-title">Badges</div>
                <div class="badge-list">
                    <div class="badge"></div>
                    <div class="badge"></div>
                    <div class="badge"></div>
                </div>
            </div>
            <div class="analytics">
            <div class="analytics-title">Analytics</div>
            <div class="analytics-privacy">Private to you</div>
            <hr>
            <div class="analytics-content">
                <div class="analytics-profiles">21 profile views</div>
                <div class="analytics-post-impressions">0 post impressions</div>
                <div class="analytics-connections">40 connections</div>
            </div>
            <div class="analytics-footer">Show all analytics -></div>
        </div>
        </div>
        
        <div class="center-panel">
            <div class="profile">
                <div class="profile-up-part"></div>
                <div class="profile-down-part">
                    <div class="profile-image">&nbsp;</div>
                    <div class="profile-name">Dimath Jayasinghe</div>
                    <div class="profile-bio">Contrary to popular belief, Lorem Ipsum is not simply random text</div>
                </div>
            </div>
            <div class="post-or-info">
                <div class="posts">Posts</div>
                <div class="info">Info</div>
            </div>
            <div class="certificates">Certicates
                <div class= "certificate-1">
                    <div class="certificate-1-icon"></div>
                </div>
                <div class="certificate-2">
                    <div class="certificate-2-icon"></div>
                </div>
                
            </div>
        </div>
        <div class="right-panel">
            <div class="events">
                <div class="event-list-title">Upcoming Events</div>
                <hr>
                <div class="event-list">
                    <div class="event-1">
                        <div class="event-1-image"></div>
                    </div>
                    <div class="event-2">
                        <div class="event-2-image"></div>
                    </div>
                    <div class="event-3">
                        <div class="event-3-image"></div>
                    </div>
                    <div class="event-4">
                        <div class="event-4-image"></div>
                    </div>
                </div>
            </dev>
            </div>
        </div>
    </div>
</body>
</html>