namespace :gulp do
  desc 'build semantic'
  task :build do
    on roles(:app) do
      execute "cd '#{release_path}/src/AppBundle/Resources/public/semantic';npm install --no-interaction;~/.npm-packages/bin/gulp build;"
      execute "cd '#{release_path}/src/AppBundle/Resources/public';compass compile"
      execute "cd '#{release_path}/src/AppBundle/Resources/public';/home/amir/.npm-packages/bin/bower install"
      execute "cd '#{release_path}';/usr/bin/php bin/console assets:install"
      execute "cd '#{release_path}';/usr/bin/php bin/console assetic:dump --env=prod"
      execute "cd '#{release_path}';/usr/bin/php bin/console assetic:dump --env=dev"
      execute "cd '#{release_path}';/usr/bin/php bin/console doctrine:schema:update --force"
      execute "cd '#{release_path}';/usr/bin/php bin/console cache:clear --env=prod"
      execute "cd '#{release_path}';/usr/bin/php bin/console cache:clear --env=dev"
      execute "cd '#{release_path}/rethink';npm install --no-interaction"
    end
  end
end