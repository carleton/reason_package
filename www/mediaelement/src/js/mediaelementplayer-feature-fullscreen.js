/**
 * Fullscreen button
 *
 * This feature creates a button to toggle fullscreen on video; it considers a variety of possibilities when dealing with it
 * since it is not consistent across browsers. It also accounts for triggering the event through Flash shim.
 */
(function ($) {

	// Feature configuration
	$.extend(mejs.MepDefaults, {
		/**
		 * @type {Boolean}
		 */
		usePluginFullScreen: true,
		/**
		 * @type {String}
		 */
		fullscreenText: ''
	});

	$.extend(MediaElementPlayer.prototype, {

		/**
		 * @type {Boolean}
		 */
		isFullScreen: false,
		/**
		 * @type {Boolean}
		 */
		isNativeFullScreen: false,
		/**
		 * @type {Boolean}
		 */
		isInIframe: false,
		/**
		 * @type {Boolean}
		 */
		isPluginClickThroughCreated: false,
		/**
		 * Possible modes
		 * (1) 'native-native'  HTML5 video  + browser fullscreen (IE10+, etc.)
		 * (2) 'plugin-native'  plugin video + browser fullscreen (fails in some versions of Firefox)
		 * (3) 'fullwindow'     Full window (retains all UI)
		 * (4) 'plugin-click'   Flash 1 - click through with pointer events
		 * (5) 'plugin-hover'   Flash 2 - hover popup in flash (IE6-8)
		 *
		 * @type {String}
		 */
		fullscreenMode: '',
		/**
		 *
		 */
		containerSizeTimeout: null,

		/**
		 * Feature constructor.
		 *
		 * Always has to be prefixed with `build` and the name that will be used in MepDefaults.features list
		 * @param {MediaElementPlayer} player
		 * @param {$} controls
		 * @param {$} layers
		 * @param {HTMLElement} media
		 */
		buildfullscreen: function (player, controls, layers, media) {

			if (!player.isVideo) {
				return;
			}

			player.isInIframe = (window.location !== window.parent.location);

			// detect on start
			media.addEventListener('loadstart', function () {
				player.detectFullscreenMode();
			});

			// build button
			var t = this,
				hideTimeout = null,
				fullscreenTitle = t.options.fullscreenText ? t.options.fullscreenText : mejs.i18n.t('mejs.fullscreen'),
				fullscreenBtn =
					$('<div class="' + t.options.classPrefix + 'button ' +
					                   t.options.classPrefix + 'fullscreen-button">' +
						'<button type="button" aria-controls="' + t.id + '" title="' + fullscreenTitle +
							'" aria-label="' + fullscreenTitle + '"></button>' +
						'</div>')
					.appendTo(controls)
					.on('click', function () {

						// toggle fullscreen
						var isFullScreen = (mejs.MediaFeatures.hasTrueNativeFullScreen && mejs.MediaFeatures.isFullScreen()) || player.isFullScreen;

						if (isFullScreen) {
							player.exitFullScreen();
						} else {
							player.enterFullScreen();
						}
					})
					.on('mouseover', function () {

						// very old browsers with a plugin
						if (t.fullscreenMode === 'plugin-hover') {
							if (hideTimeout !== null) {
								clearTimeout(hideTimeout);
								hideTimeout = null;
							}

							var buttonPos = fullscreenBtn.offset(),
								containerPos = player.container.offset();

							media.positionFullscreenButton(buttonPos.left - containerPos.left, buttonPos.top - containerPos.top, true);
						}

					})
					.on('mouseout', function () {

						if (t.fullscreenMode === 'plugin-hover') {
							if (hideTimeout !== null) {
								clearTimeout(hideTimeout);
							}

							hideTimeout = setTimeout(function () {
								media.hideFullscreenButton();
							}, 1500);
						}

					});


			player.fullscreenBtn = fullscreenBtn;

			t.globalBind('keydown', function (e) {
				if (e.keyCode === 27 && ((mejs.MediaFeatures.hasTrueNativeFullScreen && mejs.MediaFeatures.isFullScreen()) || t.isFullScreen)) {
					player.exitFullScreen();
				}
			});

			t.normalHeight = 0;
			t.normalWidth = 0;

			// setup native fullscreen event
			if (mejs.MediaFeatures.hasTrueNativeFullScreen) {

				//
				/**
				 * Detect any changes on fullscreen
				 *
				 * Chrome doesn't always fire this in an `<iframe>`
				 * @private
				 */
				var fullscreenChanged = function () {
					if (player.isFullScreen) {
						if (mejs.MediaFeatures.isFullScreen()) {
							player.isNativeFullScreen = true;
							// reset the controls once we are fully in full screen
							player.setControlsSize();
						} else {
							player.isNativeFullScreen = false;
							// when a user presses ESC
							// make sure to put the player back into place
							player.exitFullScreen();
						}
					}
				};

				player.globalBind(mejs.MediaFeatures.fullScreenEventName, fullscreenChanged);
			}

		},

		/**
		 * Detect the type of fullscreen based on browser's capabilities
		 *
		 * @return {String}
		 */
		detectFullscreenMode: function () {

			var
				t = this,
				mode = '',
				features = mejs.MediaFeatures,
				isNative = t.media.rendererName !== null && t.media.rendererName.match(/(native|html5)/)
				;

			if (features.hasTrueNativeFullScreen && isNative) {
				mode = 'native-native';
			} else if (features.hasTrueNativeFullScreen && !isNative && !features.hasFirefoxPluginMovingProblem) {
				mode = 'plugin-native';
			} else if (t.usePluginFullScreen) {
				if (mejs.MediaFeatures.supportsPointerEvents) {
					mode = 'plugin-click';
					// this needs some special setup
					t.createPluginClickThrough();
				} else {
					mode = 'plugin-hover';
				}

			} else {
				mode = 'fullwindow';
			}


			t.fullscreenMode = mode;
			return mode;
		},

		/**
		 *
		 */
		createPluginClickThrough: function () {

			var t = this;

			// don't build twice
			if (t.isPluginClickThroughCreated) {
				return;
			}

			// allows clicking through the fullscreen button and controls down directly to Flash

			/*
			 When a user puts his mouse over the fullscreen button, we disable the controls so that mouse events can go down to flash (pointer-events)
			 We then put a divs over the video and on either side of the fullscreen button
			 to capture mouse movement and restore the controls once the mouse moves outside of the fullscreen button
			 */

			var fullscreenIsDisabled = false,
				restoreControls = function () {
					if (fullscreenIsDisabled) {
						// hide the hovers
						for (var i in hoverDivs) {
							hoverDivs[i].hide();
						}

						// restore the control bar
						t.fullscreenBtn.css('pointer-events', '');
						t.controls.css('pointer-events', '');

						// prevent clicks from pausing video
						t.media.removeEventListener('click', t.clickToPlayPauseCallback);

						// store for later
						fullscreenIsDisabled = false;
					}
				},
				hoverDivs = {},
				hoverDivNames = ['top', 'left', 'right', 'bottom'],
				i, len,
				positionHoverDivs = function () {
					var fullScreenBtnOffsetLeft = fullscreenBtn.offset().left - t.container.offset().left,
						fullScreenBtnOffsetTop = fullscreenBtn.offset().top - t.container.offset().top,
						fullScreenBtnWidth = fullscreenBtn.outerWidth(true),
						fullScreenBtnHeight = fullscreenBtn.outerHeight(true),
						containerWidth = t.container.width(),
						containerHeight = t.container.height();

					for (i in hoverDivs) {
						hoverDivs[i].css({position: 'absolute', top: 0, left: 0}); //, backgroundColor: '#f00'});
					}

					// over video, but not controls
					hoverDivs.top
					.width(containerWidth)
					.height(fullScreenBtnOffsetTop);

					// over controls, but not the fullscreen button
					hoverDivs.left
					.width(fullScreenBtnOffsetLeft)
					.height(fullScreenBtnHeight)
					.css({top: fullScreenBtnOffsetTop});

					// after the fullscreen button
					hoverDivs.right
					.width(containerWidth - fullScreenBtnOffsetLeft - fullScreenBtnWidth)
					.height(fullScreenBtnHeight)
					.css({
						top: fullScreenBtnOffsetTop,
						left: fullScreenBtnOffsetLeft + fullScreenBtnWidth
					});

					// under the fullscreen button
					hoverDivs.bottom
					.width(containerWidth)
					.height(containerHeight - fullScreenBtnHeight - fullScreenBtnOffsetTop)
					.css({top: fullScreenBtnOffsetTop + fullScreenBtnHeight});
				};

			t.globalBind('resize', function () {
				positionHoverDivs();
			});

			for (i = 0, len = hoverDivNames.length; i < len; i++) {
				hoverDivs[hoverDivNames[i]] = $('<div class="' + t.options.classPrefix + 'fullscreen-hover" />')
					.appendTo(t.container).mouseover(restoreControls).hide();
			}

			// on hover, kill the fullscreen button's HTML handling, allowing clicks down to Flash
			fullscreenBtn.on('mouseover', function () {

				if (!t.isFullScreen) {

					var buttonPos = fullscreenBtn.offset(),
						containerPos = player.container.offset();

					// move the button in Flash into place
					media.positionFullscreenButton(buttonPos.left - containerPos.left, buttonPos.top - containerPos.top, false);

					// allows click through
					t.fullscreenBtn.css('pointer-events', 'none');
					t.controls.css('pointer-events', 'none');

					// restore click-to-play
					t.media.addEventListener('click', t.clickToPlayPauseCallback);

					// show the divs that will restore things
					for (i in hoverDivs) {
						hoverDivs[i].show();
					}

					positionHoverDivs();

					fullscreenIsDisabled = true;
				}

			});

			// restore controls anytime the user enters or leaves fullscreen
			media.addEventListener('fullscreenchange', function (e) {
				t.isFullScreen = !t.isFullScreen;
				// don't allow plugin click to pause video - messes with
				// plugin's controls
				if (t.isFullScreen) {
					t.media.removeEventListener('click', t.clickToPlayPauseCallback);
				} else {
					t.media.addEventListener('click', t.clickToPlayPauseCallback);
				}
				restoreControls();
			});


			// the mouseout event doesn't work on the fullscren button, because we already killed the pointer-events
			// so we use the document.mousemove event to restore controls when the mouse moves outside the fullscreen button

			t.globalBind('mousemove', function (e) {

				// if the mouse is anywhere but the fullsceen button, then restore it all
				if (fullscreenIsDisabled) {

					var fullscreenBtnPos = fullscreenBtn.offset();


					if (e.pageY < fullscreenBtnPos.top || e.pageY > fullscreenBtnPos.top + fullscreenBtn.outerHeight(true) ||
						e.pageX < fullscreenBtnPos.left || e.pageX > fullscreenBtnPos.left + fullscreenBtn.outerWidth(true)
					) {

						fullscreenBtn.css('pointer-events', '');
						t.controls.css('pointer-events', '');

						fullscreenIsDisabled = false;
					}
				}
			});


			t.isPluginClickThroughCreated = true;
		},
		/**
		 * Feature destructor.
		 *
		 * Always has to be prefixed with `clean` and the name that was used in MepDefaults.features list
		 * @param {MediaElementPlayer} player
		 */
		cleanfullscreen: function (player) {
			player.exitFullScreen();
		},

		/**
		 *
		 */
		enterFullScreen: function () {

			var
				t = this,
				isNative = t.media.rendererName !== null && t.media.rendererName.match(/(html5|native)/)
				;

			if (mejs.MediaFeatures.isiOS && mejs.MediaFeatures.hasiOSFullScreen && typeof t.media.webkitEnterFullscreen === 'function') {
				t.media.webkitEnterFullscreen();
				return;
			}

			// set it to not show scroll bars so 100% will work
			$(document.documentElement).addClass(t.options.classPrefix + 'fullscreen');

			// store sizing
			t.normalHeight = t.container.height();
			t.normalWidth = t.container.width();


			// attempt to do true fullscreen
			if (t.fullscreenMode === 'native-native' || t.fullscreenMode === 'plugin-native') {

				mejs.MediaFeatures.requestFullScreen(t.container[0]);
				//return;

				if (t.isInIframe) {
					// sometimes exiting from fullscreen doesn't work
					// notably in Chrome <iframe>. Fixed in version 17
					setTimeout(function checkFullscreen() {

						if (t.isNativeFullScreen) {
							var percentErrorMargin = 0.002, // 0.2%
								windowWidth = $(window).width(),
								screenWidth = screen.width,
								absDiff = Math.abs(screenWidth - windowWidth),
								marginError = screenWidth * percentErrorMargin;

							// check if the video is suddenly not really fullscreen
							if (absDiff > marginError) {
								// manually exit
								t.exitFullScreen();
							} else {
								// test again
								setTimeout(checkFullscreen, 500);
							}
						}

					}, 1000);
				}

			} else if (t.fullscreeMode === 'fullwindow') {
				// move into position

			}

			// make full size
			t.container
			.addClass(t.options.classPrefix + 'container-fullscreen')
			.width('100%')
			.height('100%');

			// Only needed for safari 5.1 native full screen, can cause display issues elsewhere
			// Actually, it seems to be needed for IE8, too
			//if (mejs.MediaFeatures.hasTrueNativeFullScreen) {
			t.containerSizeTimeout = setTimeout(function () {
				t.container.css({width: '100%', height: '100%'});
				t.setControlsSize();
			}, 500);
			//}

			if (isNative) {
				t.$media
				.width('100%')
				.height('100%');
			} else {
				t.container.find('iframe, embed, object, video')
				.width('100%')
				.height('100%');
			}

			if (t.options.setDimensions) {
				t.media.setSize(screen.width, screen.height);
			}

			t.layers.children('div')
			.width('100%')
			.height('100%');

			if (t.fullscreenBtn) {
				t.fullscreenBtn
				.removeClass(t.options.classPrefix + 'fullscreen')
				.addClass(t.options.classPrefix + 'unfullscreen');
			}

			t.setControlsSize();
			t.isFullScreen = true;

			var zoomFactor = Math.min(screen.width / t.width, screen.height / t.height);
			t.container.find('.' + t.options.classPrefix + 'captions-text')
				.css('font-size', zoomFactor * 100 + '%');
			t.container.find('.' + t.options.classPrefix + 'captions-text')
				.css('line-height', 'normal');
			t.container.find('.' + t.options.classPrefix + 'captions-position')
				.css('bottom', '45px');

			t.container.trigger('enteredfullscreen');
		},

		/**
		 *
		 */
		exitFullScreen: function () {

			var
				t = this,
				isNative = t.media.rendererName !== null && t.media.rendererName.match(/(native|html5)/)
				;

			// Prevent container from attempting to stretch a second time
			clearTimeout(t.containerSizeTimeout);

			// come out of native fullscreen
			if (mejs.MediaFeatures.hasTrueNativeFullScreen && (mejs.MediaFeatures.isFullScreen() || t.isFullScreen)) {
				mejs.MediaFeatures.cancelFullScreen();
			}

			// restore scroll bars to document
			$(document.documentElement).removeClass(t.options.classPrefix + 'fullscreen');

			t.container.removeClass(t.options.classPrefix + 'container-fullscreen');

			if (t.options.setDimensions) {
				t.container.width(t.normalWidth)
					.height(t.normalHeight);
				if (isNative) {
					t.$media
						.width(t.normalWidth)
						.height(t.normalHeight);
				} else {
					t.container.find('iframe, embed, object, video')
						.width(t.normalWidth)
						.height(t.normalHeight);
				}

				t.media.setSize(t.normalWidth, t.normalHeight);

				t.layers.children('div')
					.width(t.normalWidth)
					.height(t.normalHeight);
			}

			t.fullscreenBtn
			.removeClass(t.options.classPrefix + 'unfullscreen')
			.addClass(t.options.classPrefix + 'fullscreen');

			t.setControlsSize();
			t.isFullScreen = false;

			t.container.find('.' + t.options.classPrefix + 'captions-text')
				.css('font-size','');
			t.container.find('.' + t.options.classPrefix + 'captions-text')
				.css('line-height', '');
			t.container.find('.' + t.options.classPrefix + 'captions-position')
				.css('bottom', '');

			t.container.trigger('exitedfullscreen');
		}
	});

})(mejs.$);
