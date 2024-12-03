<?php
$about = Data::get_about();
if(!is_array($about)) {
	trigger_error("Can't get 'about' from database", E_USER_WARNING);
	return(false);
}

$projects = Data::select_projects();
if(!is_array($projects)) {
	trigger_error("Can't get projects", E_USER_WARNING);
	return(false);
}

$html = '';
foreach($projects as $project) {
	$html .=
<<<HEREDOC
                <li class="project">
                    <a href="{$project["id"]}" name="project">
                        <img src="assets/img/uploaded/{$project["image"]}" alt="Project img" class="project__img">
                        <h3 class="project__title">{$project["text1"]}</h3>
                    </a>
                </li>
		
HEREDOC;
	
}

?>    
    <header class="header">
        <div class="header__wrapper">
            <h1 class="header__title">
                <strong>Hi, my name is <?= $about["title"]; ?> <em><Empty></Empty></em></strong><br>
                an artist
            </h1>
            <div class="header__text">
                <p>here you can see examples of my work</p>
            </div>

        </div>
    </header>


    <main class="section">
        <div class="container">
            <h2 class="title-1">Projects</h2>
            <ul class="projects">
		<?= $html; ?>
            </ul>
        </div>
    </main>

