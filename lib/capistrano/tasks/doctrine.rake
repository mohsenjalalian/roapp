namespace :doctrine do

  task :rollback do
    on roles(:app) do
      symfony_console('doctrine:migrations:migrate', 'first --no-interaction')
    end
  end

  task :migrate do
    on roles(:app) do
      symfony_console('doctrine:migrations:migrate', '--no-interaction')
    end
  end

  namespace :fixtures do
    task :load do
      on roles(:app) do
        symfony_console('doctrine:fixtures:load', '--no-interaction')
      end
    end
  end
end