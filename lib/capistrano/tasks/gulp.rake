namespace :gulp do
  desc 'build semantic'
  task :build do
    on roles(:app) do
      execute "cd '#{release_path}/src/AppBundle/Resources/public/semantic';npm install gulp;~/.npm-packages/bin/gulp build;"
    end
  end
end