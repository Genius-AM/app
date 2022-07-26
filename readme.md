## Лесные уголья: Джиппинг
### Вебпак собирает полный css и js для страниц с vuejs
###
###### запуск сборщика вебпак девелоп версии
npm run dev
###### запуск сборщика вебпак продакшн версии
npm run production

**Css это один файл** <br/>
**JS это два файла:** <br/> 
**- один для работы с vuejs - js/app.js**<BR/>
**- другой для работы с jquery - js/scripts_new.min.js**

**p.s. после добавления или изменения параметров в env файле, нужно чистить кэш:**
php artisan config:cache

**после изменений**<br>
composer dump-autoload<br>
php artisan cache:clear<br>
php artisan config:clear<br>
php artisan config:cache<br>
php artisan route:clear<br>
php artisan view:clear<br>

**Swagger generate**<br>
php artisan l5-swagger:generate<br>

**Swagger API**
/api/documentation

**Vuejs компоненты**<br>
https://github.com/isneezy/vue-selectize - vue-selectize

### Установка:

```
composer install
composer run-script post-root-package-install
composer run-script post-create-project-cmd  
php artisan jwt:secret
```

### Установка Docker:

```
docker-compose up -d --build
composer install --ignore-platform-req=ext-zip --no-scripts --prefer-dist
npm i
```
# gelendzik
