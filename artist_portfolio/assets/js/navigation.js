window.addEventListener("load", function(event) {

	var navLinks = {
		default_class: 'nav-list__link',
		active_class: 'nav-list__link--active',
		
		activeLink: null,
		setLinkToActive: function(linkName) 
		{
			let active_class = this.default_class + ' ' + this.active_class;
		
			if(this.activeLink !== null) {
				this.activeLink.setAttribute("class", this.default_class);
			}
			
			switch(linkName) {
				case("projects"):
					this.projects.setAttribute("class", active_class);
					this.activeLink = this.projects;
					break;
				case("about"):
					this.about.setAttribute("class", active_class);
					this.activeLink = this.about;
					break;
				case("contacts"):
					this.contacts.setAttribute("class", active_class);
					this.activeLink = this.contacts;
					break;
			}
			
		},
		
		onClick: function(linkName, event) 
		{
			event.preventDefault();
			this.setLinkToActive(linkName);
			page.load(linkName);
		},
	};
	
	navLinks.projects = document.querySelector("a[name='projects']");
	navLinks.projects.addEventListener("click", navLinks.onClick.bind(navLinks, "projects"));
	
	navLinks.about = document.querySelector("a[name='about']");
	navLinks.about.addEventListener("click", navLinks.onClick.bind(navLinks, "about"));
	
	navLinks.contacts = document.querySelector("a[name='contacts']");
	navLinks.contacts.addEventListener("click", navLinks.onClick.bind(navLinks, "contacts"));

	navLinks.setLinkToActive("projects");
	page.load("projects");
});
