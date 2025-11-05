// Main feed functionality
import "./component/postCard.js";

/**
 * Plan is to load maximum 10 posts at a time. and count how many 
 * times we have loaded for that we use POST FETCH OFFSET ROUND variable
 * 
 */

let POST_FETCH_OFFSET_ROUND = 1;
document.addEventListener("DOMContentLoaded", async function () {
  document
    .getElementById("loadMoreBtn")
    .addEventListener("click", loadMorePosts);
  try {
    await showPostSkeletons(3);
    const posts = await fetchFeed("for_you");
    renderFeed(posts);
  } catch (error) {
    console.error("Error fetching initial feed:", error);
    noPostsMessage();
  } finally {
    hidePostSkeletons();
  }

  const tabs = document.querySelectorAll(".tab");
  tabs.forEach((tab) => {
    tab.addEventListener("click", async () => {
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      const value = tab.getAttribute("value");
      if (value === "for_you" || value === "following") {
        try {
          await showPostSkeletons(2);
          const posts = await fetchFeed(value);
          renderFeed(posts);
        } catch (error) {
          console.error("Error fetching feed:", error);
          noPostsMessage();
        } finally {
          hidePostSkeletons();
        }
      }
    });
  });
});

function renderFeed(posts) {
  const feed = document.getElementById("feed");
  //   feed.innerHTML = "";
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
  el.setAttribute("tag", post.user_handle || `@user${post.user_id ?? ""}`);

  el.setAttribute("post-time", post.post_time || (post.created_at ?? "")); // e.g. "2h" or timestamp
  el.setAttribute("post-content", post.content || "");
  if (post.image) el.setAttribute("post-img", post.image); // filename

  el.setAttribute("like-count", String(post.likes ?? post.like_count ?? 0));
  el.setAttribute("liked", post.liked ? "1" : "0");
  el.setAttribute(
    "cmnt-count",
    String(post.comments ?? post.comment_count ?? 0)
  );
  el.setAttribute("post-id", String(post.id));
  el.setAttribute("post-user-id", String(post.user_id));

  // Current user context (for menu/permissions)
  el.setAttribute("current-user-id", String(ctx.currentUserId));
  el.setAttribute("current-user-role", String(ctx.currentUserRole));

  return el;
}

async function fetchFeed(feedType) {
  // Build URL with URLROOT when available
  const response = await fetch(
    `mainfeed?feed_type=${encodeURIComponent(feedType)}&offsetRound=${encodeURIComponent(POST_FETCH_OFFSET_ROUND)}`,
    { headers: { "X-Requested-With": "XMLHttpRequest" } }
  );
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
  feed.innerHTML =
    '<p class="no-posts-message">No posts available in this feed.</p>';
}

async function showPostSkeletons(count = 2) {
  const feed = document.getElementById("feed");
  if (!feed) return;
  feed.innerHTML = "";
  POST_FETCH_OFFSET_ROUND = 1;
  resetLoadMoreButton();
  // Avoid duplicating skeletons
  if (document.getElementById("feed-skeletons")) return;
  const wrap = document.createElement("div");
  wrap.id = "feed-skeletons";
  feed.prepend(wrap);

  const skUrl = `GetSkeleton?require=postSkeleton`;
  {
    const res = await fetch(skUrl, {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (!res.ok) throw new Error("skeleton_fetch_failed");
    const html = await res.text();
    wrap.innerHTML = Array.from({ length: count })
      .map(() => html)
      .join("");
  }
}

function hidePostSkeletons() {
  const sk = document.getElementById("feed-skeletons");
  if (sk) sk.remove();
}

async function loadMorePosts() {
  try {
    POST_FETCH_OFFSET_ROUND += 1;
    let activeTab = document.querySelector(".tab.active");
    let feedType = activeTab.getAttribute("value");
    const posts = await fetchFeed(feedType);
    if (!posts || posts.length === 0) {
      let loadMoreBtn = document.getElementById("loadMoreBtn");
      loadMoreBtn.textContent = "No more posts to load";
      loadMoreBtn.disabled = true;
    } else {
      renderFeed(posts);
    }
  } catch (error) {
    let loadMoreBtn = document.getElementById("loadMoreBtn");
    if (String(error && error.message).includes("No posts available")) {
      loadMoreBtn.textContent = "No more posts to load";
      loadMoreBtn.disabled = true;
    } else {
      loadMoreBtn.textContent = "Error loading posts";
      loadMoreBtn.classList.add("error-btn");
    }
  }
}

function resetLoadMoreButton() {
  let loadMoreBtn = document.getElementById("loadMoreBtn");
  loadMoreBtn.textContent = "Load More Posts";
  loadMoreBtn.disabled = false;
  loadMoreBtn.classList.remove("error-btn");
}
