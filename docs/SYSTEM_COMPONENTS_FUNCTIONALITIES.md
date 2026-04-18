# GRADLINK System Components and Core Functionalities

## Overview
This document summarizes the major components of the GRADLINK platform and their core functionalities.

| Component | Core Functionalities |
|---|---|
| User and Access Management | Registration and login for alumni, undergraduates, and admin users; role-based access control; session handling and protected routing. |
| Signup and Verification | OTP and email verification; role-specific input validation; pending alumni approval and rejection workflow. |
| Profile Management | User profile viewing and editing; profile image handling; role and identity details management. |
| Mainfeed (Home) | For You and Following feed tabs; asynchronous feed loading and pagination; skeleton loading, empty states, and shared post popup. |
| Post Management | Create, edit, and delete posts; image attachments; likes and comments; ownership and permission checks. |
| Explore and Discovery | Search and discovery flows for users, events, posts, and fundraiser content. |
| Follow Network | Follow and follow-request operations; relationship-aware visibility and interaction rules. |
| Messaging and Chat | One-to-one chat; conversation list; send, edit, and delete messages; mark-as-read and unread count updates. |
| Notifications and Alerts | In-app notification badge and modal; periodic polling; mark single or all as read; action-based redirects. |
| Events Workflow | Event request creation by users; moderation and publication lifecycle by admin. |
| Calendar | Display and browsing of ongoing or upcoming events; navigation to event details. |
| Fundraisers (Client) | Campaign listing and detail view; fundraiser request create and edit flows; progress and target visibility; active campaign summaries. |
| Donations and Payments | Donation processing flow; payment gateway integration; transaction status and reference tracking. |
| Reports and Abuse Handling | Reporting for posts, comments, profiles, events, and fundraisers; category and detail capture for moderation. |
| Bookmarks and Saved Items | Save selected content for later access and quick retrieval. |
| Left Sidebar Navigation | Role-aware navigation links; quick access to core modules; notification badge entry. |
| Right Sidebar Widgets | API-driven upcoming events and active fundraiser cards; compact summaries with quick links. |
| Admin Dashboard and Analytics | Administrative overview panels, engagement metrics, and system-level monitoring. |
| Admin Moderation and Operations | User suspension, deletion, and restoration; moderation actions for reported and managed content. |
| Alumni Verification (Admin) | Pending alumni review; approve, reject, and bulk verification operations. |
| Support and Ticketing (Admin) | Ticket status management and admin reply workflows. |
| Settings and Account Lifecycle | User settings and preferences; account lifecycle actions including reactivation and controlled deletion. |
| Media and File Handling | Secure media upload and retrieval for profile and post assets; size and file-type constraints. |
| Security and Compliance | Input sanitization, backend validation, endpoint-level authorization, and data handling safeguards. |
