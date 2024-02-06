(function ($) {
	$(document).ready(function(){

		setTimeout(function(){
			var windowHeight = $(window).height();
			var bodyHeight = $("body").height();
			var is_sticky = false;
			if (bodyHeight > windowHeight + 200) {
				is_sticky = true;
			}
			if (is_sticky) {
				$(window).scroll(function () {
					if ($(window).scrollTop() > 100) {
						$(".navbar").addClass("sticky_header");
					} else {
						$(".navbar").removeClass("sticky_header");
					}
				});
			}
		}, 1000);

		$(".testimonial_wrapper .view-content").addClass("owl-carousel owl-theme");
		$(".testimonial_wrapper .view-content").owlCarousel({
			items: 1,
			loop: true,
			autoplay: true,
			responsive:{
				0: {
					items:1
				},
				600: {
					items:2
				},
				1000: {
					items:3
				}
			}
		});
	});
})(jQuery);


// (function ($) {
// 	Drupal.behaviors.greetingPopup = {
// 	  attach: function (context, settings) {
// 		// Function to get the current time and display the appropriate greeting.
// 		function showGreetingPopup() {
// 		  var now = new Date();
// 		  var hour = now.getHours();
// 		  var greeting;
  
// 		  if (hour < 12) {
// 			greeting = 'Good morning';
// 		  } else if (hour < 18) {
// 			greeting = 'Good afternoon';
// 		  } else {
// 			greeting = 'Good evening';
// 		  }
  
// 		  // Create the HTML structure for the popup.
// 		  var popupHtml =
// 			'<div id="greeting-popup">' +
// 			'<div class="popup-content">' +
// 			'<p>' + greeting + '</p>' +
// 			'<button id="close-popup">Close</button>' +
// 			'</div>' +
// 			'</div>';
  
// 		  // Append the popup HTML to the body.
// 		  $('body', context).append(popupHtml);
  
// 		  // Center the popup on the screen.
// 		  var popup = $('#greeting-popup');
// 		  var popupContent = popup.find('.popup-content');
// 		  var top = Math.max(0, ($(window).height() - popupContent.outerHeight()) / 2);
// 		  var left = Math.max(0, ($(window).width() - popupContent.outerWidth()) / 2);
  
// 		  popup.css({
// 			'top': top + 'px',
// 			'left': left + 'px',
// 		  });
  
// 		  // Close button click handler.
// 		  $('#close-popup').on('click', function () {
// 			popup.remove();
// 		  });
// 		}
  
// 		// Show the greeting popup when the document is ready.
// 		$(document).ready(function () {
// 		  showGreetingPopup();
// 		});
// 	  },
// 	};
//   })(jQuery);

  
(function ($) {
	Drupal.behaviors.greetingPopup = {
	  attach: function (context, settings) {
		// Function to check if the popup has been shown before.
		function hasPopupBeenShown() {
		  return document.cookie.indexOf('greetingPopupShown=true') !== -1;
		}
  
		// Function to set a cookie indicating that the popup has been shown.
		function setPopupShownCookie() {
		  var expirationDate = new Date();
		  expirationDate.setFullYear(expirationDate.getFullYear() + 1);
		  document.cookie = 'greetingPopupShown=true; expires=' + expirationDate.toUTCString() + '; path=/';
		}
  
		// Function to get the current time and display the appropriate greeting.
		function showGreetingPopup() {
		  // Check if the popup has been shown before.
		  if (!hasPopupBeenShown()) {
			var now = new Date();
			var hour = now.getHours();
			var greeting;
  
			if (hour < 12) {
			  greeting = 'Good morning';
			} else if (hour < 18) {
			  greeting = 'Good afternoon';
			} else {
			  greeting = 'Good evening';
			}
  
			// Check if the user is on the homepage.
			if (window.location.pathname === '/' || window.location.pathname === '/trikon/') {
			  // Create the HTML structure for the popup.
			  var popupHtml =
				'<div id="greeting-popup">' +
				'<div class="popup-content">' +
				'<p>' + greeting + '</p>' +
				'<button id="close-popup">Close</button>' +
				'</div>' +
				'</div>';
  
			  // Append the popup HTML to the body.
			  $('body', context).append(popupHtml);
  
			  // Center the popup on the screen.
			  var popup = $('#greeting-popup');
			  var popupContent = popup.find('.popup-content');
			  var top = Math.max(0, ($(window).height() - popupContent.outerHeight()) / 2);
			  var left = Math.max(0, ($(window).width() - popupContent.outerWidth()) / 2);
  
			  popup.css({
				'top': top + 'px',
				'left': left + 'px',
			  });
  
			  // Close button click handler.
			  $('#close-popup').on('click', function () {
				popup.remove();
				// Set a cookie to indicate that the popup has been shown.
				setPopupShownCookie();
			  });
			}
		  }
		}
  
		// Show the greeting popup when the document is ready.
		$(document).ready(function () {
		  showGreetingPopup();
		});
	  },
	};
  })(jQuery);
function openWhatsApp() {
	var adminNumber = '+880 1633-530231'; // Replace with the actual admin's number
	var message = 'Hello, I have a question.'; // You can customize the initial message

	// Construct the WhatsApp URL
	var whatsappURL = 'https://api.whatsapp.com/send?phone=' + encodeURIComponent(adminNumber) + '&text=' + encodeURIComponent(message);

	// Open the WhatsApp URL in a new tab/window
	window.open(whatsappURL, '_blank');
}