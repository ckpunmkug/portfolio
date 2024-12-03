<?php
$about = Data::get_about();
if(!is_array($about)) {
	trigger_error("Can't get 'about' from database", E_USER_WARNING);
	return(false);
}
?>
<main class="section">
    <div class="container">
        <h1 class="title-1"><?= $about["title"]; ?></h1>
        <p style="text-align: center;">
            <img src="assets/img/uploaded/<?= $about["image"]; ?>" style="margin: 10px 15px 20px 10px">
        </p>
        <ul class="content-list">
            <li class="content-list__item">
                <h2 class="title-2"><?= $about["text1"]; ?></h2>
                <p><?= $about["text2"]; ?></p>
            </li>
        </ul>
    </div>
</main>

