// Bootstrap tooltip
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});

// WOW animation
new WOW().init();

// Header scroll fixed
window.addEventListener('scroll', function () {
  let header = document.getElementById('header');
  if (window.pageYOffset > 200) {
    header.classList.add('active');
  } else {
    header.classList.remove('active');
  }
});

// Header menu link button active state
document.addEventListener("DOMContentLoaded", function () {
  let linkbtn = document.getElementsByClassName('link-btn');
  for (let i = 0; i < linkbtn.length; i++) {
    linkbtn[i].addEventListener("click", function () {
      for (let j = 0; j < linkbtn.length; j++) {
        linkbtn[j].classList.remove('active');
      }
      this.classList.add('active');
    });
  }

  // Ripples effect
  if ($('.testimonial-section').length) {
    $('.testimonial-section').ripples({
      resolution: 256,
      perturbance: 0.01,
    });
  }

  // Typed animation
  if ($(".typed").length) {
    $(".typed").typed({
      strings: ["Website Development", "Custom Software Development", "Mobile App Development", "SEO", "Graphics Design Supports"],
      typeSpeed: 30,
      startDelay: 500,
      backSpeed: 40,
      backDelay: 500,
      loop: true,
      loopCount: 5,
      showCursor: false,
      cursorChar: "|",
      contentType: 'html'
    });
  }

  // Slick carousel for testimonials
  if ($('.testimonial').length) {
    $('.testimonial').slick({
      autoplay: true,
      autoplaySpeed: 3000,
      centerMode: true,
      centerPadding: '0px',
      slidesToShow: 2,
      slidesToScroll: 1,
      draggable: true,
      swipeToSlide: true,
      arrows: false,
      dots: true,
      responsive: [
        {
          breakpoint: 1024,
          settings: { slidesToShow: 2, slidesToScroll: 1, arrows: true }
        },
        {
          breakpoint: 768,
          settings: { slidesToShow: 1, arrows: false, dots: true }
        },
        {
          breakpoint: 600,
          settings: { slidesToShow: 1, arrows: false, dots: true }
        },
        {
          breakpoint: 480,
          settings: { slidesToShow: 1, arrows: false, dots: true }
        }
      ]
    });
  }
});