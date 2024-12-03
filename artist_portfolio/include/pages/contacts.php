<?php
$contacts = Data::get_contacts();
if(!is_array($contacts)) {
	trigger_error("Can't get contacts from database", E_USER_ERROR);
	exit(255);
}
?>    
    <main class="section">
        <div class="container">
            <h1 class="title-1">Contacts</h1>

            <ul class="content-list">
                <li class="content-list__item">
                    <h2 class="title-2">VK</h2>
                    <p><a href=""><?= $contacts["vk"]; ?></a></p>
                </li>
                <li class="content-list__item">
                    <h2 class="title-2">Telegram</h2>
                    <p><a href=""><?= $contacts["telegram"]; ?></a></p>
                </li>
                <li class="content-list__item">
                    <h2 class="title-2">Email</h2>
                    <p><a href=""><?= $contacts["email"]; ?></a></p>
                </li>
            </ul>

        </div>
    </main>

