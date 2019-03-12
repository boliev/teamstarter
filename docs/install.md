# Установка проекта
## Чат
### Локально
Установка не требуется, все сделает докер
### Сервер
**Настройки nginx**
```
location /socket.io/ {
    proxy_pass http://test.teamstarter.ru:8081;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
}
```
**Настройки в env.local**
```
WEBSOCKET_URL_FOR_SERVER=localhost
WEBSOCKET_PORT_FOR_SERVER=8081
WEBSOCKET_URL_FOR_CLIENT=https://test.teamstarter.ru
WEBSOCKET_PORT_FOR_CLIENT=443
```

**Запуск процесса**
socket.io лежит в отдельной директории, т.к. почти никогда не изменяется
```
cd /var/www/ts-socket-server/teamstarter/socket-server/
npm install
pm2 start index.js
```