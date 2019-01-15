# Symfony console

`bin/console test:projects:add-to-user user@gmail.com 5 Published` Добавить пользователю `user@gmail.com` 5 проектов со статсом `Published`

`bin/console test:specialists:add 5` Добавить 5 тестовых специалистов

`bin/console projects:index` Поисковая индексация всех проектов

`bin/console specialists:index` Поисковая индексация всех специалистов

# Deployer
`dep test:projects:add-to-user dev --u user@gmail.com --n 5` Добавить пользователю `user@gmail.com` 5 проектов со статсом `Published`

`dep test:specialists:add dev --n 5` Добавить 5 тестовых специалистов

`dep search:projects:index` поисковая индексация всех проектов

`dep search:specialists:index` поисковая индексация всех специалистов