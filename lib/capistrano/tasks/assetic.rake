namespace :assetic do

  desc 'Dump assetic'
  task :dump do
    on roles(:app) do
      symfony_console('assetic:dump', '--env=dev')
    end
  end
end