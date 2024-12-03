console.log("Background script completed");

async function openControlPage()
{
	let $url = browser.runtime.getURL("page/index.html");
	console.log("Control page url", $url);
	
	let $createProperties = {
		"url": $url	
	};
	
	let $Tab = await browser.tabs.create($createProperties);
	console.log("Tab object", $Tab);
}

function onClickBrowserAction($tab, $OnClickData)
{
	openControlPage();
	console.log("Browser action button clicked", $tab, $OnClickData);
}
browser.browserAction.onClicked.addListener(onClickBrowserAction)
