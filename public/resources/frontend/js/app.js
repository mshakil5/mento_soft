var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

function scroller() {

  let p = window.pageYOffset;

  if (p > 200) {
    let k = document.getElementById('header');
    k.classList.add('active')
  } else {
    let k = document.getElementById('header');
    k.classList.remove('active')
  }

}

$(".testimonial-section").ripples({
    resolution: 256,
    perturbance: 0.01,
});

$(function () {
  $(".typed").typed({
    strings: services,
    stringsElement: null,
    // typing speed
    typeSpeed: 30,
    // time before typing starts
    startDelay: 500,
    // backspacing speed
    backSpeed: 40,
    // time before backspacing
    backDelay: 500,
    // loop
    loop: true,
    // false = infinite
    loopCount: 5,
    // show cursor
    showCursor: false,
    // character for cursor
    cursorChar: "|",
    // attribute to type (null == text)
    attr: null,
    // either html or text
    contentType: 'html',
    // call when done callback function
    callback: function () { },
    // starting callback function before each string
    preStringTyped: function () { },
    //callback for every typed string
    onStringTyped: function () { },
    // callback for reset
    resetCallback: function () { }
  });
});

$(document).ready(function () {
    if ($('.testimonial').length > 0) {
        $('.testimonial').slick({
            autoplay: false,
            centerMode: true,
            centerPadding: '0px',
            slidesToShow: 2,
            slidesToScroll: 1,
            arrows: false,
            dots: true,
            adaptiveHeight: true,
            responsive: [
                { breakpoint: 1024, settings: { slidesToShow: 2, arrows: true } },
                { breakpoint: 768, settings: { slidesToShow: 1, centerMode: true, centerPadding: '0px', arrows: false, dots: true } },
                { breakpoint: 600, settings: { slidesToShow: 1, arrows: false, dots: true } },
                { breakpoint: 480, settings: { slidesToShow: 1, arrows: false, dots: true } }
            ]
        });
    }
});

new WOW().init();

var linkbtn = document.getElementsByClassName('link-btn');

for (let i = 0; i < linkbtn.length; i++) {
  linkbtn[i].addEventListener("click", function () {
    for (var j = 0; j < linkbtn.length; j++) {
      linkbtn[j].classList.remove('active');
    }
    this.classList.add('active');
  });
}

function openBox(idToShow) {
    const allBoxes = document.querySelectorAll('.visualBox');

    allBoxes.forEach(box => {
        const iframe = box.querySelector('iframe');
        const video = box.querySelector('video');

        // Reset previous videos
        if (iframe) iframe.src = '';
        if (video) {
            video.pause();
            video.innerHTML = '';
        }

        // Activate selected box
        if (box.id === idToShow + '-box') {
            box.classList.add('active');

            // Load iframe src
            if (iframe && !iframe.src) {
                iframe.src = iframe.dataset.src;
            }

            // Load video src
            if (video && !video.src) {
                const source = document.createElement('source');
                source.src = video.dataset.src;
                source.type = 'video/mp4';
                video.appendChild(source);
                video.load();
            }
        } else {
            box.classList.remove('active');
        }
    });
}

function closeModule() {
    const allBoxes = document.querySelectorAll('.visualBox');
    allBoxes.forEach(box => {
        box.classList.remove('active');

        const iframe = box.querySelector('iframe');
        const video = box.querySelector('video');

        if (iframe) iframe.src = '';
        if (video) {
            video.pause();
            video.innerHTML = '';
        }
    });
}

$(window).on('scroll', function() {
    if ($(this).scrollTop() > 100) {
        $('.material-whatsapp').css('display', 'flex');
    } else {
        $('.material-whatsapp').css('display', 'none');
    }
});

$('.navbar-collapse .nav-link').click(function() {
    $('.navbar-collapse').collapse('hide');
});