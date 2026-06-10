## db_usr_adm - Databases and users simple administration
Упрощённое управление в MySQL базами данных, пользователями и таблицами.

![picture alt](https://github.com/ckpunmkug/release/blob/main/db_usr_adm/Screenshot.png "Main window")

### Назначение
Используется при необходимости создать/удалить пользователя или базу данных, дать/забрать резрешения пользователю, а так же для удаления всех таблиц в базе данных.

### Установка (debian)
```
sudo bash
cd /tmp
wget https://github.com/ckpunmkug/release/raw/refs/heads/main/db_usr_adm/db_usr_adm.run
sh ./db_usr_adm.run
rm ./db_usr_adm.run
```

### Запуск
```
sudo db_usr_adm
```

### Удаление
```
sudo /usr/local/lib/db_usr_adm/uninstall
```

### Функционал
**Lists the databases** - Вывод списка баз данных<br />
**Lists the users** - Вывод списка пользователей<br />
**Lists the tables** - Вывод списка таблиц для базы данных<br />


