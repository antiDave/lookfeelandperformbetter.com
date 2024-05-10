var swiper = new Swiper(".custom-swiper", {
  // Optional parameters
  direction: "horizontal",
  loop: true,
  autoplay: {
    delay: 5000,
  },
  // If we need pagination
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});

var swiper = new Swiper(".videoStoriesSlider", {
  slidesPerView: 4,
  spaceBetween: 0,
  autoplay: false,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
    clickable: true,
    observer: true,
    observeParents: true,
    parallax: true,
  },
  breakpoints: {
    1199: {
      slidesPerView: 4,
      spaceBetween: 0,
    },
    1023: {
      slidesPerView: 3,
      spaceBetween: 0,
    },
    480: {
      slidesPerView: 2,
      spaceBetween: 0,
    },
  },
});
var swiper = new Swiper(".myImagesSwiper", {
  direction: "verticle",
  loop: true,
  effect: "fade",
  clickable: true,
});

// Get the button:
let scrollToTopButton = document.getElementById("scrollToTopBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    scrollToTopButton.style.display = "block";
  } else {
    scrollToTopButton.style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function scrollToTop() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

