#
# Clone the applications git repo into a capistrano type tree
#
include_recipe "users::deploy_user"

# Drop deploy key into user's .ssh directory
cookbook_file "/home/#{node['users']['deploy_user']}/.ssh/id_rsa" do
  source "id_rsa"
  action :create
  owner node['users']['deploy_user']
  group node['apache']['user']
  mode 0600
end

template "#{node[:wp_hooked][:app_installation_base]}/shared/config/database.yml" do
  source "database.yml.erb"
  owner node[:users][:deploy_user]
  group "apache"
  mode 0755
end

# deploy_revision gives us an idempotent way of grabbing the latest revision
# of a branch
deploy_revision node[:wp_hooked][:app_installation_base] do
  repo "git@github.com:headshift/wp-hooked.git"
  branch node[:wp_hooked][:deploy_branch]
  user node[:users][:deploy_user]
  group "apache"
  environment "production"
  shallow_clone true
  action :deploy
  symlinks "config/wp-config.php" => "wordpress/wp-config.php",
           "uploads"        => "wordpress/wp-content/"
end

# Delete the deploy key
file "/home/#{node['users']['deploy_user']}/.ssh/id_rsa" do
  action :delete
end

