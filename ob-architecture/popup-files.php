
from popup maker we have added 

SS :- https://i.imgur.com/X8N6nDN.png, https://i.imgur.com/rwYufSg.png



<script type="text/javascript">
jQuery(document).ready(function ($) {
  var popupId = 57010;
  //var scrollThreshold = 1000;
  var scrollThreshold = jQuery(""body"").hasClass(""home"") ? 1000 : 500;
  var cookieName = ""pum-57010"";
  var sessionKey = ""pum-57010-session"";

  // Ensure Popup Maker library is loaded
  if (typeof PUM === ""undefined"") {
    console.error(""Popup Maker (PUM) library not found."");
    return;
  }

  // Utility to mark the session across tabs
  function markSession() {
    sessionStorage.setItem(sessionKey, ""true"");
    //localStorage.setItem(sessionKey, Date.now().toString()); // Use timestamp to track
  }

  // Utility to check if the popup session is marked
  function isSessionMarked() {
    return sessionStorage.getItem(sessionKey);
  }

  // Check if the cookie exists (popup already closed or submitted) or session is marked
  if (PUM.getCookie(cookieName) || isSessionMarked()) {
    console.log(
      ""Popup already closed, submitted, or session marked. Will not trigger again.""
    );
    return;
  }

  // Trigger popup on scroll
  var isPopupTriggered = false;
  $(window).on(""scroll"", function () {
    if (!isPopupTriggered && $(window).scrollTop() > scrollThreshold) {
      isPopupTriggered = true;
      console.log(""Triggering popup ID: "" + popupId);
      PUM.open(popupId);
      markSession(); // Mark the popup as shown across tabs and sessions
    }
  });

  // Listen for localStorage changes (for cross-tab communication)
  /*window.addEventListener('storage', function (e) {
        if (e.key === sessionKey && e.newValue) {
            console.log(""Popup session marked in another tab. Will not trigger here."");
            sessionStorage.setItem(sessionKey, 'true'); // Sync sessionStorage with localStorage
        }
    });*/

  // Debugging: Optional console logs
  var debugMode = false;
  if (!debugMode) {
    console.log = function () {};
  }
});

</script>