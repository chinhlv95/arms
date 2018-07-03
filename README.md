To setup project. Please follow this steps
1. サーバにLDAP拡張がある必要です。
2. git pull origin develop
3. cp .env.example .env
4. .envファイル設定 
6. Laravelの通りデータベース情報を設定する
7. メールサーバ情報を設定する
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=53767501591958
MAIL_PASSWORD=f5d199111e5c6b
MAIL_ENCRYPTION=null
5. php artisan key:generate
6. composer update or composer install
7. npm install
8. php artisan migrate
9. 初期データを設定する
    php artisan db:seed --class=UsersTableSeeder
    php artisan db:seed --class=RoleTableSeeder
10. データを読み込む
    php artisan synch:members
    php artisan synch:departments
    php artisan synch:projects