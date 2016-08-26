function add_link(url, rel) {
  var link = document.getElementById(rel) || document.createElement('link');
  link.id = rel;
  link.rel = rel;
  link.href = url;
  document.body.appendChild(link);
}

function prerender_url() {
  var anchor = document.getElementById('anchor');
  anchor.innerText = '';
  anchor.href = '';
  var progress = document.getElementById('progress');
  progress.innerText = '';
  var input_url = document.getElementById('url');
  var url = input_url.value;
  if (!url)
    return false;
  if (!url.indexOf('http') == 0)
    url = 'http://' + url;
//  if (document.location.hash != '#' + url)
//    document.location.hash = '#' + url;
  // Set the input url to the url we're actually prerendering.
  input_url.value = url;
  add_link(url, 'prerender');

  window.setTimeout(function() {
    console.log('loaded');
    set_progress("Click to navigate to prerendered page: ");
    anchor.href = url;
    anchor.innerText = url;
  }, 0);
  console.log('prerendering: ' + url);
  return false;
}

/*
function check_hash() {
  var hash = document.location.hash;
  if (!hash)
    return;
  hash = hash.substring(1);
  var url = document.getElementById('url');
  if (url.value != hash) {
    url.value = hash;
    prerender_url();
  }
}
*/
function set_prerender_state() {
  var state = document.visibilityState || document.webkitVisibilityState;
  if (state != 'visible') {
    // State is something other than visible -- prerendering is enabled.
    // Note: we should really be checking for 'prerender' state here, but
    // doesn't seem to be set ever.
    var id = document.location.search.replace('?prerender-id=', '');
    window.localStorage.setItem('prerender-enabled-' + id, 'true');
  }
}

function set_progress(progress_text) {
  var progress = document.getElementById('progress');
  progress.innerText = progress_text;
}

var visibility_events = ['webkitvisibilitystatechange',
                         'webkitvisibilitychange',
                         'visibilitychange'];

function check_prerender_enabled() {
  set_progress('Checking to see if prerender is enabled...');
  var url = document.location.search;
  if (url.indexOf('?prerender-id=') != -1) {
    // Prerendered page. Monitor visibility and mark if prerendering is
    // enabled in localStorage. We listen for all past and future events.
    for ( var i = 0; i < visibility_events.length; ++i)
      document.addEventListener(visibility_events[i], set_prerender_state, false);

    set_prerender_state();
  } else {
    // Prerender self and wait for the localStorage marker.
    // Pick a unique url for cache busting
    var id = Date.now() + '-' + Math.random()
    var href = document.URL + '?prerender-id=' + id;

    // If we time out, mark prerendering as disabled.
    var disabledTimeout = window.setTimeout(function() {
      set_prerender_enabled(false);
    }, 5000);

    window.addEventListener('storage', function(event) {
      if (event.key == 'prerender-enabled-' + id) {
        // This browser is wicked.
        set_prerender_enabled(true);
        // Clean up marker.
        window.localStorage.removeItem(event.key);
        window.clearTimeout(disabledTimeout);
      }
    }, false);

    add_link(href, 'prerender');
  }
}

function set_prerender_enabled(enabled) {
  if (enabled) {
    console.log("prerender enabled");
    set_progress("Prerender is ENABLED");
    var input_url = document.getElementById('url');
    input_url.disabled = '';
    var input_submit = document.getElementById('url_submit');
    input_submit.disabled = '';
//    check_hash();
  } else {
    console.log("prerender disabled");
    set_progress("Prerender is DISABLED");
  }

  for ( var i = 0; i < visibility_events.length; ++i)
    document.removeEventListener(visibility_events[i], set_prerender_state, false);
}

//window.addEventListener('hashchange', check_hash, false);
