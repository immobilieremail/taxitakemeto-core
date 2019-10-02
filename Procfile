postdeploy: php artisan migrate && curl https://api.rollbar.com/api/1/deploy/ -F access_token=$ROLLBAR_TOKEN -F environment=$APP_ENV -F revision=$CONTAINER_VERSION -F local_username=scalingo
