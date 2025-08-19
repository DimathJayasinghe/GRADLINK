function handleClose() {
  window.location.href = window.location.origin + '/gradlink/';
}

let currentSlide = 1;
let totalSlides = 0;
let isAnimating = false;

document.addEventListener('DOMContentLoaded', () => {
  const slides = document.querySelectorAll('.auth-card .slide');
  totalSlides = slides.length;

  if (totalSlides === 0) return; // pages without slides (e.g., alumni)

  hideAllSlides();
  showSlide(1, 'none');
  updateNavigation();
});

function hideAllSlides() {
  document.querySelectorAll('.auth-card .slide').forEach(s => {
    s.style.display = 'none';
    s.classList.remove('active', 'slide-in-right', 'slide-in-left');
  });
}

function showSlide(n, direction = 'right') {
  if (isAnimating) return;
  isAnimating = true;

  hideAllSlides();

  const sel = `.slide_undergrad_${n}, .slide_alumni_${n}, .slide_${n}`;
  const target = document.querySelector(sel);
  if (target) {
    target.style.display = 'block';
    if (direction !== 'none') {
      target.classList.add(direction === 'right' ? 'slide-in-right' : 'slide-in-left');
    }
    setTimeout(() => {
      target.classList.add('active');
      isAnimating = false;
    }, 30);
  } else {
    isAnimating = false;
  }
  updateSlideCounter(n);
}

function updateSlideCounter(n) {
  const el = document.querySelector('.cardNo');
  if (el) el.textContent = n;
}

function updateNavigation() {
  const prevBtn = document.querySelector('[data-btn="previous"]');
  const nextBtn = document.querySelector('[data-btn="next"]');
  const submitBtn = document.querySelector('[data-btn="submit"]');

  if (prevBtn) {
    if (currentSlide <= 1) prevBtn.classList.add('btn-hidden');
    else prevBtn.classList.remove('btn-hidden');
  }

  if (nextBtn && submitBtn) {
    if (currentSlide >= totalSlides) {
      nextBtn.classList.add('btn-hidden');
      submitBtn.classList.remove('btn-hidden'); // only on last slide
    } else {
      nextBtn.classList.remove('btn-hidden');
      submitBtn.classList.add('btn-hidden');
    }
  }
}

function handleNext() {
  if (isAnimating) return;

  const current = document.querySelector(
    `.slide_undergrad_${currentSlide}, .slide_alumni_${currentSlide}, .slide_${currentSlide}`
  );
  if (current) {
    const required = current.querySelectorAll('input[required], select[required], textarea[required]');
    for (const input of required) {
      if (!String(input.value || '').trim()) {
        input.classList.add('error');
        input.addEventListener('input', () => input.classList.remove('error'), { once: true });
        return;
      }
    }
  }

  if (currentSlide < totalSlides) {
    currentSlide++;
    showSlide(currentSlide, 'right');
    updateNavigation();
  } else {
    const form = document.querySelector('.form');
    if (form) form.submit();
  }
}

function handlePrevious() {
  if (isAnimating) return;
  if (currentSlide > 1) {
    currentSlide--;
    showSlide(currentSlide, 'left');
    updateNavigation();
  }
}