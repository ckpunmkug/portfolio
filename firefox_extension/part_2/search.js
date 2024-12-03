var $search_query = "abcd";

if(document.URL == "https://duckduckgo.com/") {
	document.execCommand("insertText", false, $search_query);
	let $form = document.getElementById("searchbox_homepage");
	$form.submit();
} 
else {
	let $URL = new URL(document.URL);
	if(
		$URL.host == "duckduckgo.com"
		&& $URL.search == "?t=h_&q="+ $search_query +"&ia=web"
	) {
		parse();
	}
}

function parse()
{
	let $result = [];

	let $A = document.getElementsByTagName("A");
	let $i = 0, $l = $A.length;
	for($i = $i; $i < $l; $i++) {
		let $style = window.getComputedStyle($A[$i]);
		let $line_height = $style.getPropertyValue("line-height");
		if($line_height == "25.65px") {
			$result.push($A[$i].getAttribute("href"));
		}
	}
	
	send_result($result);
}

async function send_result($result)
{
	let $sending = browser.runtime.sendMessage(null, $result, null);
	console.log($sending);
}

