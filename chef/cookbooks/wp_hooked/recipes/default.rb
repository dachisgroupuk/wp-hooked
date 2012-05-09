#
# Cookbook Name:: wp_hooked
# Recipe:: default
#
# 
# Copyright 2011, Headshift Limited
#
# All rights reserved - Do Not Redistribute
#

# Code should be owned by the application user
directory "#{node[:wp_hooked][:app_installation_base]}/shared" do
  owner node[:users][:deploy_user]
  group node[:users][:dev_gid]
  mode 0755
  recursive true
end

directory "#{node[:wp_hooked][:app_installation_base]}/shared/config" do
  owner node[:users][:deploy_user]
  group node[:users][:dev_gid]
  mode 0755
  recursive true
end

# And directories for uploading should be owned by the web user,
# we'll keep the group as the application user though so we can diddle
# with the files later if we need to.
directory "#{node[:wp_hooked][:app_installation_base]}/shared/uploads" do
  owner node[:apache][:user]
  group node[:users][:dev_gid]
  mode 0775
  recursive true
end

template "#{node[:wp_hooked][:app_installation_base]}/shared/config/wp-config.php" do
  source "wp-config.php.erb"
  owner node[:users][:deploy_user]
  group node[:users][:dev_gid]
  mode 0755
end

#include_recipe "wp_hooked::gems"
include_recipe "wp_hooked::clone_app"
include_recipe "wp_hooked::configure_apache"



#mysql_database "create rbu database" do
#    host "localhost"
#    username "root"
#    # we shouldn't need a password here, since root has the details in .my.cnf
#    #password node[:mysql][:server_root_password]
#    database node[:wp_hooked][:db_name]
#    action :create_db
#end

# Need to create the app user
#GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES
#    ->   ON rbutrial.*
#    ->   TO 'rbu'@'localhost' identified by 'rbu';
    
# Might be able to do that with a rake task or a drush task.

#template "#{node[:wp_hooked][:app_installation_base]}/shared/config/database.yml" do
#  source "database.yml.erb"
#  owner node[:users][:deploy_user]
#  group node[:users][:dev_gid]
#  mode 0755
#end


# Create one settings file to be used across all releases


# Via the web interface we need to:
#  - choose the open atrium theme
#  - set the base language
#  - ===> create the settings.php file
#  - ===> create the sites/default/files directory (this will need to be shared - NFS?)
