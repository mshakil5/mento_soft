<div class="cookie-consent-banner" id="cookieConsent">
    <div class="cookie-content">
        <div class="cookie-header">
            <iconify-icon icon="mdi:cookie" width="32" height="32" style="color: #ff6d33"></iconify-icon>
            <h4>Cookies Policy</h4>
        </div>
        <p>Like most websites, this site uses cookies to assist with navigation and your ability to provide feedback, analyse your use of products and services so that we can improve them, assist with our personal promotional and marketing efforts and provide consent from third parties.</p>
        <div class="cookie-buttons">
            <button class="btn-cookie-accept" onclick="acceptCookies()">Accept</button>
        </div>
    </div>
</div>

<script>
  function setCookie(name, value, days) {
      let expires = "";
      if (days) {
          const date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }

  function getCookie(name) {
      const nameEQ = name + "=";
      const ca = document.cookie.split(';');
      for(let i = 0; i < ca.length; i++) {
          let c = ca[i].trim();
          if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
      }
      return null;
  }

  function acceptCookies() {
      setCookie('cookie_consent', 'accepted', 365);
      hideCookieBanner();
  }

  function showCookieBanner() {
      document.getElementById('cookieConsent').classList.add('active');
  }

  function hideCookieBanner() {
      document.getElementById('cookieConsent').classList.remove('active');
  }

  document.addEventListener('DOMContentLoaded', function() {
      if (!getCookie('cookie_consent')) {
          setTimeout(showCookieBanner, 1000);
      }
  });
</script>