# gradlink
UCSC Alumni-Student Connect Platform – GRADLINK is a university project done by a team of second year computer science undergraduate as their year-based project.

# 🎓 GRADLINK – UCSC Alumni-Student Connect Platform

A role-based digital platform designed to strengthen connections between UCSC alumni and current students. GRADLINK enables verified alumni and students to collaborate, network, form groups, and engage in academic and industry-level projects.

---

## 📌 Project Overview

GRADLINK is a year-long academic group project by four UCSC undergraduates. The platform is being built from **scratch** using **vanilla HTML, CSS, JavaScript, PHP**, and **MySQL**, without using any frameworks or libraries.

It aims to:
- Foster alumni-student collaboration
- Provide mentoring and internship opportunities
- Enable group-based conversations and industry project work
- Allow admin analytics and oversight
- Facilitate secure role-based access

---

## 🚀 Features

### 👩‍🎓 Student Module
- Academic profile and achievements
- CV uploads and mentorship requests
- Join groups by interest or batch
- Participate in industry projects

### 🎓 Alumni Module
- Portfolio showcasing career path
- Post blogs, jobs, papers, or projects
- Form company/interest groups
- Offer mentorship or industry projects

### 🧑‍💼 Admin Panel
- Approve users, posts, and groups
- Access analytics and activity reports
- Role management and group moderation

### 🔁 Shared Features
- Direct and group messaging
- Role-based login and dashboards
- Smart group suggestions based on interests

---

## 🛠️ Tech Stack

| Layer      | Technology             |
|------------|------------------------|
| Frontend   | HTML5, CSS3, JavaScript (Vanilla) |
| Backend    | PHP (no frameworks)    |
| Database   | MySQL                  |
| Hosting    | XAMPP / WAMP (for local dev) |
| DevOps     | Git, GitHub            |

---

## 📁 Project Structure
(*tentative)

/gradlink
│
├── /auth # Login and registration
├── /students # Student dashboards and features
├── /alumni # Alumni dashboards and features
├── /admin # Admin panel and analytics
├── /groups # Group creation, membership, and chat
├── /projects # Project listing and team handling
├── /chat # Messaging system
├── /assets # CSS, JS, images
├── /includes # Reusable PHP (DB conn, header, footer)
├── /sql # ERD and database scripts
├── index.php # Landing page
└── README.md


---

## ⚙️ Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/your-org-or-username/gradlink.git
   cd gradlink

2. Set up your local environment:

  Install XAMPP or WAMP
  
  Move the project folder to htdocs/ (for XAMPP)
  
  Import sql/gradlink.sql into phpMyAdmin
  
  Configure database credentials in /includes/db.php.

3. Start your local server and open:

   http://localhost/gradlink/


## 👨‍👩‍👧‍👦 Team Members
    Dimath Jayasinghe
    Kaveen Amarasekara
    Harsath Mohomed
    Sunali Perera

## 📄 License
  This project is part of UCSC academic coursework and is not licensed for commercial use.
  
  (Currently Licensed under MIT LICENSE)

## 🤝 Contribution Guidelines
  This project is currently not open for public contributions.
  UCSC staff and supervisors may provide feedback via Issues or Discussions.

## 🧠 Acknowledgements
  Special thanks to: (need editing)

    Dr. Ajantha Athukorale (Supervisor)
    Mrs. Yohan Wanigasuriya (Co-supervisor)
    UCSC Administration
    UCSC Alumni Association
    UCSC Student Union
    All mentors and advisors
