# Symfony console

`bin/console test:specialists:add some@mail.ru` Добавить тестового пользователя some@mail.ru (Должен быть описан в `src/Testing/data/specialists.json`).

`bin/console projects:index` Поисковая индексация всех проектов

`bin/console specialists:index` Поисковая индексация всех специалистов

`bin/console fos:user:promote someuser@mail.com ROLE_ADMIN` - Дать админские права

`bin/console fos:user:demote someuser@mail.com ROLE_ADMIN` - Забрать админские права

# Deployer

`dep test:specialists:add dev --n 5` Добавить тестового пользователя some@mail.ru (Должен быть описан в `src/Testing/data/specialists.json`).

`dep search:projects:index` поисковая индексация всех проектов

`dep search:specialists:index` поисковая индексация всех специалистов

`dep user:make:admin --u someuser@mail.com dev` - Дать админские права

`dep user:remove:admin --u someuser@mail.com dev` - Забрать админские права