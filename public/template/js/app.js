
// header menu fixed script 

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


var linkbtn = document.getElementsByClassName('link-btn');


for (let i = 0; i <= linkbtn.length; i++) {
  linkbtn[i].addEventListener("click", function () {
    for (var j = 0; j < linkbtn.length; j++) {
      linkbtn[j].classList.remove('active');
    }
    this.classList.add('active');
  });

}


function openBox(data) {
  if (data === 'e-commerce') {
    document.getElementById('ecommerce-box').classList.add('active');
    document.getElementById('mobile-app').classList.remove('active');
    document.getElementById('customSoftware').classList.remove('active');
    document.getElementById('graphics').classList.remove('active');
    document.getElementById('seo').classList.remove('active');
    document.getElementById('web').classList.remove('active');
  }
  else if (data === 'mobile-app') {
    document.getElementById('mobile-app').classList.add('active');
    document.getElementById('ecommerce-box').classList.remove('active');
    document.getElementById('customSoftware').classList.remove('active');
    document.getElementById('graphics').classList.remove('active');
    document.getElementById('seo').classList.remove('active');
    document.getElementById('web').classList.remove('active');
  }
  else if (data === 'customSoftware') {
    document.getElementById('customSoftware').classList.add('active');
    document.getElementById('mobile-app').classList.remove('active');
    document.getElementById('ecommerce-box').classList.remove('active');
    document.getElementById('graphics').classList.remove('active');
    document.getElementById('seo').classList.remove('active');
    document.getElementById('web').classList.remove('active');
  }
  else if (data === 'graphics') {
    document.getElementById('graphics').classList.add('active');
    document.getElementById('customSoftware').classList.remove('active');
    document.getElementById('mobile-app').classList.remove('active');
    document.getElementById('ecommerce-box').classList.remove('active');
    document.getElementById('seo').classList.remove('active');
    document.getElementById('web').classList.remove('active');
  }
  else if (data === 'seo') {
    document.getElementById('seo').classList.add('active');
    document.getElementById('graphics').classList.remove('active');
    document.getElementById('customSoftware').classList.remove('active');
    document.getElementById('mobile-app').classList.remove('active');
    document.getElementById('ecommerce-box').classList.remove('active');
    document.getElementById('web').classList.remove('active');
  }
  else if (data === 'web') {
    document.getElementById('web').classList.add('active');
    document.getElementById('seo').classList.remove('active');
    document.getElementById('graphics').classList.remove('active');
    document.getElementById('customSoftware').classList.remove('active');
    document.getElementById('mobile-app').classList.remove('active');
    document.getElementById('ecommerce-box').classList.remove('active');
  }

}

function closeModule() {
  document.getElementById('web').classList.remove('active');
  document.getElementById('seo').classList.remove('active');
  document.getElementById('graphics').classList.remove('active');
  document.getElementById('customSoftware').classList.remove('active');
  document.getElementById('mobile-app').classList.remove('active');
  document.getElementById('ecommerce-box').classList.remove('active');
}












