var StackedCardHandler = function ($scope, $) {
   window.onbeforeunload = function () {
      window.scrollTo({
         top: 0,
         behavior: "smooth",
         duration: 400,
      });
   };

   gsap.registerPlugin(ScrollTrigger);

   const container = $scope.find(".eael-stacked-cards__container");
   const cardStyle = container.attr("data-cadr_style");
   const scrollTriggerConfig = JSON.parse(container.attr("data-scrolltrigger"));
   const elements = {
      verticalCards: gsap.utils.toArray(
         $scope.find(".eael-stacked-cards__item")
      ),
      horizontalCards: gsap.utils.toArray(
         $scope.find(".eael-stacked-cards__item_hr")
      ),
      container: container,
   };

   //Setup hover effects for all cards
   setupHoverEffects($scope, $);

   if ("vertical" === cardStyle) {
      initVerticalCards(elements, scrollTriggerConfig);
   } else if ("horizontal" === cardStyle) {
      inithorizontalCards(elements, scrollTriggerConfig);
   }
};

const setupHoverEffects = ($scope, $) => {
   $scope.find(".eael-stacked-cards__link").each((_, button) => {
      const $button = $(button);
      const normalStyle = $button.attr("style");
      const hoverStyle = $button.attr("data-hover-style");

      $button
         .on("mouseenter", () => $button.attr("style", hoverStyle))
         .on("mouseleave", () => $button.attr("style", normalStyle));
   });
};

const initVerticalCards = (elements, scrollTriggerConfig) => {
   const animation = createVerticalAnimation(elements.verticalCards);
   createVerticalScrollTrigger(
      elements.container,
      animation,
      scrollTriggerConfig,
      elements.verticalCards.length
   );
};

const createVerticalAnimation = (cards) => {
   const animation = gsap.timeline();
   if (!cards || !cards.length) return animation;

   //Inital setup for each card
   cards.forEach((card, index) => {
      const $card = jQuery(card),
         startForm = $card.data("start_form"),
         bgColor = card.getAttribute("data-bgColor"),
         animatedCard = $card.data("stacked_card");
      const getOpacity =
         animatedCard?.opacity === 1.1 ? 0 : animatedCard?.opacity;

      gsap.set(card, {
         position: "absolute",
         top: 0,
         left: 0,
         filter: "blur(0px)",
         y: index === 0 ? 0 : startForm * (index + 1), //Calculated scroll Y value
         opacity: index === 0 ? 1 : getOpacity,
         rotation: 0,
         backgroundColor: bgColor,
      });
   });

   //Create animation sequence for each card
   cards.forEach((card, index) => {
      let animatedCard = jQuery(card).data("stacked_card") || {};
      const dataTranslate = parseInt(card.getAttribute("data-yaxis"));
      const prevCard = cards[index - 1];
      const isLastCard = index === cards.length - 1;

      if (index > 0) {
         window.addEventListener("resize", () => {
            ScrollTrigger.refresh();
         });
         animation.to(
            card,
            {
               opacity: 1,
               y: isLastCard
                  ? (animatedCard.y || 0) + dataTranslate
                  : animatedCard.y || 0,
               duration: 1,
               ease: "Power2.out",
            },
            index
         );
         // animation.to(cards[index - 1], { ...animatedCard }, index);
         if (prevCard) {
            animation.to(
               prevCard,
               {
                  ...animatedCard,
               },
               index
            );
         }
      } else {
         window.addEventListener("load", () => {
            window.addEventListener("resize", () => {
               ScrollTrigger.refresh();
            });

            animation.to(
               card,
               {
                  opacity: 1,
                  y: animatedCard.y || 0,
                  duration: 1,
                  ease: "Power2.out",
               },
               index
            );
            // animation.to(cards[index - 1], { ...animatedCard }, index);
            if (prevCard) {
               animation.to(
                  prevCard,
                  {
                     ...animatedCard,
                  },
                  index
               );
            }
         });
      }
   });
   return animation;
};

const createVerticalScrollTrigger = (
   container,
   animation,
   scrollConfig,
   cardCount
) => {
   // Calculate the total height based on the number of cards and the viewport height
   const totalHeight = cardCount * window.innerHeight;
   const scrollEnd =
      scrollConfig.end === "default" ? totalHeight : scrollConfig.end;

   // Create a ScrollTrigger instance with the parsed data and calculated values
   ScrollTrigger.create({
      trigger: container,
      invalidateOnRefresh: true,
      animation,
      start: `top top+=${scrollConfig.start}`,
      end: `+=${scrollEnd}`, // Dynamic end point
      scrub: true,
      pin: true,
      markers: scrollConfig.marker,
   });
};

const inithorizontalCards = (elements, scrollTriggerConfig) => {
   const timeline = createHorizontalTimeline(
      elements.container,
      scrollTriggerConfig
   );

   setupResponsiveHorizontalCards(elements.horizontalCards, timeline);
};

const createHorizontalTimeline = (container, scrollTriggerConfig) => {
   return gsap.timeline({
      scrollTrigger: {
         trigger: container,
         pin: true,
         scrub: 0.5,
         start: `top ${scrollTriggerConfig.start}`,
         end: `bottom ${scrollTriggerConfig.end}`,
         markers: scrollTriggerConfig.marker,
         invalidateOnRefresh: true,
      },
   });
};

const setupResponsiveHorizontalCards = (cards, timeline) => {
   //GSAP's Match Media for Responsive
   let mediaQuery = gsap.matchMedia();

   // desktop setup code here...
   mediaQuery.add("(min-width: 1000px)", () => {
      cards.forEach((card, index) => {
         let bgColor = card.getAttribute("data-bgColor");
         let animatedHrCard = jQuery(card).data("stacked_card_hr");
         let spacer = 0;
         gsap.set(card, { backgroundColor: bgColor });
         if (index > 0) {
            window.addEventListener("resize", () => {
               ScrollTrigger.refresh();
            });
            timeline.fromTo(
               card,
               {
                  y: 0,
                  x: window.innerWidth / 1.125 + spacer * index,
                  stagger: 0.5,
                  backgroundColor: bgColor,
                  opacity: 0,
               },
               {
                  y: 0,
                  opacity: 1,
                  ...animatedHrCard,
                  // x: spacer * (index + 1),
                  stagger: 0.5,
                  backgroundColor: bgColor,
                  zIndex: index + 1,
               }
            );
         }
      });
   });

   // tablet device code here...
   mediaQuery.add("(min-width: 800px) and (max-width: 999px)", () => {
      cards.forEach((card, index) => {
         let bgColor = card.getAttribute("data-bgColor");
         let animatedHrCard = jQuery(card).data("stacked_card_hr");
         let spacer = 0;

         gsap.set(card, { backgroundColor: bgColor });
         if (index > 0) {
            window.addEventListener("resize", () => {
               ScrollTrigger.refresh();
            });
            timeline.fromTo(
               card,
               {
                  x: 0,
                  y: window.innerWidth / 1.125 + spacer * index,
                  stagger: 0.5,
                  backgroundColor: bgColor,
                  opacity: 0,
               },
               {
                  x: 0,
                  y: animatedHrCard.x,
                  // y: spacer * (index + 1),
                  opacity: 1,
                  rotation: animatedHrCard.rotation,
                  stagger: 0.5,
                  backgroundColor: bgColor,
                  zIndex: index + 1,
               }
            );
         }
      });
   });

   // mobile device ccode here...
   mediaQuery.add("(max-width: 799px)", () => {
      cards.forEach((card, index) => {
         let bgColor = card.getAttribute("data-bgColor");
         let animatedHrCard = jQuery(card).data("stacked_card_hr");
         let spacer = 0;

         gsap.set(card, { backgroundColor: bgColor });
         if (index > 0) {
            window.addEventListener("resize", () => {
               ScrollTrigger.refresh();
            });
            timeline.fromTo(
               card,
               {
                  x: 0,
                  y: window.innerWidth / 1.125 + spacer * index,
                  stagger: 0.5,
                  backgroundColor: bgColor,
                  opacity: 0,
               },
               {
                  x: 0,
                  y: animatedHrCard.x,
                  // y: spacer * (index + 1),
                  opacity: 1,
                  rotation: animatedHrCard.rotation,
                  stagger: 0.5,
                  backgroundColor: bgColor,
                  zIndex: index + 1,
               }
            );
         }
      });
   });
};

jQuery(window).on("elementor/frontend/init", function () {
   if (eael.elementStatusCheck("stackedCard")) {
      return false;
   }

   elementorFrontend.hooks.addAction(
      "frontend/element_ready/eael-stacked-cards.default",
      StackedCardHandler
   );
});
