/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/stacked-cards.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/stacked-cards.js":
/*!**************************************!*\
  !*** ./src/js/view/stacked-cards.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _typeof(o) { \"@babel/helpers - typeof\"; return _typeof = \"function\" == typeof Symbol && \"symbol\" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && \"function\" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? \"symbol\" : typeof o; }, _typeof(o); }\nfunction ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }\nfunction _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }\nfunction _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\nfunction _toPropertyKey(t) { var i = _toPrimitive(t, \"string\"); return \"symbol\" == _typeof(i) ? i : i + \"\"; }\nfunction _toPrimitive(t, r) { if (\"object\" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || \"default\"); if (\"object\" != _typeof(i)) return i; throw new TypeError(\"@@toPrimitive must return a primitive value.\"); } return (\"string\" === r ? String : Number)(t); }\nvar StackedCardHandler = function StackedCardHandler($scope, $) {\n  window.onbeforeunload = function () {\n    window.scrollTo({\n      top: 0,\n      behavior: \"smooth\",\n      duration: 400\n    });\n  };\n  gsap.registerPlugin(ScrollTrigger);\n  var container = $scope.find(\".eael-stacked-cards__container\");\n  var cardStyle = container.attr(\"data-cadr_style\");\n  var scrollTriggerConfig = JSON.parse(container.attr(\"data-scrolltrigger\"));\n  var elements = {\n    verticalCards: gsap.utils.toArray($scope.find(\".eael-stacked-cards__item\")),\n    horizontalCards: gsap.utils.toArray($scope.find(\".eael-stacked-cards__item_hr\")),\n    container: container\n  };\n\n  //Setup hover effects for all cards\n  setupHoverEffects($scope, $);\n  if (\"vertical\" === cardStyle) {\n    initVerticalCards(elements, scrollTriggerConfig);\n  } else if (\"horizontal\" === cardStyle) {\n    inithorizontalCards(elements, scrollTriggerConfig);\n  }\n};\nvar setupHoverEffects = function setupHoverEffects($scope, $) {\n  $scope.find(\".eael-stacked-cards__link\").each(function (_, button) {\n    var $button = $(button);\n    var normalStyle = $button.attr(\"style\");\n    var hoverStyle = $button.attr(\"data-hover-style\");\n    $button.on(\"mouseenter\", function () {\n      return $button.attr(\"style\", hoverStyle);\n    }).on(\"mouseleave\", function () {\n      return $button.attr(\"style\", normalStyle);\n    });\n  });\n};\nvar initVerticalCards = function initVerticalCards(elements, scrollTriggerConfig) {\n  var animation = createVerticalAnimation(elements.verticalCards);\n  createVerticalScrollTrigger(elements.container, animation, scrollTriggerConfig, elements.verticalCards.length);\n};\nvar createVerticalAnimation = function createVerticalAnimation(cards) {\n  var animation = gsap.timeline();\n  if (!cards || !cards.length) return animation;\n\n  //Inital setup for each card\n  cards.forEach(function (card, index) {\n    var $card = jQuery(card),\n      startForm = $card.data(\"start_form\"),\n      bgColor = card.getAttribute(\"data-bgColor\"),\n      animatedCard = $card.data(\"stacked_card\");\n    var getOpacity = (animatedCard === null || animatedCard === void 0 ? void 0 : animatedCard.opacity) === 1.1 ? 0 : animatedCard === null || animatedCard === void 0 ? void 0 : animatedCard.opacity;\n    gsap.set(card, {\n      position: \"absolute\",\n      top: 0,\n      left: 0,\n      filter: \"blur(0px)\",\n      y: index === 0 ? 0 : startForm * (index + 1),\n      //Calculated scroll Y value\n      opacity: index === 0 ? 1 : getOpacity,\n      rotation: 0,\n      backgroundColor: bgColor\n    });\n  });\n\n  //Create animation sequence for each card\n  cards.forEach(function (card, index) {\n    var animatedCard = jQuery(card).data(\"stacked_card\") || {};\n    var dataTranslate = parseInt(card.getAttribute(\"data-yaxis\"));\n    var prevCard = cards[index - 1];\n    var isLastCard = index === cards.length - 1;\n    if (index > 0) {\n      window.addEventListener(\"resize\", function () {\n        ScrollTrigger.refresh();\n      });\n      animation.to(card, {\n        opacity: 1,\n        y: isLastCard ? (animatedCard.y || 0) + dataTranslate : animatedCard.y || 0,\n        duration: 1,\n        ease: \"Power2.out\"\n      }, index);\n      // animation.to(cards[index - 1], { ...animatedCard }, index);\n      if (prevCard) {\n        animation.to(prevCard, _objectSpread({}, animatedCard), index);\n      }\n    } else {\n      window.addEventListener(\"load\", function () {\n        window.addEventListener(\"resize\", function () {\n          ScrollTrigger.refresh();\n        });\n        animation.to(card, {\n          opacity: 1,\n          y: animatedCard.y || 0,\n          duration: 1,\n          ease: \"Power2.out\"\n        }, index);\n        // animation.to(cards[index - 1], { ...animatedCard }, index);\n        if (prevCard) {\n          animation.to(prevCard, _objectSpread({}, animatedCard), index);\n        }\n      });\n    }\n  });\n  return animation;\n};\nvar createVerticalScrollTrigger = function createVerticalScrollTrigger(container, animation, scrollConfig, cardCount) {\n  // Calculate the total height based on the number of cards and the viewport height\n  var totalHeight = cardCount * window.innerHeight;\n  var scrollEnd = scrollConfig.end === \"default\" ? totalHeight : scrollConfig.end;\n\n  // Create a ScrollTrigger instance with the parsed data and calculated values\n  ScrollTrigger.create({\n    trigger: container,\n    invalidateOnRefresh: true,\n    animation: animation,\n    start: \"top top+=\".concat(scrollConfig.start),\n    end: \"+=\".concat(scrollEnd),\n    // Dynamic end point\n    scrub: true,\n    pin: true,\n    markers: scrollConfig.marker\n  });\n};\nvar inithorizontalCards = function inithorizontalCards(elements, scrollTriggerConfig) {\n  var timeline = createHorizontalTimeline(elements.container, scrollTriggerConfig);\n  setupResponsiveHorizontalCards(elements.horizontalCards, timeline);\n};\nvar createHorizontalTimeline = function createHorizontalTimeline(container, scrollTriggerConfig) {\n  return gsap.timeline({\n    scrollTrigger: {\n      trigger: container,\n      pin: true,\n      scrub: 0.5,\n      start: \"top \".concat(scrollTriggerConfig.start),\n      end: \"bottom \".concat(scrollTriggerConfig.end),\n      markers: scrollTriggerConfig.marker,\n      invalidateOnRefresh: true\n    }\n  });\n};\nvar setupResponsiveHorizontalCards = function setupResponsiveHorizontalCards(cards, timeline) {\n  //GSAP's Match Media for Responsive\n  var mediaQuery = gsap.matchMedia();\n\n  // desktop setup code here...\n  mediaQuery.add(\"(min-width: 1000px)\", function () {\n    cards.forEach(function (card, index) {\n      var bgColor = card.getAttribute(\"data-bgColor\");\n      var animatedHrCard = jQuery(card).data(\"stacked_card_hr\");\n      var spacer = 0;\n      gsap.set(card, {\n        backgroundColor: bgColor\n      });\n      if (index > 0) {\n        window.addEventListener(\"resize\", function () {\n          ScrollTrigger.refresh();\n        });\n        timeline.fromTo(card, {\n          y: 0,\n          x: window.innerWidth / 1.125 + spacer * index,\n          stagger: 0.5,\n          backgroundColor: bgColor,\n          opacity: 0\n        }, _objectSpread(_objectSpread({\n          y: 0,\n          opacity: 1\n        }, animatedHrCard), {}, {\n          // x: spacer * (index + 1),\n          stagger: 0.5,\n          backgroundColor: bgColor,\n          zIndex: index + 1\n        }));\n      }\n    });\n  });\n\n  // tablet device code here...\n  mediaQuery.add(\"(min-width: 800px) and (max-width: 999px)\", function () {\n    cards.forEach(function (card, index) {\n      var bgColor = card.getAttribute(\"data-bgColor\");\n      var animatedHrCard = jQuery(card).data(\"stacked_card_hr\");\n      var spacer = 0;\n      gsap.set(card, {\n        backgroundColor: bgColor\n      });\n      if (index > 0) {\n        window.addEventListener(\"resize\", function () {\n          ScrollTrigger.refresh();\n        });\n        timeline.fromTo(card, {\n          x: 0,\n          y: window.innerWidth / 1.125 + spacer * index,\n          stagger: 0.5,\n          backgroundColor: bgColor,\n          opacity: 0\n        }, {\n          x: 0,\n          y: animatedHrCard.x,\n          // y: spacer * (index + 1),\n          opacity: 1,\n          rotation: animatedHrCard.rotation,\n          stagger: 0.5,\n          backgroundColor: bgColor,\n          zIndex: index + 1\n        });\n      }\n    });\n  });\n\n  // mobile device ccode here...\n  mediaQuery.add(\"(max-width: 799px)\", function () {\n    cards.forEach(function (card, index) {\n      var bgColor = card.getAttribute(\"data-bgColor\");\n      var animatedHrCard = jQuery(card).data(\"stacked_card_hr\");\n      var spacer = 0;\n      gsap.set(card, {\n        backgroundColor: bgColor\n      });\n      if (index > 0) {\n        window.addEventListener(\"resize\", function () {\n          ScrollTrigger.refresh();\n        });\n        timeline.fromTo(card, {\n          x: 0,\n          y: window.innerWidth / 1.125 + spacer * index,\n          stagger: 0.5,\n          backgroundColor: bgColor,\n          opacity: 0\n        }, {\n          x: 0,\n          y: animatedHrCard.x,\n          // y: spacer * (index + 1),\n          opacity: 1,\n          rotation: animatedHrCard.rotation,\n          stagger: 0.5,\n          backgroundColor: bgColor,\n          zIndex: index + 1\n        });\n      }\n    });\n  });\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  if (eael.elementStatusCheck(\"stackedCard\")) {\n    return false;\n  }\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-stacked-cards.default\", StackedCardHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/stacked-cards.js?");

/***/ })

/******/ });