
role :web, "events-prod0.headshift.com"                          # Your HTTP server, Apache/etc
role :app, "events-prod0.headshift.com"                          # This may be the same as your `Web` server
role :db,  "events-prod0.headshift.com", :primary => true        # This is where Rails migrations will run

set :application, "wp-hooked.com"
set :branch, 'production'
set :deploy_via, :remote_cache


