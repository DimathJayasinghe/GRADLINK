// Main feed functionality
<script src="<?php echo URLROOT; ?>/js/component/postCard.js"></script>;

document.addEventListener("DOMContentLoaded", function () {
  let posts = fetchFeed("for_you");
//   renderFeed(posts);

  const tabs = document.querySelectorAll(".tab");
  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      const value = tab.getAttribute("value");
      if (value === "for_you" || value === "following") {
        try {
          posts = fetchFeed(value);
          renderFeed(posts);
        } catch (error) {
          noPostsMessage();
        }
      }
    });
  });
});

async function renderFeed(posts) {
  let feed = document.getElementById("feed");
  feed.innerHTML = "";
  posts.forEach(post, (post) => {
    let postCard = createPostCard(post);
    feed.appendChild(postCard);
  });
}

function createPostCard(post) {
    let postCard = document.createElement("div");
}

async function fetchFeed(feedType) {
  let response = await fetch(`/mainfeed?feed=${feedType}`);
  let data = await response.json();
  if (data.success) {
    if (data.posts.length === 0) {
      noPostsMessage();
      return [];
    }
    return data.posts;
  } else {
    throw new Error("Failed to fetch feed");
  }
}

function noPostsMessage() {
  let feed = document.getElementById("feed");
  feed.innerHTML =
    '<p class="no-posts-message">No posts available in this feed.</p>';
}
