require 'capistrano/ext/multistage'
require 'yaml'

set :stages, %w(production)
set :default_stage, 'production'
set :application, "wp-hooked"
set :repository,  "git@github.com:headshift/wp-hooked.git"

set :scm, :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

role :web, "events-prod0.headshift.com"                          # Your HTTP server, Apache/etc
role :app, "events-prod0.headshift.com"                          # This may be the same as your `Web` server
#role :db,  "your primary db-server here", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

set :ssh_options, { :forward_agent => true } # for checking out code from code.headshift.com
set :user,        'dev'
set :group,       'devs'
set :use_sudo,    false
default_run_options[:pty] = true

set :deploy_to, "/srv/html/iview"

after "deploy", 'deploy:symlink_config', 'deploy:symlink_uploads'

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

# if you're still using the script/reaper helper you will need
# these http://github.com/rails/irs_process_scripts

# If you are using Passenger mod_rails uncomment this:
# namespace :deploy do
#   task :start do ; end
#   task :stop do ; end
#   task :restart, :roles => :app, :except => { :no_release => true } do
#     run "#{try_sudo} touch #{File.join(current_path,'tmp','restart.txt')}"
#   end
# end
