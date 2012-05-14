
if node[:wp_hooked][:deploy_branch] == "staging"
  bash "Create HTTP auth credentials" do
    code "echo /usr/bin/htpasswd -bc #{node[:wp_hooked][:htpasswd_file]} #{node[:wp_hooked][:http_auth_user]} #{node[:wp_hooked][:http_auth_password]} ; /usr/bin/htpasswd -bc #{node[:wp_hooked][:htpasswd_file]} #{node[:wp_hooked][:http_auth_user]} #{node[:wp_hooked][:http_auth_password]}"
    not_if do
      File.exists?("#{node[:wp_hooked][:htpasswd_file]}")
    end
  end
end

web_app "wp_hooked" do 
  docroot node[:wp_hooked][:docroot]
  server_name node[:wp_hooked][:web_server_name]
end

apache_site "wp_hooked" do
  action :enable
end

