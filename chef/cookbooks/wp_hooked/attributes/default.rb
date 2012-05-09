
default[:wp_hooked][:app_installation_base] = '/srv/html/wp_hooked/'
default[:wp_hooked][:docroot] = '/srv/html/wp_hooked/current/wordpress'

default[:wp_hooked][:web_server_name] = 'wp_hooked.staging.headshift.com'
default[:wp_hooked][:web_server_aliases] = nil

default[:wp_hooked][:db_user] = 'wp_hooked'
default[:wp_hooked][:db_password] = 'unvie32nPN'
default[:wp_hooked][:db_host] = 'mysql-prod0.headshift.com'
default[:wp_hooked][:db_name] = 'wp_hooked_staging0'

default[:wp_hooked][:deploy_branch] = "staging"


default[:wp_hooked][:htpasswd_file] = "/etc/httpd/conf.d/htpasswd-wp_hooked"
default[:wp_hooked][:http_auth_user] = "wp_hooked"
default[:wp_hooked][:http_auth_password] = "afs3glLP"

