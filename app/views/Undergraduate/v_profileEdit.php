<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        .profile-edit{
            display: flex;
            flex-direction: column;
            border-radius: 8px;
            background-color: #0f1518;
            padding: 20px;
            width: 60%;   /* increased width */
            max-width: 600px;
            margin: auto; /* center on page */
        }
        .profile-picture{
            width: 100px;
            height: 100px;
            border-radius: 5px;
            background-color: grey;
            margin-bottom: 20px;
            align-self: center;
        }

        .profile-details-edit{
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        /* Name field */
        .name {
            width: 100%;
            background-color: #1f2429;
            border: 1px solid tan;
            border-radius: 8px;
            padding: 5px;
        }
        .name textarea {
            width: 100%;
            height: 50px;
            background-color: #1f2429;
            color: #cbb8a3;
            border: none;
            outline: none;
            resize: none;
            font-size: 16px;
            border-radius: 5px;
            padding: 5px;
        }

        /* Bio field */
        .bio{
            width: 100%;
            background-color: #1f2429;
            border: 1px solid tan;
            border-radius: 8px;
            padding: 5px;
        }
        .bio textarea {
            width: 100%;
            height: 70px;
            background-color: #1f2429;
            color: #cbb8a3;
            border: none;
            outline: none;
            resize: none;
            font-size: 16px;
            border-radius: 5px;
            padding: 5px;
        }

        /* Certificates & Projects */
        .certificates, .projects {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .certificates .current-certificates, .projects .current-projects {
            width: 100%;
            min-height: 80px;
            background-color: #1f2429;
            border: 1px solid tan;
            border-radius: 8px;
            padding: 5px;
            position: relative;
        }
        .current-certificate-remove, .current-project-remove {
            position: absolute;
            top: 5px;
            right: 5px;
        }

        /* Buttons */
        .certificates-add-button button, .projects-add-button button {
            width: 30%;
            padding: 10px;
            background-color: #6f7b85;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            align-self: center;
        }
        .certificates-add-button button:hover, .projects-add-button button:hover {
            background-color: #b8e3e9;
            color: #000;
        }

        /* Save Button (centered) */
        .save-button {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .save-button button {
            width: 50%;
            padding: 10px;
            background-color: #6f7b85;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        .save-button button:hover {
            background-color: #b8e3e9;
            color: #000;
        }
    </style>
</head>
<body>
    
</body>
</html>
