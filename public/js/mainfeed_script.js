// Main feed functionality
import "./component/postCard.js";

/**
 * Plan is to load maximum 10 posts at a time. and count how many
 * times we have loaded for that we use POST FETCH OFFSET ROUND variable
 *
 */

let POST_FETCH_OFFSET_ROUND = 1;
let lastCheckedTimestamp = null;

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
    startPollingNewPosts();
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
    currentUserId: (window.CURRENT_USER_ID ?? "").toString(),
    currentUserRole: (window.CURRENT_USER_ROLE ?? "").toString(),
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
    `mainfeed?feed_type=${encodeURIComponent(
      feedType
    )}&offsetRound=${encodeURIComponent(POST_FETCH_OFFSET_ROUND)}`,
    { headers: { "X-Requested-With": "XMLHttpRequest" } }
  );
  const data = await response.json();
  if (data.success) {
    if (!data.posts || data.posts.length === 0) {
      throw new Error("No posts available");
    }
    // Only update 'since' marker when loading the top of the feed (round 1)
    // to avoid regressing the timestamp during pagination (Load More)
    const latest = getLatestCreatedAt(data.posts);
    if (POST_FETCH_OFFSET_ROUND === 1 && latest) {
      // Guard against clock drift/regression: only move forward
      if (!lastCheckedTimestamp ||
          new Date(latest.replace(" ", "T")) > new Date(String(lastCheckedTimestamp).replace(" ", "T"))) {
        lastCheckedTimestamp = latest;
      }
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

  // Client-side skeletons (no network fetch)
  const html = skeletonCardHTML();
  wrap.innerHTML = Array.from({ length: count })
    .map(() => html)
    .join("");
}

function hidePostSkeletons() {
  const sk = document.getElementById("feed-skeletons");
  if (sk) sk.remove();
}

// Returns markup for a single skeleton post card
function skeletonCardHTML() {
  return `
  <div class="post skeleton" aria-hidden="true">
    <div class="post-header">
      <div class="post-user">
        <div class="profile-photo skeleton-circle skeleton-box"></div>
        <div class="post-user-info skeleton-info" style="width: 190px;">
          <div class="skeleton-line lg skeleton-box" style="width: 80%;"></div>
          <div class="skeleton-line sm skeleton-box" style="width: 60%;"></div>
          <div class="skeleton-line sm skeleton-box" style="width: 40%;"></div>
          <div class="post-content" style="margin-top:8px;">
            <br><br>
            <div class="post-content skeleton-line sm skeleton-box"><p class="post-text">                                  </p></div>
          </div>
        </div>
      </div>
      <div class="post-menu" role="button" aria-hidden="true">
        <div class="skeleton-line sm skeleton-box" style="width: 18px; height: 18px; border-radius: 4px;"></div>
      </div>
    </div>
    <div class="post-media">
      <div class="skeleton-rect skeleton-box"></div>
    </div>
    <div class="post-actions">
      <div class="liked" aria-hidden="true">
        <div class="skeleton-line sm skeleton-box" style="width: 50px;"></div>
      </div>
      <div class="comment-btn" aria-hidden="true">
        <div class="skeleton-line sm skeleton-box" style="width: 60px;"></div>
      </div>
    </div>
  </div>`;
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

/**
 *
 * Polling to find new posts every 2 minutes
 */
function startPollingNewPosts() {
  setInterval(async () => {
    try {
      if (!lastCheckedTimestamp) return; // don't poll until initial timestamp is set
      let activeTab = document.querySelector(".tab.active");
      let feedType = activeTab.getAttribute("value");
      const posts = await fetchNewPosts(feedType);
    } catch (error) {}
  }, 30000); // 30 seconds interval we check for new posts
}

async function fetchNewPosts(feedType) {
  try {
    const res = await fetch(
      `mainfeed/newPosts?feed_type=${encodeURIComponent(
        feedType
      )}&since=${encodeURIComponent(lastCheckedTimestamp)}`,
      { headers: { "X-Requested-With": "XMLHttpRequest" } }
    );
    const data = await res.json();
    const ok =
      data &&
      (data.success === true || data.succcess === true) &&
      Number(data.count) > 0;
    if (ok) {
      /**
       * Show new posts notification
       */
      const newPostsAvailableButton =
        document.querySelector(".newPostsAvailable");
      if (!newPostsAvailableButton) return;
      newPostsAvailableButton.classList.add("is-visible");
      const newPostCountSpan = document.getElementById("newPostCount");
      if (newPostCountSpan) newPostCountSpan.textContent = `(${data.count})`;
      newPostsAvailableButton.onclick = async () => {
        POST_FETCH_OFFSET_ROUND = 1;
        try {
          await showPostSkeletons(3);
          const posts = await fetchFeed(feedType);
          // Replace current feed with new posts
          const feed = document.getElementById("feed");
          if (feed) feed.innerHTML = "";
          renderFeed(posts);
          // After rendering, set since to newest post created_at to prevent re-popping
          lastCheckedTimestamp =
            getLatestCreatedAt(posts) || lastCheckedTimestamp;
        } catch (err) {
          console.error("Error loading new posts:", err);
        } finally {
          hidePostSkeletons();
          newPostsAvailableButton.classList.remove("is-visible");
        }
      };
    }
  } catch (err) {
    console.error("Error fetching new posts:", err);
  }
}

// Helper: get newest created_at value from posts array (assumes DESC order fallback)
function getLatestCreatedAt(posts) {
  if (!Array.isArray(posts) || posts.length === 0) return null;
  // Prefer first item (DESC by created_at), else reduce to max
  const first = posts[0];
  if (first && first.created_at) return first.created_at;
  // Fallback: compute max
  let max = null;
  for (const p of posts) {
    if (p && p.created_at) {
      if (
        !max ||
        new Date(p.created_at.replace(" ", "T")) >
          new Date(max.replace(" ", "T"))
      ) {
        max = p.created_at;
      }
    }
  }
  return max;
}
