// Dynamically load the Jitsi API script when the page loads
const script = document.createElement('script');
script.src = 'https://8x8.vc/vpaas-magic-cookie-d6cd25815c5b4585afb818b28d86b07b/external_api.js';
script.async = true;

script.onload = function () {
  try {
    const api = new window.JitsiMeetExternalAPI("8x8.vc", {
      roomName: "vpaas-magic-cookie-d6cd25815c5b4585afb818b28d86b07b/SampleAppUniqueClassificationsFeedMostly",
      parentNode: document.getElementById('jaas-container'),
      userInfo: {
        displayName: 'User', // You can change the display name here
      },
      configOverwrite: {
        startWithAudioMuted: true,  // Mute audio by default
        startWithVideoMuted: true,  // Mute video by default
      },
    });

    // Toggle audio on button click
    document.getElementById('toggle-audio').addEventListener('click', function () {
      api.executeCommand('toggleAudio');
    });

    // Toggle video on button click
    document.getElementById('toggle-video').addEventListener('click', function () {
      api.executeCommand('toggleVideo');
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function () {
      if (api) {
        api.dispose(); // Dispose of the Jitsi API instance when leaving the page
      }
    });
  } catch (error) {
    console.error('Error initializing Jitsi: ', error);
    alert('Error initializing Jitsi, please try again later.');
  }
};

script.onerror = function () {
  console.error('Error loading Jitsi API script.');
  alert('Error loading Jitsi API, please check your internet connection or try again later.');
};

document.body.appendChild(script);
