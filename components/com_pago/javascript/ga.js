/*jQuery( document ).ready(function() {
    
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    
    ga('create', PAGO_APP_UAID, 'none', {'name': 'cp_tracker'});
    
    ga('cp_tracker.send', 'screenview', {
      'appName': 'paGO Commerce',
      'appId': PAGO_APP_ID,
      'appVersion': PAGO_APP_VERSION,
      'appInstallerId': PAGO_APP_INSTALL_ID
    });
    
    ga('cp_tracker.set', 'dimension1', PAGO_APP_ID); // appId
    ga('cp_tracker.set', 'dimension2', PAGO_APP_INSTALL_ID); // appInstallerId
    ga('cp_tracker.set', 'dimension3', 'paGO Commerce'); // appName
    ga('cp_tracker.set', 'dimension4', PAGO_APP_VERSION); // appVersion
    
    ga('cp_tracker.send', 'pageview'); 
});*/
    
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', PAGO_APP_UAID, 'none', {'name': 'cp_tracker'});

ga('cp_tracker.set', {
  'appName': 'paGO Commerce',
  'appId': PAGO_APP_ID,
  'appVersion': PAGO_APP_VERSION,
  'appInstallerId': PAGO_APP_INSTALL_ID
});

ga('cp_tracker.send', 'screenview', {'screenName': 'Home'});

ga('cp_tracker.set', 'dimension1', PAGO_APP_ID); // appId
ga('cp_tracker.set', 'dimension2', PAGO_APP_INSTALL_ID); // appInstallerId
ga('cp_tracker.set', 'dimension3', 'paGO Commerce'); // appName
ga('cp_tracker.set', 'dimension4', PAGO_APP_VERSION); // appVersion

ga('cp_tracker.send', 'pageview'); 
// ga('cp_tracker.send', 'event', 'video', 'started');
