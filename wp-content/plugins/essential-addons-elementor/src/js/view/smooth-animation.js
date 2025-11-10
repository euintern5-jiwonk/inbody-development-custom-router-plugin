let SmoothAnimationHandler = function ($scope, $) {
    // console.log('Scope',  $scope[0].className);

    // if( $scope[0].className.includes('eael_smooth_animation') === false ) {
    //     return;
    // }

    let $scopeId = $scope.data('id'),
    $presetData = $scope.data('preset'),
    $eventFunction = $scope.data('event_function'),
    $coreTriggerData = $scope.data('coretrigger'),
    $translateX = $scope.data('translatex'),
    $translateY = $scope.data('translatey'),
    $opacityData = $scope.data('opacity'),
    $rotationData = $scope.data('rotation'),
    $scaleData = $scope.data('scale'),
    $scaleXData = $scope.data('scalex'),
    $scaleYData = $scope.data('scaley'),
    $skewData = $scope.data('skew'),
    $skewXData = $scope.data('skewx'),
    $skewYData = $scope.data('skewy'),
    $durationData = $scope.data('duration'),
    $delayData = $scope.data('delay'),
    $repeatData = $scope.data('repeat'),
    $easeData = $scope.data('ease'),
    $yoyoData = $scope.data('yoyo'),
    $staggerData = $scope.data('stagger'),
    $transformOriginXData = $scope.data('transformoriginx'),
    $customTransformOriginXData = $scope.data('custom_transformoriginx'),
    $transformOriginYData = $scope.data('transformoriginy'),
    $customTransFormOriginYData = $scope.data('custom_transformoriginy'),
    $bgColorData = $scope.data('bg_color'),

    // ScrollTrigger

    //Start
    $elementStartData = $scope.data('element_start'),
    $customElementStartData = $scope.data('custom_element_start'),
    $controllerStartData = $scope.data('controller_start'),
    $customControllerStartData = $scope.data('custom_controller_start'),
    
    //End
    $elementEndData = $scope.data('element_end'),
    $customElementEndData = $scope.data('custom_element_end'),
    $controllerEndData = $scope.data('controller_end'),
    $customControllerEndData = $scope.data('custom_controller_end'),

    $markersData = $scope.data('markers'),
    $scrollon = $scope.data('scrollon'),
    $scrubData = $scope.data('scrub'),
    
    $toggleActionsOnEnterData = $scope.data('toggle_actions_on_enter'),
    $toggleActionsOnLeaveData = $scope.data('toggle_actions_on_leave'),
    $toggleAactionsOnEnterBackData = $scope.data('toggle_actions_on_enter_back'),
    $toggleActionsOnLeaveBackData = $scope.data('toggle_actions_on_leave_back'),
    $pinData = $scope.data('pin');

    //Is Editor
    if ( window.isEditMode ) {
        if(window[`eael_sa_${$scopeId}`] === true){
            return;
        }

        if( window[`eael_sa_${$scopeId}`] === undefined && window.isEditMode || 1 ) {
            window[`eael_sa_${$scopeId}`] = true;
            var eaelEditModeSettings = [];
    
            function getHoverEffectSettingsVal( $el ) {
                $.each($el, function (i, el) {
                    let $getSettings = el.attributes.settings.attributes;
                    if ( el.attributes.elType === 'widget' ) {
                        if ( $getSettings['eael_smooth_animation_section'] === 'yes' ) {
                            eaelEditModeSettings[el.attributes.id] = el.attributes.settings.attributes;
                        }
                    }

                    if ( el.attributes.elType === 'container' ) {
                        getHoverEffectSettingsVal( el.attributes.elements.models );
                    }
    
                    if ( el.attributes.elType === 'section' ) {
                        getHoverEffectSettingsVal( el.attributes.elements.models );
                    }
    
                    if ( el.attributes.elType === 'column' ) {
                        getHoverEffectSettingsVal( el.attributes.elements.models );
                    }
                });
            }
    
            getHoverEffectSettingsVal( window.elementor.elements.models );
        }

        //
        for ( const key in eaelEditModeSettings ) {
            if ( $scopeId === key ) {
                const smoothAnimationTransformSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_transform_setting'];
                const smoothAnimationTransformOrigin = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_transform_orign_setting'];
                const smoothAnimationScaleSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_scale_setting'];
                const smoothAnimationSkewSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_skew_setting'];
                const smoothAnimationColorSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_color_setting'];
                const smoothAnimationAnimationSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_animation_setting'];
                const smoothAnimationManualSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_manual_setting'];
                const smoothAnimationMarkersSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_markers'];
                const smoothAnimationStartSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_canvas_start'];
                const smoothAnimationEndSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_canvas_end'];
                const smoothAnimationScarbSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_canvas_scrub'];
                const smoothAnimationPinSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_event_pin'];
                const smoothAnimationToggleActionsSettings = eaelEditModeSettings?.[key]?.['eael_smooth_animation_toggle_actions'];
                
                let {
                    eael_smooth_animation_event_function: eventFunction,
                    eael_smooth_animation_event_transform_translatex: translateXData,
                    eael_smooth_animation_event_transform_translatey: translateYData,
                    eael_smooth_animation_transform_originx: transformOriginXData,
                    eael_smooth_animation_trans_originx_custom: customTransformOriginXData,
                    eael_smooth_animation_transform_originy: transformOriginYData,
                    eael_smooth_animation_trans_originy_custom: customTransFormOriginYData,
                    eael_smooth_animation_event_transform_opacity: opacityData,
                    eael_smooth_animation_event_transform_rotate: rotationData,
                    eael_smooth_animation_event_scalexy: scaleXYData,
                    eael_smooth_animation_event_scale: scaleData,
                    eael_smooth_animation_event_scalex: scaleXData,
                    eael_smooth_animation_event_scaley: scaleYData,
                    eael_smooth_animation_event_skew: skewData,
                    eael_smooth_animation_event_skewx: skewXData,
                    eael_smooth_animation_event_skewy: skewYData,
                    eael_smooth_animation_event_bg_color: bgColorData,
                    eael_smooth_animation_event_animation_easing: easeData,
                    eael_smooth_animation_event_animation_yoyo: yoyoData,
                    eael_smooth_animation_event_animation_stagger: staggerData,
                    eael_smooth_animation_event_duration: durationData,
                    eael_smooth_animation_event_delay: delayData,
                    eael_smooth_animation_event_loop: repeatData,
                    eael_smooth_animation_event_markers: markersData,
                    eael_smooth_animation_event_canvas_element_start: elementStartData,
                    eael_smooth_animation_event_canvas_controller_start: controllerStartData,
                    eael_smooth_animation_event_canvas_element_start_custom: customElementStartData,
                    eael_smooth_animation_event_canvas_controller_start_custom: customControllerStartData,
                    eael_smooth_animation_event_canvas_element_end: elementEndData,
                    eael_smooth_animation_event_canvas_controller_end: controllerEndData,
                    eael_smooth_animation_event_canvas_element_end_custom: customElementEndData,
                    eael_smooth_animation_event_canvas_controller_end_custom: customControllerEndData,
                    eael_smooth_animation_event_scrub_settings: scrubData,
                    eael_smooth_animation_event_scrub_setting_default: scrubDataDefault,
                    eael_smooth_animation_event_canvas_scrub_custom: scrubDataCustom,
                    eael_smooth_animation_toggle_actions_on_enter: toggleActionsOnEnterData,
                    eael_smooth_animation_toggle_actions_on_leave: toggleActionsOnLeaveData,
                    eael_smooth_animation_toggle_actions_on_enter_back: toggleAactionsOnEnterBackData,
                    eael_smooth_animation_toggle_actions_on_leave_back: toggleActionsOnLeaveBackData,
                    eael_smooth_animation_event_pin_setting_default: pinData
                } = eaelEditModeSettings?.[key];

                $eventFunction = eventFunction ? { tween: eventFunction } : 'to';
                
                if ( 'yes' === smoothAnimationTransformSettings ) {
                    $translateX = translateXData?.['size'] ? {
                        size: translateXData['size'],
                        unit: translateXData['unit']
                    } : null;

                    $translateY = translateYData?.['size'] ? {
                        size: translateYData['size'],
                        unit: translateYData['unit']
                    } : null;

                    $opacityData = opacityData?.['size'] ? { opacity: opacityData['size'] } : null;
                    $rotationData = rotationData?.['size'] ? { rotation: rotationData['size'] } : null;
                }

                //Transform Origin
                if ( 'yes' === smoothAnimationTransformOrigin ) {
                    //Transform Origin X
                    if ( 'custom' !== transformOriginXData ) {
                        $transformOriginXData = transformOriginXData ? { transformoriginx: transformOriginXData } : '';
                    } else {
                        $customTransformOriginXData = customTransformOriginXData?.['size'] ? {
                            size: customTransformOriginXData['size'],
                            unit: customTransformOriginXData['unit']
                        } : null;
                    }

                    //Transform Origin Y
                    if ( 'custom' !== transformOriginYData ) {
                        $transformOriginYData = transformOriginYData ? { transformoriginy: transformOriginYData } : '';
                    } else {
                        $customTransFormOriginYData = customTransFormOriginYData?.['size'] ? {
                            size: customTransFormOriginYData['size'],
                            unit: customTransFormOriginYData['unit']
                        } : null;
                    }
                }
                
                //Color
                if ( 'yes' === smoothAnimationColorSettings ) {
                    $bgColorData = bgColorData ? { bg_color: bgColorData } : null;
                }

                //Scale
                if ( 'yes' === smoothAnimationScaleSettings ) {
                    if ( 'yes' === scaleXYData ) {
                        $scaleXData = scaleXData?.['size'] ? { scalex: scaleXData['size'] } : null;      
                        $scaleYData = scaleYData?.['size'] ? { scaley: scaleYData['size'] } : null;
                    } else {
                        $scaleData = scaleData?.['size'] ? { scale: scaleData['size'] } : null;      
                    }
                }

                //Skew
                if ( 'yes' === smoothAnimationSkewSettings ) {
                    $skewXData = skewXData?.['size'] ? { skewx: skewXData['size'] }: null;
                    $skewYData = skewYData?.['size'] ? { skewy: skewYData['size'] }: null;
                }

                //Animation Settings
                if ( 'yes' === smoothAnimationAnimationSettings ) {
                    $easeData    = easeData ? { ease      : easeData }: null;
                    $yoyoData    = yoyoData ? { yoyo      : yoyoData }: false;
                    $staggerData = staggerData ? { stagger: staggerData } : '';
                }

                //Manual Settings
                if ( 'yes' === smoothAnimationManualSettings ) {
                    $durationData = durationData ? { duration: durationData['size'] }: '';
                    $delayData    = delayData ? { delay      : delayData['size'] }   : '';
                    $repeatData   = repeatData ? { repeat    : repeatData }  : '';
                }

                //ScrollTrigger Options
                //Start
                if ( 'yes' === smoothAnimationStartSettings ) {
                    if ( 'custom' !== elementStartData ) {
                        $elementStartData = elementStartData ? { element_start: elementStartData } : '';
                    } else {
                        $customElementStartData = customElementStartData?.['size'] ? {
                            size: customElementStartData['size'],
                            unit: customElementStartData['unit']
                        } : null;
                    }
                    if ( 'custom' !== controllerStartData ) {
                        $controllerStartData = controllerStartData ? { controller_start: controllerStartData } : '';
                    } else {
                        $customControllerStartData = customControllerStartData?.['size'] ? {
                            size: customControllerStartData['size'],
                            unit: customControllerStartData['unit']
                        } : null;
                    }
                }

                //End
                if ( 'yes' === smoothAnimationEndSettings ) {
                    if ( 'custom' !== elementEndData ) {
                        $elementEndData = elementEndData ? { element_end: elementEndData } : '';
                    } else {
                        $customElementEndData = customElementEndData?.['size'] ? {
                            size: customElementEndData['size'],
                            unit: customElementEndData['unit']
                        } : null;
                    }
                    if ( 'custom' !== controllerEndData ) {
                        $controllerEndData = controllerEndData ? { controller_end: controllerEndData } : '';
                    } else {
                        $customControllerEndData = customControllerEndData?.['size'] ? {
                            size: customControllerEndData['size'],
                            unit: customControllerEndData['unit']
                        } : null;
                    }
                }

                //Markers Settings
                if ( 'true' === smoothAnimationMarkersSettings ) {
                    $markersData = markersData ? { markers: markersData } : '';
                }
                
                //Scrub Settings
                if ( 'yes' === smoothAnimationScarbSettings ) {
                    if ( 'custom' !== scrubData ) {
                        $scrubData = scrubDataDefault ? { scrub: scrubDataDefault } : '';
                    } else {
                        $scrubData = scrubDataCustom ? { scrub: scrubDataCustom['size'] } : '';
                    }
                }

                //ToggleActions
                if ( 'yes' === smoothAnimationToggleActionsSettings ) {
                    $toggleActionsOnEnterData = toggleActionsOnEnterData ? { toggle_actions_on_enter: toggleActionsOnEnterData } : '';
                    $toggleActionsOnLeaveData = toggleActionsOnLeaveData ? { toggle_actions_on_leave: toggleActionsOnLeaveData } : '';
                    $toggleAactionsOnEnterBackData = toggleAactionsOnEnterBackData ? { toggle_actions_on_enter_back: toggleAactionsOnEnterBackData } : '';
                    $toggleActionsOnLeaveBackData = toggleActionsOnLeaveBackData ? { toggle_actions_on_leave_back: toggleActionsOnLeaveBackData } : '';
                }

                //Pin Settings
                if ( 'yes' === smoothAnimationPinSettings ) {
                    $pinData = pinData ? { pin: pinData } : '';
                }
            }
        }
    }

    let $eventTarget = `.elementor-element-${$scopeId}`;

    let $coreTrigger = $coreTriggerData ? `[data-coretrigger="${$coreTriggerData}"]` : $eventTarget;
    let tweenInstance = $eventFunction?.tween ? $eventFunction?.tween : 'to';
    let $x = $translateX?.size ? `${$translateX?.size}${$translateX?.unit}` : '';
    let $y = $translateY?.size ? `${$translateY?.size}${$translateY?.unit}` : '';
    let $opacity = $opacityData ? $opacityData?.opacity : '';
    let $rotation = $rotationData ? $rotationData?.rotation : '';
    let $scale = $scaleData ? $scaleData?.scale : '';
    let $scaleX = $scaleXData ? $scaleXData?.scalex : '';
    let $scaleY = $scaleYData ? $scaleYData?.scaley : '';
    // let $skew = $skewData ? $skewData?.skew : '';
    let $skewX = $skewXData ? $skewXData?.skewx : '';
    let $skewY = $skewYData ? $skewYData?.skewy : '';
    let $duration = $durationData ? $durationData?.duration : '';
    let $delay = $delayData ? $delayData?.delay : '';
    let $repeat = $repeatData ? $repeatData?.repeat : '';
    let $ease = $easeData ? $easeData?.ease : '';
    let $yoyo = $yoyoData ? $yoyoData?.yoyo : false;
    let $stagger = $staggerData ? $staggerData?.stagger : '';
    //transformOrigin
    const $transformOriginX = $transformOriginXData?.transformoriginx || '';
    const $transformOriginY = $transformOriginYData?.transformoriginy || '';
    const $customTransformOriginX = $customTransformOriginXData ? `${$customTransformOriginXData.size}${$customTransformOriginXData.unit}` : '';
    const $customTransformOriginY = $customTransFormOriginYData ? `${$customTransFormOriginYData.size}${$customTransFormOriginYData.unit}` : '';
    const $transformOrigin = [$transformOriginX, $transformOriginY, $customTransformOriginX, $customTransformOriginY].filter(Boolean).join(' ').trim();
    let $bgColor = $bgColorData ? $bgColorData?.bg_color : '';
    
    // Initialize an empty object to store properties
    let properties = {};

    if ($x !== undefined && $x !== null && $x !== '') {
        properties.x = $x;
    }

    if ($y !== undefined && $y !== null && $y !== '') {
        properties.y = $y;
    }

    if ($opacity !== undefined && $opacity !== null && $opacity !== '') {
        properties.opacity = $opacity;
    } 
    
    if ($rotation !== undefined && $rotation !== null && $rotation !== '') {
        properties.rotation = $rotation;
    }

    if ($duration !== undefined && $duration !== null && $duration !== '') {
        properties.duration = $duration;
    }
    
    if ($delay !== undefined && $delay !== null && $delay !== '') {
        properties.delay = $delay;
    }

    if ($repeat !== undefined && $repeat !== null && $repeat !== '') {
        properties.repeat = $repeat;
    }
    
    if ($scale !== undefined && $scale !== null && $scale !== '') {
        properties.scale = $scale;
    }

    if ($scaleX !== undefined && $scaleX !== null && $scaleX !== '') {
        properties.scaleX = $scaleX;
    }

    if ($scaleY !== undefined && $scaleY !== null && $scaleY !== '') {
        properties.scaleY = $scaleY;
    }

    // if ($skew !== undefined && $skew !== null && $skew !== '') {
    //     properties.skew = $skew;
    // }
    if ($skewX !== undefined && $skewX !== null && $skewX !== '') {
        properties.skewX = $skewX;
    }
    if ($skewY !== undefined && $skewY !== null && $skewY !== '') {
        properties.skewY = $skewY;
    }

    if ($ease !== undefined && $ease !== null && $ease !== '') {
        properties.ease = $ease;
    }

    if ($yoyo !== undefined && $yoyo !== null && $yoyo !== '') {
        properties.yoyo = $yoyo;
    }

    if ($stagger !== undefined && $stagger !== null && $stagger !== '') {
        properties.stagger = $stagger;
    }

    if ($bgColor !== undefined && $bgColor !== null && $bgColor !== '') {
        properties.backgroundColor = $bgColor;
    }

    if ($transformOrigin !== undefined && $transformOrigin !== null && $transformOrigin !== '') {
        properties.transformOrigin = $transformOrigin;
    }

        // let element = document.querySelector('.eael_smooth_animation');
        // let $scrolltrigger = element.id;
        let $scrolltrigger = '';

        //Start
        let $elementStart = $elementStartData?.element_start || '';
        let $controllerStart = $controllerStartData?.controller_start || '';
        let $start = [$elementStart, $controllerStart, `${$customElementStartData?.size || ''}${$customElementStartData?.unit || ''}`, `${$customControllerStartData?.size || ''}${$customControllerStartData?.unit || ''}`]
            .filter(Boolean)
            .join(' ')
            .trim();

        //End
        let $elementEnd = $elementEndData?.element_end || '';
        let $controllerEnd = $controllerEndData?.controller_end || '';
        let $end = [$elementEnd, $controllerEnd, `${$customElementEndData?.size || ''}${$customElementEndData?.unit || ''}`, `${$customControllerEndData?.size || ''}${$customControllerEndData?.unit || ''}`]
            .filter(Boolean)
            .join(' ')
            .trim();

    let $toggleActionsOnEnter = $toggleActionsOnEnterData ? $toggleActionsOnEnterData?.toggle_actions_on_enter : '';
    let $toggleActionsOnLeave = $toggleActionsOnLeaveData ? $toggleActionsOnLeaveData?.toggle_actions_on_leave : '';
    let $toggleAactionsOnEnterBack = $toggleAactionsOnEnterBackData ? $toggleAactionsOnEnterBackData?.toggle_actions_on_enter_back : '';
    let $toggleActionsOnLeaveBack = $toggleActionsOnLeaveBackData ? $toggleActionsOnLeaveBackData?.toggle_actions_on_leave_back : '';
    let $toggleActions = ($toggleActionsOnEnter || $toggleActionsOnLeave || $toggleAactionsOnEnterBack || $toggleActionsOnLeaveBack) ? `${ $toggleActionsOnEnter} ${$toggleActionsOnLeave} ${$toggleAactionsOnEnterBack} ${$toggleActionsOnLeaveBack}` : '';

    let $markers = $markersData ? $markersData?.markers : '';
    let $scrub = $scrubData?.scrub ? $scrubData?.scrub : '';
    
    let $pin = $pinData ? $pinData?.pin : '';

    let scrolltriggerVal = {};


    scrolltriggerVal.trigger = $scrolltrigger ? `#${$scrolltrigger}` : $eventTarget;
    
    // if ($eventTarget !== undefined && $eventTarget !== null && $eventTarget !== '') {
        // scrolltriggerVal.trigger = $eventTarget;
    // }
    if ($start !== undefined && $start !== null && $start !== '') {
        scrolltriggerVal.start = $start;
    }
    if ($end !== undefined && $end !== null && $end !== '') {
        scrolltriggerVal.end = $end;
    }
    if ($scrub !== undefined && $scrub !== null && $scrub !== '') {
        scrolltriggerVal.scrub = $scrub;
    }
    if ($markers !== undefined && $markers !== null && $markers !== '') {
        scrolltriggerVal.markers = $markers;
    }
    if ($toggleActions !== undefined && $toggleActions !== null && $toggleActions !== '') {
        scrolltriggerVal.toggleActions = $toggleActions ? $toggleActions : 'play';
    }
    if ($pin !== undefined && $pin !== null && $pin !== '') {
        scrolltriggerVal.pin = Boolean($pin);
    }

    //
    properties.scrollTrigger = scrolltriggerVal;


    // gsap code here!
    if( $scrollon === 'on' ){
        gsap.registerPlugin(ScrollTrigger);
    }
    
    gsap[tweenInstance]($coreTrigger, properties);
}

jQuery(window).on("elementor/frontend/init", function () {
    if (eael.elementStatusCheck('eaelSmoothAnimation')) {
        return false;
    }
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/widget", 
        SmoothAnimationHandler
    );
    elementorFrontend.hooks.addAction( "frontend/element_ready/container", SmoothAnimationHandler );
    // elementorFrontend.hooks.addAction( "frontend/element_ready/section", SmoothAnimationHandler );
    // elementorFrontend.hooks.addAction( "frontend/element_ready/column", SmoothAnimationHandler );
});