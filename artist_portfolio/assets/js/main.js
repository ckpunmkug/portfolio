//
// // 1. Проверка темной темы на уровне системных настроек
// if (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ) {
//     btnDarkMode.classList.add("dark-mode-btn--active");
//     document.body.classList.add("dark");
// }
//
// // 2. Проверка темной темы в localStorage
// if (localStorage.getItem('darkMode') === 'dark') {
//     btnDarkMode.classList.add("dark-mode-btn--active");
//     document.body.classList.add("dark");
// } else if (localStorage.getItem("darkMode") === "light") {
//     btnDarkMode.classList.remove("dark-mode-btn--active");
//     document.body.classList.remove("dark");
// }
//
// // Если меняются системные настройки, меняем тему
// window
//     .matchMedia("(prefers-color-scheme: dark)")
//     .addEventListener("change", (event) => {
//         const newColorScheme = event.matches ? "dark" : "light";
//
//         if (newColorScheme === "dark") {
//             btnDarkMode.classList.add("dark-mode-btn--active");
//             document.body.classList.add("dark");
//             localStorage.setItem("darkMode", "dark");
//         } else {
//             btnDarkMode.classList.remove("dark-mode-btn--active");
//             document.body.classList.remove("dark");
//             localStorage.setItem("darkMode", "light");
//         }
//     });
//
// // Включение ночного режима по кнопке
// btnDarkMode.onclick = function () {
//     btnDarkMode.classList.toggle("dark-mode-btn--active");
//     const isDark = document.body.classList.toggle("dark");
//
//     if (isDark) {
//         localStorage.setItem("darkMode", "dark");
//     } else {
//         localStorage.setItem("darkMode", "light");
//     }
// };
//
//
// Включение ночного режима по кнопке

window.addEventListener("load", function() {

	const btnDarkMode = document.querySelector(".dark-mode-btn");
	
	btnDarkMode.addEventListener("click", function() {
	    btnDarkMode.classList.toggle("dark-mode-btn--active");
	    const isDark = document.body.classList.toggle("dark");

	    if (isDark) {
		document.cookie = "darkMode=dark; path=/";
	    } else {
		document.cookie = "darkMode=light; path=/";
	    }
	});
	
	// Проверка темной темы в куки
	let cookies = document.cookie.split("; ");
	let darkModeCookie = cookies.find(cookie => cookie.startsWith("darkMode="));

	if (darkModeCookie) {
	    let darkMode = darkModeCookie.split("=")[1];
	    if (darkMode === 'dark') {
		btnDarkMode.classList.add("dark-mode-btn--active");
		document.body.classList.add("dark");
	    } else if (darkMode === "light") {
		btnDarkMode.classList.remove("dark-mode-btn--active");
		document.body.classList.remove("dark");
	    }
	}
});

function clearCookies() {
    let cookies = document.cookie.split("; ");

    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        let eqPos = cookie.indexOf("=");
        let name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }

    // Установите светлую тему после очистки куки
    btnDarkMode.classList.remove("dark-mode-btn--active");
    document.body.classList.remove("dark");
}

