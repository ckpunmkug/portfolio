
var $pauseBeforeShowingSubscriptionWidget = 3; // sec  (Пауза перед появлением окна подписки на канал в секудах)
var $subscriptionWidgetAnimation = {
	framesCount: 20, // (Количество кадров в анимации)
	pauseBetweenFrames: 30, // msec (Пауза между кадрами в милисекундах)
};

var $subscriptionWidget = null;
function subscriptionWidget($container)
{
	this.container = $container;
	
	this.animation = {
		frames_count: $subscriptionWidgetAnimation.framesCount,
		pause_between_frames: $subscriptionWidgetAnimation.pauseBetweenFrames,
		current_frame_number: 0,
	};
	
	
	this.isSubscribed = function()
	{
		let $return = localStorage.getItem("userSubscribedTelegramChannel");
		if(typeof($return) == 'string' && $return == '1') {
			return(true);
		}
		return(false);
	}
	
	this.showWidget = function()
	{
		this.container.style.setProperty("display", "block");
		
		let $DOMRectList = this.container.getClientRects();
		let $containerHeight = $DOMRectList[0].height;
		
		if(this.animation.current_frame_number == 0) {
			this.animation.current_frame_number = 1;
			this.container.style.setProperty("bottom", "-" + String($containerHeight) + "px");
		}
		
		let $offsetStep = $containerHeight / this.animation.frames_count;
		let $offset = $offsetStep * this.animation.current_frame_number;
		
		this.container.style.setProperty("bottom", "-" + String($containerHeight - $offset) + "px");
	
		this.animation.current_frame_number += 1;
		
		if(this.animation.current_frame_number > this.animation.frames_count) {
			this.container.style.setProperty("bottom", "0px");
			return(true);
		}
		
		setTimeout(this.showWidget.bind(this), this.animation.pause_between_frames);
		
		return(true);
	}
	
	this.hideWidget = function()
	{
		this.container.style.setProperty("display", "none");
	}
	
	this.setSubscribed = function()
	{
		localStorage.setItem("userSubscribedTelegramChannel", '1');
		this.container.style.setProperty("display", "none");
	}

	let $return, $element;
	
	$element = this.container.querySelector('button[name="close"]');
	$element.addEventListener("click", this.hideWidget.bind(this));
	
	$element = this.container.querySelector('button[name="subscribe"]');
	$element.addEventListener("click", this.setSubscribed.bind(this));
	
	$return = this.isSubscribed();
	if($return) {
		return(true);
	}
	
	this.showWidget();
}
window.addEventListener("load", function(event) {
	let $element = document.querySelector('div[name="subscription_widget"]');
	setTimeout(function() {
	
		$subscriptionWidget = new subscriptionWidget($element);

	}, $pauseBeforeShowingSubscriptionWidget * 1000);
	
});

