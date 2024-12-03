var $textarea = null;

function save()
{
	let $value = $textarea.value;
	localStorage.setItem("textarea", $value);
}

function load()
{
	$value = localStorage.getItem("textarea");
	$textarea.value = $value;
}

function print()
{
	let $value = $textarea.value;
	document.body.innerText = $value;
}

function windowOnLoad(event)
{
	$textarea = document.querySelector("textarea");

	let $element = null;
	
	$element = document.querySelector("button[name='save']");
	$element.addEventListener("click", save);

	$element = document.querySelector("button[name='load']");
	$element.addEventListener("click", load);
	
	$element = document.querySelector("button[name='print']");
	$element.addEventListener("click", print);
}
window.addEventListener("load", windowOnLoad);

