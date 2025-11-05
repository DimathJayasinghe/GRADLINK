// Main feed functionality
import "./component/postCard.js";

document.addEventListener("DOMContentLoaded", async function () {
  try {
    const posts = await fetchFeed("for_you");
    renderFeed(posts);
  } catch (error) {
    console.error("Error fetching initial feed:", error);
    noPostsMessage();
  }

  const tabs = document.querySelectorAll(".tab");
  tabs.forEach((tab) => {
    tab.addEventListener("click", async () => {
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      const value = tab.getAttribute("value");
      if (value === "for_you" || value === "following") {
        try {
          const posts = await fetchFeed(value);
          renderFeed(posts);
        } catch (error) {
          console.error("Error fetching feed:", error);
          noPostsMessage();
        }
      }
    });
  });
});

function renderFeed(posts) {
  const feed = document.getElementById("feed");
  feed.innerHTML = "";
  posts.forEach((post) => {
    const postCard = createPostCard(post);
    feed.appendChild(postCard);
  });
}

function createPostCard(post) {
  const el = document.createElement("post-card");
  const ctx = {
    currentUserId: "",
    currentUserRole: "",
  };
  // Map your backend fields to post-card attributes
  el.setAttribute("profile-img", post.profile_image || "default.jpg");
  el.setAttribute("user-name", post.name || "User");
  el.setAttribute("user-role", (post.role || "undergrad").toLowerCase());
  el.setAttribute("tag", post.user_handle || `@user${post.user_id ?? ''}`);

  el.setAttribute("post-time", post.post_time || (post.created_at ?? "")); // e.g. "2h" or timestamp
  el.setAttribute("post-content", post.content || "");
  if (post.image) el.setAttribute("post-img", post.image); // filename

  el.setAttribute("like-count", String(post.likes ?? post.like_count ?? 0));
  el.setAttribute("liked", post.liked ? "1" : "0");
  el.setAttribute("cmnt-count", String(post.comments ?? post.comment_count ?? 0));
  el.setAttribute("post-id", String(post.id));
  el.setAttribute("post-user-id", String(post.user_id));

  // Current user context (for menu/permissions)
  el.setAttribute("current-user-id", String(ctx.currentUserId));
  el.setAttribute("current-user-role", String(ctx.currentUserRole));

  return el;
}

async function fetchFeed(feedType) {
  // Use relative path so it works under /GRADLINK/public/ (no leading slash)
  const response = await fetch(`mainfeed?feed_type=${feedType}`);
  const data = await response.json();
  if (data.success) {
    if (!data.posts || data.posts.length === 0) {
      throw new Error("No posts available");
    }
    return data.posts;
  }
  throw new Error("Failed to fetch feed");
}

function noPostsMessage() {
  const feed = document.getElementById("feed");
  feed.innerHTML = '<p class="no-posts-message">No posts available in this feed.</p>';
}
