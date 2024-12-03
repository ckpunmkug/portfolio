var $serviceTabId = null;
var $controlTabId = null;

async function onUpdateTab($tabId, $changeInfo, $tab)
{
	if(
		$tab.status == "complete"
		&& $changeInfo.status == "complete"
		&& $tab.id == $serviceTabId
	) {
		await browser.tabs.executeScript(
			$serviceTabId, 
			{
			file: "/search.js"
			}
		);
	}
	
	if(
		$tab.status == "complete"
		&& $changeInfo.status == "complete"
		&& $tab.id == $controlTabId
	) {
		await browser.tabs.executeScript(
			$controlTabId, 
			{
			file: "/content.js"
			}
		);
	}
}

async function onClickBrowserAction($tab, $OnClickData)
{
	let $Tab = null;
	
	browser.tabs.onUpdated.addListener(onUpdateTab);
	
	$Tab = await browser.tabs.create({"url": "https://duckduckgo.com/"});
	$serviceTabId = $Tab.id;
	
	let $url = browser.runtime.getURL("/control.html");
	$Tab = await browser.tabs.create({ url: $url });
	$controlTabId = $Tab.id;
}
browser.browserAction.onClicked.addListener(onClickBrowserAction)

async function onMessageRuntime($message, $sender, $sendResponse) {
	console.log($controlTabId);
	
	let $return = await browser.tabs.sendMessage($controlTabId, $message);
}
browser.runtime.onMessage.addListener(onMessageRuntime)

