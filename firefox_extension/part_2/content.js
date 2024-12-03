var $textarea = null;
$textarea = document.querySelector("textarea");

function onMessageRuntime($message, $sender)
{
	$textarea.value = $message;	
}
browser.runtime.onMessage.addListener(onMessageRuntime)

