
role :web, "events-prod0.headshift.com"                          # Your HTTP server, Apache/etc
role :app, "events-prod0.headshift.com"                          # This may be the same as your `Web` server
role :db,  "events-prod0.headshift.com", :primary => true        # This is where Rails migrations will run

set :application, "wp-hooked.com"
set :branch, 'production'
set :deploy_via, :remote_cache

namespace :deploy do
  desc "Link the config files"
  task :symlink_config do
    run "ln -s #{shared_path}/config/wp-config.php #{release_path}/wordpress/wp-config.php"
  end

  desc "Link the upload dir"
  task :symlink_uploads do
    run "ln -s #{shared_path}/uploads #{release_path}/wordpress/wp-content/uploads"
  end
end

