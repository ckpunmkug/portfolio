<?php
if(!(@is_string($_GET['id']) && ctype_digit($_GET['id']))) {
	trigger_error("id not set", E_USER_ERROR);
	exit(255);
}

$project = Data::select_project(intval($_GET['id']));
if(!is_array($project)) {
	trigger_error("Can't get project from database", E_USER_WARNING);
	return(false);
}

$html = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
   <main class="section">
        <div class="container">

            <div class="project-details">

            <h1 class="title-1">{$project["title"]}</h1>

                <img src="assets/img/uploaded/{$project["image"]}" alt="" class="project__img">


                <div class="project-details__desc">
                    <p>{$project["text1"]}</p>
                </div>

                <div class="project-details__desc">
                    <p>{$project["text2"]}</p>
                </div>


            </div>
        </div>
    </main>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

echo($html);

