var page =
{
	container: "AD628B",
	
	load: function(pageName, projectId = null)
	{//{{{
		var url = "";
		switch(pageName) {
			case("projects"):
				url = "page.php?name=projects";
				break;
			case("about"):
				url = "page.php?name=about";
				break;
			case("contacts"):
				url = "page.php?name=contacts";
				break;
			case("project"):
				url = "page.php?name=project&id=" + String(projectId);
				break;
			default:
				return(false);
		}
		
		var req = new XMLHttpRequest();
		req.addEventListener("load", this.onLoad.bind(this, req, pageName));
		req.open("GET", url);
		req.send();
	},//}}}
	
	onLoad: function(req, pageName)
	{//{{{
		let html = req.responseText;
		this.container.innerHTML = html;
		if(pageName == 'projects') {
			this.initProjectLinks();
		}
	},//}}}
	
	initProjectLinks: function()
	{//{{{
		let links = this.container.querySelectorAll("a[name='project']");
		let i = 0, l = links.length;
		for(i = i; i < l; i += 1) {
			let link = links[i];
			let id = link.getAttribute("href");
			link.addEventListener("click", this.onClickProjectLink.bind(this, id))
		}
	},//}}}
	
	onClickProjectLink: function(id, event)
	{//{{{
		event.preventDefault();
		this.load("project", id);
	},//}}}
	
};

window.addEventListener("load", function() {
	page.container = document.getElementById(page.container);
});

